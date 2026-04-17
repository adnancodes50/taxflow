<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-160px)] px-4">

        <div class="w-full max-w-lg bg-[#111111] border border-[#222222] rounded-2xl p-10 shadow-2xl">

            {{-- Heading --}}
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-tight">
                    Create Account
                </h2>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">
                    Join TaxFlowAI
                </p>
            </div>

            {{-- Errors --}}
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-3 rounded-xl text-[10px] font-black uppercase tracking-widest mb-6 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                        Full Name
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#6a4dff]/50 transition-all text-sm"
                        placeholder="Enter your name"
                    >
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#6a4dff]/50 transition-all text-sm"
                        placeholder="email@example.com"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#6a4dff]/50 transition-all text-sm"
                        placeholder="••••••••"
                    >
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                        Confirm Password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full bg-white/[0.03] border border-white/5 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#6a4dff]/50 transition-all text-sm"
                        placeholder="••••••••"
                    >
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full bg-[#6a4dff] hover:bg-[#6a4dff]/90 text-white font-bold py-4 rounded-xl shadow-xl shadow-[#6a4dff]/30 transition-all uppercase tracking-widest text-[10px] mt-6"
                >
                    Register
                </button>
            </form>

            {{-- Login Link --}}
            <div class="mt-8 pt-6 border-t border-[#222222] text-center">
                <a href="{{ route('login') }}"
                   class="text-[10px] font-black text-slate-500 hover:text-[#6a4dff] uppercase tracking-widest transition-colors">
                    Already have an account? Sign In
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
