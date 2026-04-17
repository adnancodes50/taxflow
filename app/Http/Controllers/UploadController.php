<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Setting;
use App\Models\Upload;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'statement' => 'required|file|mimes:pdf,txt|max:10240',
        ]);

        $file = $request->file('statement');
        $path = $file->store('uploads', 'local');

        Log::info('📁 STORED FILE: '.$path);

        $upload = Upload::create([
            'user_id' => Auth::id(),
            'file_name' => $file->getClientOriginalName(),
            'file_buffer' => $path,
            'file_mime_type' => $file->getMimeType(),
            'is_guest' => ! Auth::check(),
        ]);

        session(['pending_upload_id' => $upload->id]);

        return redirect()->route('analyze', $upload->id);
    }

    public function analyze($id)
    {
        return view('frontend.analyze', [
            'uploadId' => $id,
            'isLoggedIn' => Auth::check(),
        ]);
    }

    public function processAnalysis($uploadId)
    {
        try {

            Log::info('🚀 START PROCESS');

$upload = Upload::findOrFail($uploadId);

// ✅ CHECK FILE EXISTS
if (! Storage::disk('local')->exists($upload->file_buffer)) {
    return;
}

// ✅ GET FILE PATH
$filePath = Storage::disk('local')->path($upload->file_buffer);

// ✅ PARSE PDF ONCE
$parser = new Parser();
$pdf = $parser->parseFile($filePath);

// ✅ EXTRACT DATA
$pageCount = count($pdf->getPages());
$rawText = $pdf->getText();

// ✅ CREATE REPORT
$report = Report::create([
    'user_id' => auth()->id(),
    'upload_id' => $upload->id,
    'file_name' => $upload->file_name,
    'business_name' => 'Processing...',
    'status' => 'analyzing',
    'payment_status' => 'pending',
    'page_count' => $pageCount,
]);

response()->json(['success' => true])->send();

ignore_user_abort(true);
set_time_limit(300);

$settings = Setting::first();

// ✅ CALL AI
$result = $this->callAI($rawText, $settings, $report);

            if (! $result) {
                // ⚠️ DO NOT mark failed yet (background running)
                return;
            }

            // If immediate result (rare)
            $report->update([
                'status' => 'completed',
                'business_name' => $result['businessName'] ?? 'Business',
                'date_range' => $result['dateRange'] ?? '',
                'income' => $result['summary']['totalIncome'] ?? 0,
                'expenses' => $result['summary']['totalExpenses'] ?? 0,
                'net_income' => $result['summary']['netIncome'] ?? 0,
                'analysis_results' => $result,
            ]);

            Log::info('✅ DONE');

        } catch (\Exception $e) {
            Log::error('❌ ERROR: '.$e->getMessage());
        }
    }

    /**
     * 🔥 UPDATED AI CALL (BACKGROUND MODE)
     */
    private function callAI($text, $settings, $report)
    {
        try {

            $prompt = <<<PROMPT
You MUST extract EVERY transaction from the provided text.

STRICT RULES:
- Do NOT skip any transaction
- Do NOT summarize
- Do NOT group transactions
- Each line/row = ONE transaction
- Preserve original merchant names EXACTLY as written
- Preserve exact amounts (including + or -)
- If unsure, still include the transaction
- Total number of transactions MUST match the text
IF expense:
→ MUST be assigned to ANY valid expense category
→ NEVER fallback directly to "Other Business Expenses"

CLASSIFICATION RULES:
- Positive amount (+) = "income"
- Negative amount (-) = "expense"

ACCURACY RULE:
- Accuracy = (number of extracted transactions / number of detected transactions in text) * 100
- Return accuracy as percentage string (e.g. "92%")

RETURN STRICT JSON ONLY (NO TEXT, NO MARKDOWN):

{
  "businessName": "",
  "accuracy": "",
  "dateRange": "",
  "summary": {
    "totalIncome": 0,
    "totalExpenses": 0,
    "netIncome": 0
  },
  "transactions": [
    {
      "date": "",
      "name": "",
      "amount": 0,
      "status": "",
      "type": "income"
    }
  ]
}

TEXT:
{$text}
PROMPT;
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 300,
            ])
                ->withHeaders([
                    'Authorization' => 'Bearer '.$settings->ai_key,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/responses', [
                    'model' => 'gpt-4.1',
                    'input' => $prompt,
                    'background' => true, // ✅ MAIN CHANGE
                ]);

            if (! $response->successful()) {
                Log::error('❌ API ERROR: '.$response->body());
                $report->update(['status' => 'failed']);

                return null;
            }

            $data = $response->json();

            // ✅ STORE BACKGROUND JOB ID
            $report->update([
                'openai_id' => $data['id'],
                'status' => 'analyzing', // ensure status is set to analyzing
            ]);

            Log::info('🧠 OpenAI Background Job ID: '.$data['id']);

            return null; // ⛔ no immediate result

        } catch (\Exception $e) {
            Log::error('❌ AI ERROR: '.$e->getMessage());
            $report->update(['status' => 'failed']);

            return null;
        }
    }

    public static function attachUploadToUser()
    {
        if (session()->has('pending_upload_id') && auth()->check()) {

            $uploadId = session('pending_upload_id');

            $upload = Upload::find($uploadId);

            if ($upload) {
                $upload->update([
                    'user_id' => auth()->id(),
                    'is_guest' => false,
                ]);

                Report::where('upload_id', $uploadId)->update([
                    'user_id' => auth()->id(),
                ]);

                Log::info('✅ Upload + Report linked to user: '.$uploadId);
            }

            session()->forget('pending_upload_id');
        }
    }

    public function fetchResult($reportId)
    {
        try {

            $report = Report::find($reportId);

            if (! $report || ! $report->openai_id) {
                return response()->json(['status' => 'no_job']);
            }

            $settings = Setting::first();

            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 300,
            ])
                ->withHeaders([
                    'Authorization' => 'Bearer '.$settings->ai_key,
                ])
                ->get('https://api.openai.com/v1/responses/'.$report->openai_id);

            if (! $response->successful()) {
                Log::error('❌ FETCH ERROR: '.$response->body());

                return response()->json(['status' => 'error']);
            }

            $data = $response->json();

            // ⏳ STILL PROCESSING
            if (($data['status'] ?? '') !== 'completed') {
                return response()->json(['status' => 'processing']);
            }

            // ✅ GET TEXT OUTPUT
            $raw = $data['output'][0]['content'][0]['text'] ?? '';

            if (! $raw) {
                Log::error('❌ EMPTY RESULT');

                return response()->json(['status' => 'error']);
            }

            $raw = str_replace(['```json', '```'], '', $raw);

            // ✅ STEP 1: DECODE FIRST AI RESULT
            $result = json_decode(trim($raw), true);

            Log::info('✅ FIRST AI PARSED. TX COUNT: '.count($result['transactions']));

            // 🔥 STEP 2: SECOND AI CALL (CLASSIFICATION)

            $classified = $this->classifyTransactions(json_encode($result));
            if (! $classified) {

                return response()->json(['status' => 'processing']);
            }

            // ✅ STEP 3: FINAL SAVE (ONLY NOW COMPLETE)
            $report->update([
                'status' => 'completed',
                'business_name' => $result['businessName'] ?? 'Business',
                'date_range' => $result['dateRange'] ?? '',
                'income' => $result['summary']['totalIncome'] ?? 0,
                'expenses' => $result['summary']['totalExpenses'] ?? 0,
                'net_income' => $result['summary']['netIncome'] ?? 0,
                'analysis_results' => $result,
                'classified_results' => $classified,
            ]);

            return response()->json(['status' => 'completed']);
            // return response()->json(['status' => 'completed']);
        } catch (\Exception $e) {
            Log::error('❌ FETCH EXCEPTION: '.$e->getMessage());

            return response()->json(['status' => 'error']);
        }
    }



