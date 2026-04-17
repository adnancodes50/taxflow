@extends('admin.layouts.app')

@section('content')
<main class="w-full bg-black min-h-screen px-4 sm:px-8 py-6">

    {{-- HEADER --}}
    <div class="mb-10 mt-2">
        <h2 class="text-xl sm:text-2xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-1">
            Admin Dashboard
        </h2>
        <p class="text-slate-500 text-xs sm:text-sm font-medium">
            System overview & activity monitoring
        </p>
        <div class="h-1 w-12 bg-[#6a4dff] rounded-full mt-3"></div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
        {{-- TOTAL SALES --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Total Sales</p>
                <svg class="w-5 h-5 text-[#6a4dff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex justify-between items-end">
                <h3 class="text-2xl font-black text-[#6a4dff]">
                    ${{ number_format($totalsales, 2) }}
                </h3>
                <span class="text-xs px-2 py-1 rounded-full
                    {{ $salesChange >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                    {{ $salesChange >= 0 ? '+' : '' }}{{ number_format($salesChange, 1) }}%
                </span>
            </div>
        </div>

        {{-- TOTAL ORDERS --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Total Orders</p>
                <svg class="w-5 h-5 text-[#6a4dff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <div class="flex justify-between items-end">
                <h3 class="text-2xl font-black text-white">{{ $totalOrders }}</h3>
                <span class="bg-emerald-500/10 text-emerald-400 text-xs px-2 py-1 rounded-full">
                    Live
                </span>
            </div>
        </div>

        {{-- COMPLETE --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Complete</p>
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex justify-between items-end">
                <h3 class="text-2xl font-black text-emerald-400">{{ $totalcomplete }}</h3>
                <span class="text-emerald-400 text-xs">Verified</span>
            </div>
        </div>

        {{-- PENDING --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">Pending</p>
                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex justify-between items-end">
                <h3 class="text-2xl font-black text-rose-400">{{ $totalpending }}</h3>
                <span class="bg-rose-500/10 text-rose-400 text-xs px-2 py-1 rounded-full">
                    Pending
                </span>
            </div>
        </div>
    </div>

    {{-- SYSTEM ACTIVITY TABLE --}}
    <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-5 border-b border-[#1a1a1a] flex justify-between items-center">
            <div>
                <h2 class="text-sm font-black text-white uppercase tracking-wide">System Activity</h2>
                <p class="text-[10px] text-slate-500 mt-1">All user reports and transactions</p>
            </div>
            <span class="text-[9px] text-slate-500 font-black uppercase bg-[#111] px-3 py-1 rounded-full">Live</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-slate-500 text-[10px] uppercase border-b border-[#1a1a1a] bg-[#050505]">
                        <th class="px-6 py-4 text-left font-black w-[25%]">Client</th>
                        <th class="px-6 py-4 text-left font-black w-[20%]">Filename</th>
                        <th class="px-6 py-4 text-center font-black w-[10%]">Pages</th>
                        <th class="px-6 py-4 text-center font-black w-[15%]">Date</th>
                        <th class="px-6 py-4 text-center font-black w-[15%]">Amount</th>
                        <th class="px-6 py-4 text-right font-black w-[15%]">Status</th>
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

                            {{-- FILE --}}
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
                                    {{ $report->page_count }}
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

    @if($report->payment_status === 'paid')
        <span class="inline-flex items-center gap-2 bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-bold">
            <i class="fa-solid fa-check text-xs"></i>
            PAID
        </span>

    @elseif($report->payment_status === 'pending')
        <span class="inline-flex items-center gap-2 bg-amber-500/10 text-amber-400 px-3 py-1.5 rounded-lg text-xs font-bold">
            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
            PENDING
        </span>

    @endif

</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-slate-400 text-sm">No records found</p>
                                    <p class="text-slate-500 text-xs">No user reports available yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
