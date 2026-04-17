@extends('welcome')

@section('content')

<div class="flex flex-col items-center bg-black justify-center min-h-[calc(100vh-160px)] px-4">

    <div class="w-full max-w-[500px] bg-[#111111] border border-[#222222] rounded-3xl p-8 sm:p-10 shadow-2xl relative overflow-hidden">

        {{-- Top Glow Line --}}
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-[#6a4dff] to-transparent opacity-50"></div>

        {{-- Heading --}}
        <div class="text-center mb-10">
            <div class="size-16 bg-[#6a4dff]/10 rounded-2xl flex items-center justify-center text-[#6a4dff] mx-auto mb-6 border border-[#6a4dff]/20">
               <i class="fa-solid fa-upload text-2xl"></i>
            </div>

            <h2 class="text-2xl font-black text-white mb-2 uppercase tracking-tight">
                Upload Statement
            </h2>

            <p class="text-slate-500 text-[10px] font-black uppercase tracking-[0.2em]">
                PDF or Text formats supported
            </p>
        </div>

        {{-- Error --}}
        @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl text-[10px] font-black uppercase tracking-widest mb-8 text-center">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('upload.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

            {{-- File Upload --}}
            <div class="relative group">
                <input
                    type="file"
                    name="statement"
                    accept=".pdf,.txt"
                    class="hidden"
                    id="file-upload"
                    onchange="showFileName(this)"
                >

                <label
                    for="file-upload"
                    class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-[#222222] hover:border-[#6a4dff]/50 rounded-2xl cursor-pointer bg-white/[0.02] hover:bg-white/[0.04] transition-all group"
                >
                    <div id="uploadPlaceholder">
                       <i class="fa-solid fa-circle-plus text-slate-500 text-4xl mb-2 group-hover:text-[#6a4dff] transition-colors ml-10"></i>
                        <p class="text-slate-500 text-xs font-bold">Drop statement here</p>
                        <p class="text-slate-600 text-[9px] uppercase font-black tracking-[0.2em] mt-1">
                            or click to browse
                        </p>
                    </div>

                    <div id="filePreview" class="hidden text-center">
                        <i class="fa-solid fa-file text-[#6a4dff] text-4xl mb-2"></i>
                        <p id="fileName" class="text-white text-xs font-bold truncate max-w-[200px]"></p>
                        <p class="text-slate-500 text-[9px] uppercase font-black tracking-widest mt-1">
                            Change file
                        </p>
                    </div>
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full bg-[#6a4dff] hover:bg-[#6a4dff]/90 text-white font-bold py-4 rounded-xl shadow-xl shadow-[#6a4dff]/30 transition-all uppercase tracking-widest text-[10px] flex items-center justify-center gap-2"
            >
                <i class="fa-solid fa-bolt text-lg"></i>
                Start AI Analysis
            </button>

        </form>

    </div>

</div>

{{-- JS --}}
<script>
function showFileName(input) {
    const file = input.files[0];

    if (file) {
        document.getElementById('uploadPlaceholder').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('fileName').innerText = file.name;
    }
}
</script>

@endsection