public function lock($reportId)
{
    // ✅ Get report
    $report = Report::findOrFail($reportId);

    // ✅ Get settings
    $settings = Setting::first();

    // ✅ Price per page
    $pricePerPage = $settings->per_page_price ?? 0;

    // ✅ Total pages (safe fallback)
    $pages = $report->page_count ?? 0;

    // ✅ Total price
    $totalPrice = $pages * $pricePerPage;

    return view('frontend.lock', compact(
        'report',
        'pricePerPage',
        'totalPrice'
    ));
}

    private function classifyTransactions($transactions)
    {
        try {

            Log::info('🧠 AI CALL #2 START');

            $settings = Setting::first();

            $prompt = <<<PROMPT
ROLE:
You are TaxFlowPro AI.

OBJECTIVE:
Classify EACH transaction into Schedule C category.

IMPORTANT RULES:
- DO NOT group transactions
- DO NOT aggregate
- DO NOT merge merchants
- RETURN ALL transactions individually
- TOTAL transaction count MUST remain EXACTLY same
- IF amount > 0 → "Gross Profit and Income"
- IF amount < 0 → MUST assign to ONE of the provided expense categories
- NEVER use "Other Business Expenses"
- NEVER leave category empty
- ALWAYS pick BEST matching category

--------------------------------------------------

SCHEDULE C CATEGORIES:

Advertising
Car and Truck Expenses
Commissions and Fees
Contract Labor
Depletion
Depreciation and Section 179 Expense
Employee Benefit Programs
Insurance (other than health)
Interest – Mortgage
Interest – Other
Legal and Professional Services
Office Expense
Pension and Profit Sharing Plans
Rent or Lease – Vehicles, Machinery, Equipment
Rent or Lease – Other Business Property
Repairs and Maintenance
Supplies (not included in Cost of Goods Sold)
Taxes and Licenses
Travel
Meals
Utilities
Wages
Gross Profit and Income

--------------------------------------------------

CLASSIFICATION RULES:

- IF amount > 0 → "Gross Profit and Income"
- ELSE classify based on merchant meaning
- DO NOT skip any transaction
- DO NOT change amounts or names

--------------------------------------------------

OUTPUT:

{
  "transactions": [
    {
      "id": 1,
      "date": "",
      "name": "",
      "amount": 0,
      "type": "income",
      "category": "Office Expense"
    }
  ]
}

--------------------------------------------------

DATA:
{$transactions}
PROMPT;

            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 300,
            ])->withHeaders([
                'Authorization' => 'Bearer '.$settings->ai_key,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/responses', [
                'model' => 'gpt-4.1',
                'input' => $prompt,
            ]);

            if (! $response->successful()) {
                Log::error('❌ AI ERROR: '.$response->body());

                return null;
            }

            $data = $response->json();
            $raw = $data['output'][0]['content'][0]['text'] ?? '';

            if (! $raw) {
                Log::error('❌ EMPTY RESPONSE');

                return null;
            }

            Log::info('📥 RAW CLASSIFY RESPONSE: '.substr($raw, 0, 500));

            $raw = str_replace(['```json', '```'], '', $raw);
            $result = json_decode(trim($raw), true);

            if (! $result || ! isset($result['transactions'])) {
                return null;
            }

            $transactions = $result['transactions'];

            // ✅ Schedule C Order
            $scheduleC = [
                'Advertising',
                'Car and Truck Expenses',
                'Commissions and Fees',
                'Contract Labor',
                'Depletion',
                'Depreciation and Section 179 Expense',
                'Employee Benefit Programs',
                'Insurance (other than health)',
                'Interest – Mortgage',
                'Interest – Other',
                'Legal and Professional Services',
                'Office Expense',
                'Pension and Profit Sharing Plans',
                'Rent or Lease – Vehicles, Machinery, Equipment',
                'Rent or Lease – Other Business Property',
                'Repairs and Maintenance',
                'Supplies (not included in Cost of Goods Sold)',
                'Taxes and Licenses',
                'Travel',
                'Meals',
                'Utilities',
                'Wages',
                'Gross Profit and Income',
            ];

            $categories = [];

            // Initialize categories
            foreach ($scheduleC as $cat) {
                $categories[$cat] = [
                    'name' => $cat,
                    'total' => 0,
                    'count' => 0,
                    'merchants' => [],
                ];
            }

            // Process transactions
            foreach ($transactions as $tx) {

                $cat = $tx['category'] ?? null;

                // 🔥 FORCE CATEGORY RULE
                if ($tx['amount'] > 0) {

                    // income always fixed
                    $cat = 'Gross Profit and Income';

                } else {

                    // expense → must be valid Schedule C category
                    if (! $cat || ! isset($categories[$cat])) {

                        // 🔥 FORCE DEFAULT EXPENSE CATEGORY
                        $cat = 'Office Expense';
                    }
                }

                // Normalize merchant safely
                $merchant = strtoupper($tx['name']);

                if (str_contains($merchant, 'STRIPE')) {
                    $merchant = 'STRIPE';
                }
                if (str_contains($merchant, 'AFFINIPAY')) {
                    $merchant = 'AFFINIPAY';
                }
                if (str_contains($merchant, 'IRS')) {
                    $merchant = 'IRS';
                }

                // Category totals
                $categories[$cat]['total'] += abs($tx['amount']);
                $categories[$cat]['count']++;

                // Merchant grouping
                if (! isset($categories[$cat]['merchants'][$merchant])) {
                    $categories[$cat]['merchants'][$merchant] = [
                        'merchant' => $merchant,
                        'total_amount' => 0,
                        'transaction_count' => 0,
                        'transactions' => [],
                    ];
                }

                $categories[$cat]['merchants'][$merchant]['total_amount'] += $tx['amount'];
                $categories[$cat]['merchants'][$merchant]['transaction_count']++;

                $categories[$cat]['merchants'][$merchant]['transactions'][] = [
                    'name' => $tx['name'],
                    'amount' => $tx['amount'],
                    'date' => $tx['date'] ?? null,
                ];
            }

            // Convert merchant objects to arrays
            foreach ($categories as &$cat) {
                $cat['merchants'] = array_values($cat['merchants']);
            }
            unset($cat);

            // Income summary
            $income = collect($transactions)->where('type', 'income');

            $final = [
                'income_summary' => [
                    'total' => $income->sum('amount'),
                    'count' => $income->count(),
                ],
                'categories' => array_values($categories),
            ];

            Log::info('✅ CLASSIFICATION COMPLETE');

            return $final;

        } catch (\Exception $e) {
            Log::error('❌ CLASSIFY ERROR: '.$e->getMessage());

            return null;
        }
    }

    public function getReport($id)
    {
        $upload = Upload::with('report')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $report = $upload->report;

        // Decode JSON safely
        $classifiedResults = is_string($report->classified_results)
            ? json_decode($report->classified_results, true)
            : $report->classified_results;

        $categories = $classifiedResults['categories'] ?? [];

        // Schedule C Order
        $scheduleC = [
            'Advertising',
            'Car and Truck Expenses',
            'Commissions and Fees',
            'Contract Labor',
            'Depletion',
            'Depreciation and Section 179 Expense',
            'Employee Benefit Programs',
            'Insurance (other than health)',
            'Interest – Mortgage',
            'Interest – Other',
            'Legal and Professional Services',
            'Office Expense',
            'Pension and Profit Sharing Plans',
            'Rent or Lease – Vehicles, Machinery, Equipment',
            'Rent or Lease – Other Business Property',
            'Repairs and Maintenance',
            'Supplies (not included in Cost of Goods Sold)',
            'Taxes and Licenses',
            'Travel',
            'Meals',
            'Utilities',
            'Wages',
            'Gross Profit and Income',
        ];

        // Sort categories
        $sorted = [];

        foreach ($scheduleC as $catName) {
            foreach ($categories as $cat) {
                if ($cat['name'] === $catName) {
                    $sorted[] = $cat;
                    break;
                }
            }
        }

        // Add any missing categories
        foreach ($categories as $cat) {
            if (! in_array($cat['name'], $scheduleC)) {
                $sorted[] = $cat;
            }
        }

        return view('frontend.report', compact(
            'upload',
            'report',
            'sorted',
            'classifiedResults'
        ));
    }

    public function downloadReport($id)
    {
        $upload = Upload::with('report')->findOrFail($id);

        $report = $upload->report;

        if (! $report) {
            abort(404, 'Report not found');
        }

        // ✅ FIX: decode JSON safely
        $data = is_string($report->classified_results)
            ? json_decode($report->classified_results, true)
            : $report->classified_results;

        $categories = $data['categories'] ?? [];

        $totalIncome = 0;
        $totalExpenses = 0;
        $groupedCategories = [];

        foreach ($categories as $cat) {

            $total = (float) ($cat['total'] ?? 0);

            if ($cat['name'] === 'Gross Profit and Income') {
                $totalIncome += $total;
            } else {
                $totalExpenses += $total;
            }

            // 🔥 determine real signed total
            if ($cat['name'] === 'Gross Profit and Income') {
                $groupedCategories[$cat['name']] = $total; // positive
            } else {
                $groupedCategories[$cat['name']] = -$total; // negative
            }
        }

        $netIncome = $totalIncome - $totalExpenses;

        $pdf = Pdf::loadView('pdf.report', compact(
            'upload',
            'report',
            'groupedCategories',
            'totalIncome',
            'totalExpenses',
            'netIncome'
        ));

        return $pdf->download('report.pdf');
    }

    public function updateClassification(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $data = is_string($report->classified_results)
            ? json_decode($report->classified_results, true)
            : $report->classified_results;

        $selectedMerchants = $request->merchants;
        $newCategory = $request->category;

        $allTransactions = [];

        // 🔥 STEP 1: FLATTEN ALL TRANSACTIONS
        foreach ($data['categories'] as $cat) {
            foreach ($cat['merchants'] ?? [] as $merchant) {
                foreach ($merchant['transactions'] ?? [] as $tx) {
                    $tx['category'] = $cat['name'];
                    $tx['merchant_clean'] = strtoupper($merchant['merchant']);
                    $allTransactions[] = $tx;
                }
            }
        }

        // 🔥 STEP 2: UPDATE CATEGORY FOR SELECTED MERCHANTS
        foreach ($allTransactions as &$tx) {

            if (in_array($tx['merchant_clean'], $selectedMerchants)) {

                $tx['category'] = $newCategory;

                // 🔥 FIX TYPE + AMOUNT SIGN
                if ($newCategory === 'Gross Profit and Income') {

                    // force income
                    $tx['type'] = 'income';
                    $tx['amount'] = abs($tx['amount']);

                } else {

                    // force expense
                    $tx['type'] = 'expense';
                    $tx['amount'] = -abs($tx['amount']);
                }
            }
        }

        // 🔥 STEP 3: REBUILD FULL STRUCTURE (CRITICAL)
        $categories = [];

        foreach ($allTransactions as $tx) {

            $cat = $tx['category'];

            if (! isset($categories[$cat])) {
                $categories[$cat] = [
                    'name' => $cat,
                    'total' => 0,
                    'count' => 0,
                    'merchants' => [],
                ];
            }

            $merchant = $tx['merchant_clean'];

            if (! isset($categories[$cat]['merchants'][$merchant])) {
                $categories[$cat]['merchants'][$merchant] = [
                    'merchant' => $merchant,
                    'total_amount' => 0,
                    'transaction_count' => 0,
                    'transactions' => [],
                ];
            }

            $amount = $tx['amount'];

            $categories[$cat]['total'] += abs($amount);
            $categories[$cat]['count']++;

            $categories[$cat]['merchants'][$merchant]['total_amount'] += $amount;
            $categories[$cat]['merchants'][$merchant]['transaction_count']++;

            $categories[$cat]['merchants'][$merchant]['transactions'][] = $tx;
        }

        // 🔥 CLEAN FORMAT
        foreach ($categories as &$cat) {
            $cat['merchants'] = array_values($cat['merchants']);
        }

        $data['categories'] = array_values($categories);

        // 🔥 SAVE
        $report->classified_results = json_encode($data);
        $report->save();

        return response()->json(['success' => true]);
    }
}
