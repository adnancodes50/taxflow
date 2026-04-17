@extends('admin.layouts.app')

@section('content')
    <main class="w-full bg-black min-h-screen px-4 sm:px-8 py-6">

        {{-- HEADER --}}
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
                        Orders
                    </h1>
                    <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">
                        Manage and track all customer orders
                    </p>
                    <div class="h-1 w-12 bg-[#6a4dff] rounded-full mt-3"></div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="bg-[#0a0a0a] border border-[#1a1a1a] px-4 py-2 rounded-xl">
                        <span class="text-xs text-slate-400">Total Orders:</span>
                        <span class="text-white font-bold ml-2">{{ $reports->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- STATS CARDS --}}
        @php
            $paidOrders = $reports->where('payment_status', 'paid')->count();
            $pendingOrders = $reports->where('payment_status', '!=', 'paid')->where('status', '!=', 'analyzing')->where('status', '!=', 'failed')->count();
            $processingOrders = $reports->where('status', 'analyzing')->count();
            $failedOrders = $reports->where('status', 'failed')->count();
            $totalRevenue = $reports->where('payment_status', 'paid')->sum('price');
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-4 rounded-xl hover:border-[#2a2a2a] transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.2em]">Revenue</p>
                    <svg class="w-4 h-4 text-[#6a4dff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-[#6a4dff]">${{ number_format($totalRevenue, 2) }}</h3>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-4 rounded-xl hover:border-[#2a2a2a] transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.2em]">Paid</p>
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-emerald-400">{{ $paidOrders }}</h3>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-4 rounded-xl hover:border-[#2a2a2a] transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.2em]">Pending</p>
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-amber-400">{{ $pendingOrders }}</h3>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-4 rounded-xl hover:border-[#2a2a2a] transition-all duration-300">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-slate-500 text-[9px] font-black uppercase tracking-[0.2em]">Processing</p>
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-blue-400">{{ $processingOrders }}</h3>
            </div>
        </div>

        {{-- ORDERS TABLE --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-6 py-5 border-b border-[#1a1a1a] flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-black text-white uppercase tracking-wide">All Orders</h2>
                    <p class="text-[10px] text-slate-500 mt-1">Complete order history</p>
                </div>
                <span class="text-[9px] text-slate-500 font-black uppercase bg-[#111] px-3 py-1 rounded-full">Live</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-500 text-[10px] uppercase border-b border-[#1a1a1a] bg-[#050505]">
                            <th class="px-6 py-4 text-left font-black w-[12%]">Client</th>
                            <th class="px-6 py-4 text-left font-black w-[18%]">Business Name</th>
                            <th class="px-6 py-4 text-left font-black w-[15%]">Filename</th>
                            <th class="px-6 py-4 text-center font-black w-[8%]">Pages</th>
                            <th class="px-6 py-4 text-center font-black w-[12%]">Date</th>
                            <th class="px-6 py-4 text-center font-black w-[10%]">Amount</th>
                            <th class="px-6 py-4 text-right font-black w-[10%]">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($reports as $report)
                            <tr class="hover:bg-white/5 transition-all duration-200 border-b border-[#111]">
                                {{-- CLIENT --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#6a4dff] to-[#4F8EFF] flex items-center justify-center text-xs font-bold text-white shrink-0">
                                            {{ strtoupper(substr($report->upload->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="text-white text-xs font-medium truncate">
                                            {{ $report->upload->user->name ?? 'Unknown User' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- BUSINESS NAME --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="text-white text-xs truncate">
                                            {{ $report->business_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- FILENAME --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-slate-400 text-xs truncate">
                                            {{ $report->upload->file_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- PAGES --}}
                                <td class="px-6 py-4 text-center">
                                    <span class="text-white text-xs bg-[#1a1a1a] px-2 py-1 rounded-full">
                                        {{ $report->page_count ?? 0 }}
                                    </span>
                                </td>

                                {{-- DATE --}}
                                <td class="px-6 py-4 text-center text-slate-400 text-xs">
                                    {{ $report->created_at->format('M d, Y') }}
                                </td>

                                {{-- AMOUNT --}}
                                <td class="px-6 py-4 text-center font-bold text-sm
                                    {{ $report->payment_status === 'paid' ? 'text-emerald-400' : 'text-slate-400' }}">
                                    ${{ number_format($report->price ?? 0, 2) }}
                                </td>

                                {{-- STATUS --}}
                               <td class="px-6 py-4 text-right">

    {{-- PAID (highest priority) --}}
    @if($report->payment_status === 'paid')
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-bold">
            <i class="fa-solid fa-check text-xs"></i>
            PAID
        </span>

    {{-- PROCESSING --}}
    @elseif($report->status === 'analyzing')
        <span class="inline-flex items-center gap-2 bg-blue-500/10 text-blue-400 px-3 py-1.5 rounded-lg text-xs font-bold">
            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
            PROCESSING
        </span>

    {{-- FAILED --}}
    @elseif($report->status === 'failed')
        <span class="inline-flex items-center gap-2 bg-red-500/10 text-red-400 px-3 py-1.5 rounded-lg text-xs font-bold">
            <i class="fa-solid fa-xmark text-xs"></i>
            FAILED
        </span>

    {{-- DEFAULT --}}
    @else
        <span class="inline-flex items-center gap-2 bg-amber-500/10 text-amber-400 px-3 py-1.5 rounded-lg text-xs font-bold">
            <i class="fa-solid fa-clock text-xs"></i>
            PENDING
        </span>
    @endif

</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-slate-400 text-sm">No orders found</p>
                                        <p class="text-slate-500 text-xs">No customer orders available yet</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>


                </table>

                <div class="px-6 py-4 border-t border-[#1a1a1a]">
    {{ $reports->links() }}
</div>
            </div>
        </div>
    </main>

    <style>
        /* Spinner */
        .loader {
            width: 10px;
            height: 10px;
            border: 2px solid currentColor;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
