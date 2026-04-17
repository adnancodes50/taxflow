@extends('welcome')

@section('content')
    @php
        $classifiedResults = is_string($report->classified_results)
            ? json_decode($report->classified_results, true)
            : $report->classified_results;

        $categories = $classifiedResults['categories'] ?? [];

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

        $totalIncome = 0;
        $totalExpenses = 0;

        foreach ($categories as $cat) {
            if ($cat['name'] === 'Gross Profit and Income') {
                $totalIncome += abs($cat['total']);
            } else {
                $totalExpenses += abs($cat['total']);
            }
        }

        $netIncome = $totalIncome - $totalExpenses;
    @endphp

    <div  class="mt-5 bg-black min-h-screen  " style="padding-left: 15%; padding-right: 15%;">
        <!-- Header with Business Name -->
        <div class="mb-8">
            <h2 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                {{ $report->business_name }}
            </h2>
            <div class="h-1 w-20 bg-[#6a4dff] rounded-full mt-2"></div>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">
            <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-xl p-5 hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-gray-400 text-sm uppercase tracking-wide">Total Income</p>
                <h3 class="text-green-400 text-2xl md:text-3xl font-bold mt-2">${{ number_format($totalIncome, 2) }}</h3>
                <div class="mt-2 text-xs text-gray-500">+0.00% from last period</div>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-xl p-5 hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-gray-400 text-sm uppercase tracking-wide">Total Expenses</p>
                <h3 class="text-red-400 text-2xl md:text-3xl font-bold mt-2">${{ number_format($totalExpenses, 2) }}</h3>
                <div class="mt-2 text-xs text-gray-500">Track all business costs</div>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-xl p-5 hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-gray-400 text-sm uppercase tracking-wide">Net Income</p>
                <h3 class="text-blue-400 text-2xl md:text-3xl font-bold mt-2">${{ number_format($netIncome, 2) }}</h3>
                <div class="mt-2 text-xs {{ $netIncome >= 0 ? 'text-green-500' : 'text-red-500' }}">
                    {{ $netIncome >= 0 ? '↑ Profitable' : '↓ Loss' }}
                </div>
            </div>
        </div>

        <!-- Download Button -->
        <div class="flex justify-end mb-8">
            <a href="{{ route('report.download', $upload->id) }}"
                class="w-full sm:w-auto bg-[#6a4dff] hover:bg-[#5a3de5] text-white font-semibold py-3 px-8 rounded-xl shadow-lg shadow-[#6a4dff]/20 transition-all duration-300 flex items-center justify-center gap-3 text-sm uppercase tracking-wider hover:scale-105 hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download PDF
            </a>
        </div>

        <!-- CATEGORIES LIST -->
        <div class="space-y-4">
            @foreach ($categories as $index => $cat)
                @if (($cat['count'] ?? 0) > 0)
                    <div class="bg-[#0a0a0a] rounded-xl border border-[#1a1a1a] overflow-hidden hover:border-[#2a2a2a] transition-all duration-300">
                        <!-- HEADER (Click to toggle) -->
                        <div class="p-5 cursor-pointer category-toggle flex justify-between items-center hover:bg-[#111] transition-colors duration-200"
                             data-category="{{ $index }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-200 category-arrow-{{ $index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <div class="w-2 h-2 rounded-full {{ $cat['name'] === 'Gross Profit and Income' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                <div>
                                    <h3 class="font-semibold text-lg text-white">{{ $cat['name'] }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">{{ $cat['count'] }} transactions</p>
                                </div>
                            </div>

                            @php $isIncome = $cat['name'] === 'Gross Profit and Income'; @endphp

                            <div class="text-right">
                                <div class="font-bold text-lg text-white">${{ number_format(abs($cat['total']), 2) }}</div>
                                <div class="text-xs {{ $isIncome ? 'text-green-500' : 'text-red-500' }} font-medium">
                                    {{ $isIncome ? 'Income' : 'Expense' }}
                                </div>
                            </div>
                        </div>

                        <!-- CONTENT (Merchants) - Hidden by default, shows when category is clicked -->
                        <div class="hidden category-content-{{ $index }} border-t border-[#1a1a1a] bg-[#050505]">
                            @foreach ($cat['merchants'] ?? [] as $merchant)
                                @if (($merchant['transaction_count'] ?? 0) > 0)
                                    <div class="border-b border-[#111] hover:bg-[#0c0c0c] transition-colors duration-200">
                                        <!-- MERCHANT ROW -->
                                        <div class="flex justify-between items-center p-4 pl-12">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox"
                                                       class="merchant-checkbox w-4 h-4 rounded border-gray-600 bg-transparent text-[#6a4dff] focus:ring-[#6a4dff] focus:ring-offset-0"
                                                       data-merchant="{{ $merchant['merchant'] }}">
                                                <div>
                                                    <h4 class="font-medium text-gray-200">{{ $merchant['merchant'] }}</h4>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ $merchant['transaction_count'] }} transactions
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="font-semibold {{ $merchant['total_amount'] > 0 ? 'text-green-400' : 'text-red-400' }}">
                                                ${{ number_format(abs($merchant['total_amount']), 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Empty State (if no categories) -->
        @if(count($categories) === 0)
            <div class="text-center py-16 bg-[#0a0a0a] rounded-xl border border-[#1a1a1a]">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-400">No classified transactions found.</p>
                <p class="text-gray-500 text-sm mt-1">Upload a bank statement to get started.</p>
            </div>
        @endif
    </div>

    <!-- BULK ACTION BAR -->
    <div id="bulkBar"
        class="hidden fixed bottom-0 left-0 w-full bg-[#0a0a0a] border-t border-[#1a1a1a] py-4 px-4 flex justify-center items-center z-50 shadow-2xl">
        <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
            <span id="selectedCount" class="text-white text-sm font-medium">0 selected</span>

            <select id="categoryDropdown"
                class="bg-black text-white border border-gray-700 px-4 py-2 rounded-lg focus:outline-none focus:border-[#6a4dff] text-sm">
                <option value="" class="bg-black text-gray-400">Select Category</option>
                @foreach ($scheduleC as $cat)
                    <option value="{{ $cat }}" class="bg-black text-white">
                        {{ $cat }}
                    </option>
                @endforeach
            </select>

            <button id="applyBtn"
                class="bg-[#6a4dff] hover:bg-[#5a3de5] text-white px-6 py-2 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105">
                Apply to selected
            </button>
        </div>
    </div>

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0a0a0a;
        }
        ::-webkit-scrollbar-thumb {
            background: #2a2a2a;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3a3a3a;
        }

        /* Checkbox styling */
        .merchant-checkbox {
            accent-color: #6a4dff;
            cursor: pointer;
        }

        /* Category dropdown in dark mode */
        #categoryDropdown {
            background-color: #000000 !important;
            color: #ffffff !important;
        }
        #categoryDropdown option {
            background-color: #000000 !important;
            color: #ffffff !important;
        }
        #categoryDropdown option:first-child {
            color: #9ca3af !important;
        }

        /* Rotate arrow when open */
        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>

    <script>
        // Category Toggle - Show/Hide merchants inside category
        document.querySelectorAll('.category-toggle').forEach(el => {
            const categoryIndex = el.dataset.category;
            const content = document.querySelector(`.category-content-${categoryIndex}`);
            const arrow = document.querySelector(`.category-arrow-${categoryIndex}`);

            el.onclick = (e) => {
                // Don't toggle if clicking on checkbox inside header (but there is none)
                e.stopPropagation();
                content.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            };
        });

        // Bulk Action Apply
        document.getElementById('applyBtn').addEventListener('click', () => {
            const selected = [];
            const checkboxes = document.querySelectorAll('.merchant-checkbox:checked');

            checkboxes.forEach(cb => {
                selected.push(cb.dataset.merchant);
            });

            const category = document.getElementById('categoryDropdown').value;

            if (selected.length === 0 || !category) {
                alert('Please select at least one merchant and a category');
                return;
            }

            fetch('/update-classification/{{ $report->id }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    merchants: selected,
                    category: category
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating classification');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Something went wrong');
            });
        });

        // Checkbox Selection Logic
        const checkboxes = document.querySelectorAll('.merchant-checkbox');
        const bulkBar = document.getElementById('bulkBar');
        const selectedCountSpan = document.getElementById('selectedCount');

        function updateBulkBar() {
            const checked = document.querySelectorAll('.merchant-checkbox:checked');
            const count = checked.length;

            if (count > 0) {
                bulkBar.classList.remove('hidden');
            } else {
                bulkBar.classList.add('hidden');
            }

            selectedCountSpan.innerText = count + ' selected';
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        // Initialize on page load
        updateBulkBar();
    </script>
@endsection
