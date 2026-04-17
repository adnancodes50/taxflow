@extends('admin.layouts.app')

@section('content')
    <main class="w-full bg-black min-h-screen px-4 sm:px-8 py-6">

        {{-- HEADER --}}
        <div class="mb-10 mt-2">
            <h2
                class="text-xl sm:text-2xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-1">
                Admin Dashboard
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm font-medium">
                System overview & activity monitoring
            </p>
            <div class="h-1 w-12 bg-[#6a4dff] rounded-full mt-3"></div>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
            {{-- (unchanged code here) --}}
        </div>

        {{-- SYSTEM ACTIVITY TABLE --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-6 py-5 border-b border-[#1a1a1a] flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-black text-white uppercase tracking-wide">System Activity</h2>
                    <p class="text-[10px] text-slate-500 mt-1">All user reports and transactions</p>
                </div>
                <span class="text-[9px] text-emerald-400 font-black uppercase bg-emerald-500/10  px-3 py-1 rounded-full">Live</span>
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
                                        <div
                                            class="w-8 h-8 rounded-full bg-gradient-to-br from-[#6a4dff] to-[#4F8EFF] flex items-center justify-center text-xs font-bold text-white shrink-0">
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
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
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

                                {{-- DATE (FIXED) --}}
                                <td class="px-6 py-4 text-center text-slate-400 text-xs">
                                    {{ $report->created_at->format('M d, Y h:i A') }}
                                </td>

                                {{-- AMOUNT --}}
                                <td
                                    class="px-6 py-4 text-center font-bold text-sm
                                {{ $report->payment_status === 'paid' ? 'text-emerald-400' : 'text-slate-400' }}">
                                    ${{ number_format($report->price ?? 0, 2) }}
                                </td>

                                {{-- STATUS (FIXED INLINE) --}}
                                <td class="px-6 py-4 text-right">

                                    @if ($report->payment_status === 'paid')
                                        <span
                                            class="inline-flex items-center gap-2 whitespace-nowrap bg-emerald-500/10 text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-bold">
                                            <i class="fa-solid fa-check text-xs"></i>
                                            PAYMENT PAID
                                        </span>
                                    @elseif($report->payment_status === 'pending')
                                        <span
                                            class="inline-flex items-center gap-2 whitespace-nowrap bg-amber-500/10 text-amber-400 px-3 py-1.5 rounded-lg text-xs font-bold">
                                            <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                                            PAYMENT PENDING
                                        </span>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <p class="text-slate-400 text-sm">No records found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
