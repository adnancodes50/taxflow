<x-guest-layout>
    <div class="flex flex-col items-center justify-center min-h-screen px-4 bg-[#0B0B0F] text-white">

        <div class="w-full max-w-md bg-[#111111]/80 border border-white/10 p-10 rounded-3xl shadow-[0_20px_60px_rgba(0,0,0,0.6)] backdrop-blur-xl">

            {{-- Logo + Heading --}}
            <div class="flex flex-col items-center mb-10">
               <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#7c5cff] to-[#6a4dff] flex items-center justify-center text-white shadow-lg shadow-[#6a4dff]/30 mb-6">
<i class="fa-solid fa-user-lock text-2xl"></i></div>

                <h2 class="text-2xl font-black tracking-tight uppercase">
                    Admin Console
                </h2>

                <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mt-2">
                    Authenticated Management Access
                </p>
            </div>

            {{-- Error --}}
            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 text-[10px] py-3 px-4 rounded-xl mb-6 text-center font-black uppercase tracking-widest">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                        Email Address
                    </label>
                    <input
                        type="email"
                        name="email"
                        required
                        placeholder="admin@taxflowai.com"
                        class="w-full bg-white/[0.04] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#6a4dff] focus:ring-1 focus:ring-[#6a4dff] transition-all placeholder:text-slate-500"
                    >
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2 ml-1">
                        Secure Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full bg-white/[0.04] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-[#6a4dff] focus:ring-1 focus:ring-[#6a4dff] transition-all placeholder:text-slate-500"
                    >
                </div>

                {{-- Button --}}
                <button
                    type="submit"
                    class="w-full py-4 rounded-xl font-black uppercase tracking-[0.2em] text-[10px] text-white
                           bg-gradient-to-r from-[#6a4dff] to-[#7c5cff]
                           shadow-[0_10px_30px_rgba(106,77,255,0.4)]
                           hover:scale-[1.02] active:scale-[0.98] transition-all"
                >
                    Start Session
                </button>

                {{-- Back --}}
                <div class="text-center mt-6">
                    <a href="{{ url('/') }}"
                       class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-[#6a4dff] transition-all flex items-center justify-center gap-2 group">
                        <div class="flex items-center gap-2 group cursor-pointer">
    <i class="fa-solid fa-arrow-left text-sm group-hover:-translate-x-1 transition-transform"></i>
</div>
                        Back to Website
                    </a>
                </div>

            </form>
        </div>
    </div>
</x-guest-layout>
