<header class="border-b border-slate-800 bg-black backdrop-blur-md sticky top-0 z-50">
    <div class="w-full px-4 sm:px-6 h-16 flex items-center justify-between">

        {{-- Logo --}}
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <div class="bg-indigo-600 p-1.5 rounded-lg text-white">
                    <i class="fa-solid fa-chart-line text-xl"></i>
                </div>
                <h1 class="text-base sm:text-lg font-bold tracking-tight text-white">
                    TaxFlowAI
                </h1>
            </a>
        </div>

        {{-- Right Side --}}
        <div class="flex items-center gap-4">

            {{-- Back Button (optional) --}}
            @if (!request()->is('/'))
                <a href="{{ url()->previous() }}"
                    class="p-2 rounded-xl bg-white/5 border border-white/10 text-slate-400 hover:text-white hover:bg-white/10 transition-all flex items-center justify-center group"
                    title="Go Back">

                    <i class="fa-solid fa-arrow-left text-lg group-hover:-translate-x-0.5 transition-transform"></i>

                </a>
            @endif

            {{-- Auth Check --}}
            @auth

                <a href="{{ url('/dashboard') }}"
                    class="text-xs font-bold text-slate-300 hover:text-white transition-colors">
                    Dashboard
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-[10px] font-black uppercase tracking-widest text-red-500 hover:text-white transition-all px-4 py-2 border border-red-500/20 rounded-xl hover:bg-red-500/10">
                        Sign Out
                    </button>
                </form>
            @else
                {{-- Only show on landing page --}}
                @if (request()->is('/'))
                    <a href="{{ route('login') }}"
                        class="text-[10px] font-black uppercase tracking-widest bg-[#6a4dff] text-white px-5 py-2.5 rounded-xl shadow-lg shadow-[#6a4dff]/20 hover:scale-[1.02] active:scale-95 transition-all">
                        Login
                    </a>

                    {{-- @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="text-[10px] font-black uppercase tracking-widest border border-white/10 text-white px-5 py-2.5 rounded-xl hover:bg-white/10 transition-all">
                            Register
                        </a>
                    @endif --}}
                @endif

            @endauth

        </div>
    </div>
</header>
