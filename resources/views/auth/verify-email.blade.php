<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-160px)] px-4">

        <div class="w-full max-w-lg bg-[#111111] border border-[#222222] rounded-2xl p-10 shadow-2xl">

            {{-- Heading --}}
            <div class="text-center mb-8">
                <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-tight">
                    Verify Email
                </h2>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">
                    Confirm your email to continue
                </p>
            </div>

            {{-- Description --}}
            <p class="text-[10px] text-slate-500 text-center mb-6 font-bold uppercase tracking-widest">
                Check your inbox and click the verification link
            </p>

            {{-- Success Message --}}
            @if (session('status') == 'verification-link-sent')
                <div class="bg-green-500/10 border border-green-500/20 text-green-500 p-3 rounded-xl text-[10px] font-black uppercase tracking-widest mb-6 text-center">
                    A new verification link has been sent
                </div>
            @endif

            <div class="space-y-4">

                {{-- Resend Button --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full bg-[#6a4dff]/90 text-white font-bold py-4 rounded-xl shadow-xl shadow-[#6a4dff]/30 transition-all uppercase tracking-widest text-[10px]"
                    >
                        Resend Verification Email
                    </button>
                </form>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full border border-white/10 text-slate-500 hover:text-white hover:border-white/20 py-3 rounded-xl transition-all uppercase tracking-widest text-[10px] font-bold"
                    >
                        Log Out
                    </button>
                </form>

            </div>

        </div>
    </div>
</x-guest-layout>
