@extends('admin.layouts.app')

@section('content')
<div class="w-full bg-black min-h-screen px-4 sm:px-8 py-6 items-center">


 <div class="w-full mt-0 bg-[#0a0a0a] border border-[#1a1a1a] rounded-2xl p-8 relative overflow-hidden shadow-2xl">

        {{-- ✅ SUCCESS MESSAGE OUTSIDE FORM (TOP OF MAIN DIV) --}}
        @if (session('success'))
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3 rounded-xl text-sm font-bold shadow-lg flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER --}}
        <div class="flex items-center gap-4 mt-0 pb-4 border-b border-[#1a1a1a]">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#6a4dff] to-[#4F8EFF] flex items-center justify-center shadow-lg shadow-[#6a4dff]/30">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>

            <div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                    Administrative Identity
                </h2>
                <p class="text-xs text-slate-500 uppercase tracking-wider mt-1">
                    Manage global root credentials & system settings
                </p>
                <div class="h-1 w-12 bg-[#6a4dff] rounded-full mt-2"></div>
            </div>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('admin.settings.save') }}" class="space-y-8">
            @csrf

            {{-- STRIPE KEYS SECTION (2 COLUMNS) --}}
            <div>
                <div class="flex items-center gap-2 mb-5">
                    <svg class="w-5 h-5 text-[#6a4dff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wide">Stripe Configuration</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs text-slate-400 font-medium uppercase tracking-wider">Stripe Public Key</label>
                        <input type="text"
                            name="stripe_public_key"
                            value="{{ $settings->stripe_public_key ?? '' }}"
                            placeholder="pk_live_..."
                            class="mt-2 w-full bg-black/60 border border-[#1a1a1a] rounded-xl p-4 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-[#6a4dff] focus:ring-1 focus:ring-[#6a4dff] transition-all duration-200">
                    </div>

                    <div>
                        <label class="text-xs text-slate-400 font-medium uppercase tracking-wider">Stripe Secret Key</label>
                        <input type="text"
                            name="stripe_secret_key"
                            value="{{ $settings->stripe_secret_key ?? '' }}"
                            placeholder="sk_live_..."
                            class="mt-2 w-full bg-black/60 border border-[#1a1a1a] rounded-xl p-4 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-[#6a4dff] focus:ring-1 focus:ring-[#6a4dff] transition-all duration-200">
                    </div>
                </div>
            </div>

            {{-- PRICE & AI SECTION (2 COLUMNS SIDE BY SIDE) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- PRICE SECTION --}}
                <div>
                    <div class="flex items-center gap-2 mb-5">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide">Pricing Configuration</h3>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 font-medium uppercase tracking-wider">Per Page Price (USD)</label>
                        <div class="relative mt-2">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#6a4dff] font-bold">$</span>
                            <input type="number"
                                step="0.01"
                                name="per_page_price"
                                value="{{ $settings->per_page_price ?? '' }}"
                                placeholder="0.00"
                                class="w-full bg-black/60 border border-[#1a1a1a] rounded-xl p-4 pl-8 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-[#6a4dff] focus:ring-1 focus:ring-[#6a4dff] transition-all duration-200">
                        </div>
                        <p class="text-[10px] text-slate-500 mt-2">Current pricing for each page processed</p>
                    </div>
                </div>

                {{-- AI KEY SECTION --}}
                <div>
                    <div class="flex items-center gap-2 mb-5">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wide">AI Configuration</h3>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 font-medium uppercase tracking-wider">AI API Key</label>
                        <input type="text"
                            name="ai_key"
                            value="{{ $settings->ai_key ?? '' }}"
                            placeholder="sk-..."
                            class="mt-2 w-full bg-black/60 border border-[#1a1a1a] rounded-xl p-4 text-sm text-white placeholder:text-slate-600 focus:outline-none focus:border-[#6a4dff] focus:ring-1 focus:ring-[#6a4dff] transition-all duration-200">
                        <p class="text-[10px] text-slate-500 mt-2">OpenAI API key for document processing</p>
                    </div>
                </div>
            </div>

            {{-- BUTTON --}}
            <div class="pt-6 border-t border-[#1a1a1a]">
                <button type="submit"
                    class="w-full py-4 rounded-xl text-xs font-black uppercase tracking-widest
                    bg-gradient-to-r from-[#6a4dff] to-[#5a3de5]
                    hover:from-[#5a3de5] hover:to-[#4a2dc5]
                    shadow-xl shadow-[#6a4dff]/20 hover:scale-[1.02] transition-all duration-300
                    text-white flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Update Administrative Identity
                </button>
            </div>

        </form>

    </div>
   

</div>

<style>
    /* Remove number input arrows */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #0a0a0a;
    }
    ::-webkit-scrollbar-thumb {
        background: #1a1a1a;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #2a2a2a;
    }
</style>

@endsection