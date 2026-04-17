@extends('welcome')

@section('content')
    <div class="w-full bg-black min-h-screen px-4 sm:px-8 py-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6 mb-10 mt-2">
            <div>
                <h2 class="text-xl sm:text-2xl font-black tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent mb-1">
                    Hello, {{ auth()->user()->name ?? 'User' }}!
                </h2>
                <p class="text-slate-500 text-xs sm:text-sm font-medium">
                    Automated tax intelligence archive.
                </p>
                <div class="h-1 w-12 bg-[#6a4dff] rounded-full mt-3"></div>
            </div>

            <a href="{{ route('upload') }}"
                class="w-full sm:w-auto bg-[#6a4dff] hover:bg-[#5a3de5] text-white font-bold py-3 px-6 rounded-xl shadow-xl shadow-[#6a4dff]/30 transition-all duration-300 flex items-center justify-center gap-2 text-xs uppercase tracking-widest hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Upload File
            </a>
        </div>

        {{-- STATS CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-5 mb-10">
            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-3">Total Files</p>
                <p class="text-2xl font-black text-white">{{ $stats['total'] ?? 0 }}</p>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-3">Paid</p>
                <p class="text-2xl font-black text-emerald-400">{{ $stats['paid'] ?? 0 }}</p>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-3">Unpaid</p>
                <p class="text-2xl font-black text-rose-400">{{ $stats['unpaid'] ?? 0 }}</p>
            </div>

            <div class="bg-[#0a0a0a] border border-[#1a1a1a] p-5 rounded-2xl hover:border-[#2a2a2a] transition-all duration-300">
                <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] mb-3">Total Spend</p>
                <p class="text-2xl font-black text-white">${{ number_format($stats['spend'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-[#0a0a0a] border border-[#1a1a1a] rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-6 py-5 border-b border-[#1a1a1a] flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-wide">Statement History</h3>
                    <p class="text-[10px] text-slate-500 mt-1">All your uploaded statements</p>
                </div>
                <span class="text-[9px] text-slate-500 font-black uppercase bg-[#111] px-3 py-1 rounded-full">Encrypted</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-500 text-[10px] uppercase border-b border-[#1a1a1a] bg-[#050505]">
                            <th class="px-6 py-4 font-black">File</th>
                            <th class="px-6 py-4 font-black">Date</th>
                            <th class="px-6 py-4 font-black">Status</th>
                            <th class="px-6 py-4 font-black">Accuracy</th>
                            <th class="px-6 py-4 font-black text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($uploads as $upload)
                            @php
                                $report = $upload->report;
                            @endphp

                            <tr class="hover:bg-white/5 transition-all duration-200 border-b border-[#111] {{ !$report || $report->status === 'analyzing' ? 'processing' : '' }}"
                                data-report-id="{{ $report->id ?? '' }}">
                                
                                {{-- FILE NAME --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-[#1a1a1a] flex items-center justify-center">
                                            <svg class="w-4 h-4 text-[#6a4dff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-white text-xs font-bold">{{ $upload->file_name }}</span>
                                    </div>
                                </td>

                                {{-- DATE --}}
                                <td class="px-6 py-4 text-slate-400 text-xs">
                                    {{ $upload->created_at->format('d M Y') }}
                                </td>

                                {{-- STATUS --}}
                                <td class="px-6 py-4">
                                    @if (!$report || $report->status === 'analyzing')
                                        <span class="inline-flex items-center gap-2 text-blue-400 font-bold text-xs">
                                            <span class="loader"></span>
                                            Processing
                                        </span>
                                    @elseif($report->status === 'completed')
                                        <span class="inline-flex items-center gap-2 text-emerald-400 font-bold text-xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Completed
                                        </span>
                                    @elseif($report->status === 'failed')
                                        <span class="inline-flex items-center gap-2 text-red-400 font-bold text-xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Failed
                                        </span>
                                    @endif
                                </td>

                                {{-- ACCURACY --}}
                                <td class="px-6 py-4">
                                    @if (!$report || $report->status === 'analyzing')
                                        <span class="text-gray-500 text-xs">--</span>
                                    @elseif($report->status === 'completed')
                                        <span class="text-[#6a4dff] text-xs font-bold bg-[#1a1a1a] px-2 py-1 rounded-full">
                                            {{ $report->analysis_results['accuracy'] ?? '--' }}
                                        </span>
                                    @elseif($report->status === 'failed')
                                        <span class="text-red-400 text-xs">--</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="px-6 py-4 text-right">
                                    @if (!$report || $report->status === 'analyzing')
                                        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-[#1a1a1a] text-white text-xs">
                                            <span class="loader w-3 h-3"></span>
                                            Processing
                                        </span>
                                    @elseif($report->status === 'completed' && $report->payment_status !== 'paid')
                                        <a href="{{ route('lock', $report->id) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition-all duration-200 hover:scale-105">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Pay Now
                                        </a>
                                    @elseif($report->status === 'completed' && $report->payment_status === 'paid')
                                        <a href="{{ route('report', $upload->id) }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#6a4dff] hover:bg-[#5a3de5] text-white text-xs font-bold transition-all duration-200 hover:scale-105">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Edit & View
                                        </a>
                                    @elseif($report->status === 'failed')
                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-500/20 text-red-400 text-xs font-bold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate-500 py-16">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm">No uploads found</p>
                                        <p class="text-xs text-slate-600">Upload your first bank statement to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        /* Animated dots */
        .dots::after {
            content: '';
            animation: dots 1.5s steps(3, end) infinite;
        }

        @keyframes dots {
            0% { content: ''; }
            33% { content: '.'; }
            66% { content: '..'; }
            100% { content: '...'; }
        }

        /* Spinner */
        .loader {
            width: 12px;
            height: 12px;
            border: 2px solid #6a4dff;
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

    <script>
        const interval = setInterval(() => {
            let allCompleted = true;

            document.querySelectorAll('[data-report-id].processing').forEach(el => {
                const reportId = el.dataset.reportId;
                if (!reportId) return;

                fetch(`/fetch-result/${reportId}`)
                    .then(res => res.json())
                    .then(data => {
                        const statusCell = el.querySelector('td:nth-child(3)');
                        const actionCell = el.querySelector('td:nth-child(5)');

                        if (data.status === 'processing') {
                            allCompleted = false;
                            if (statusCell) {
                                statusCell.innerHTML = `
                                    <span class="inline-flex items-center gap-2 text-blue-400 font-bold text-xs">
                                        <span class="loader"></span>
                                        Processing
                                    </span>
                                `;
                            }
                        }

                        if (data.status === 'completed') {
                            if (statusCell) {
                                statusCell.innerHTML = `
                                    <span class="inline-flex items-center gap-2 text-emerald-400 font-bold text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Completed
                                    </span>
                                `;
                            }
                            // Reload page to update action buttons
                            setTimeout(() => location.reload(), 2000);
                        }

                        if (data.status === 'failed') {
                            if (statusCell) {
                                statusCell.innerHTML = `
                                    <span class="inline-flex items-center gap-2 text-red-400 font-bold text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        Failed
                                    </span>
                                `;
                            }
                        }
                    });
            });

            if (allCompleted) {
                clearInterval(interval);
            }
        }, 25000);
    </script>
@endsection