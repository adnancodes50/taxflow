@extends('welcome')

@section('content')

<div class="flex flex-col items-center justify-center min-h-[70vh] px-4">

    <div class="w-full max-w-[480px] bg-[#111111] border border-[#222222] rounded-3xl p-5 sm:p-6 mt-4 shadow-2xl text-center">

        {{-- ICON --}}
        <div class="size-16 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-5 border border-emerald-500/20">
            <i class="fa-solid fa-lock-open text-3xl"></i>
        </div>

        {{-- TITLE --}}
        <h2 class="text-xl font-black text-white mb-3 uppercase tracking-tight">
            Your Report is Ready
        </h2>

        <p class="text-slate-500 text-sm mb-6 leading-relaxed font-medium">
            Your financial statement has been analyzed and categorized.
            Complete payment to unlock full access and download your report.
        </p>

        {{-- PRICING --}}
        <div class="bg-white/[0.02] border border-white/5 rounded-2xl p-5 mb-6 text-left">

            <div class="flex justify-between items-center mb-3">
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">
                    Pricing
                </span>

                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest px-2 py-1 bg-emerald-400/10 rounded-lg">
                    Per Page
                </span>
            </div>

            <div class="flex justify-between items-end">
                <div>
                    {{-- TOTAL --}}
                    <p class="text-white font-black text-2xl">
                        ${{ number_format($totalPrice, 2) }}
                    </p>

                    {{-- DETAILS --}}
                    <p class="text-slate-500 text-[10px] mt-1 uppercase font-bold tracking-widest">
                        ${{ number_format($pricePerPage, 2) }} × {{ $report->page_count ?? 0 }} pages
                    </p>
                </div>

                <i class="fa-solid fa-credit-card text-2xl text-slate-700"></i>
            </div>
        </div>

        {{-- PAYMENT --}}
        @if(($report->page_count ?? 0) > 0)

            @if($report->payment_status === 'paid')

                {{-- ✅ Already Paid --}}
                <div class="w-full bg-green-600 text-white font-bold py-4 rounded-xl">
                    ✅ Already Unlocked
                </div>

            @else

                {{-- 💳 Pay Button --}}
               <form method="POST"
      action="{{ route('payment.process', ['reportId' => $report->id]) }}"
      onsubmit="handlePayment(this)">

    @csrf

    <button
        type="submit"
        id="unlockBtn"
        class="w-full bg-[#6a4dff] hover:bg-[#6a4dff]/90 text-white font-bold py-4 rounded-xl shadow-xl shadow-[#6a4dff]/30 transition-all uppercase tracking-widest text-xs flex items-center justify-center gap-3"
    >
        <i class="fa-solid fa-shield-halved"></i>
        Pay & Unlock (${{ number_format($totalPrice, 2) }})
    </button>
</form>

            @endif

        @else

            {{-- ❌ No Page Count --}}
            <p class="text-red-500 text-sm font-bold">
                Unable to calculate pages. Please re-upload file.
            </p>

        @endif

        {{-- BACK --}}
        <a href="{{ route('dashboard') }}"
           class="block mt-5 text-slate-600 hover:text-white transition-colors text-[10px] font-bold uppercase tracking-[0.2em]">
            Back to Dashboard
        </a>

    </div>

</div>

{{-- JS --}}
<script>
function handlePayment(form) {
    const btn = form.querySelector('button');

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Redirecting...';
    btn.disabled = true;

    return true; // ✅ VERY IMPORTANT (allows submit)
}
</script>

@endsection