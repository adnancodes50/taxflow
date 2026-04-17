@extends('welcome')

@section('content')
    <div
        class="flex flex-col items-center justify-center min-h-[calc(100vh-160px)] p-6 relative overflow-hidden bg-[#050505]">

        {{-- Glow --}}
        <div
            class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-[#6a4dff]/10 rounded-full blur-[120px]">
        </div>

        <div
            class="w-full max-w-[460px] bg-[#0D0D0D]/80 border border-white/5 rounded-[2.5rem] p-10 shadow-2xl relative z-10">

            {{-- ICON --}}
            <div class="flex justify-center mb-10">
                <div class="relative size-24 flex items-center justify-center">
                    <div
                        class="absolute inset-0 rounded-full border-[3px] border-transparent border-t-[#6a4dff] animate-spin">
                    </div>
                    <div class="size-16 bg-[#6a4dff]/10 rounded-full flex items-center justify-center text-[#6a4dff]">
                        <i class="fa-solid fa-wand-magic-sparkles text-4xl animate-pulse"></i>
                    </div>
                </div>
            </div>

            {{-- TITLE --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl font-black text-white uppercase">Analyzing Statement</h2>
            </div>

            {{-- STEPS --}}
            <div class="space-y-5 mb-8">
                <div class="flex items-center gap-4">
                    <div class="size-5 bg-green-500 rounded"></div>
                    <span class="text-xs text-slate-400">Statement uploaded</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="size-5 bg-[#6a4dff] animate-pulse rounded"></div>
                    <span class="text-xs text-white">Processing...</span>
                </div>
            </div>

            {{-- STATUS MESSAGE --}}
            <div class="text-center">
                <p id="statusText" class="text-sm !text-[#6a4dff]">
                    Please wait while we analyze your statement...
                </p>
            </div>

        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        const uploadId = "{{ $uploadId }}";
        const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};

        const messages = [
            "Uploading statement...",
            "Reading your file...",
            "Analyzing transactions...",
            "Categorizing data...",
            "Finalizing report..."
        ];

        let msgIndex = 0;

        function rotateMessages() {
    const status = document.getElementById('statusText');

    if (msgIndex < messages.length) {
        status.innerText = messages[msgIndex]; // ✅ THIS WAS MISSING
        status.style.color = "#6a4dff"; // keep purple
        msgIndex++;
    } else {
        status.innerText = messages[messages.length - 1];
    }

    setTimeout(rotateMessages, 2000);
}

        window.onload = function() {

            if (!uploadId) {
                alert("Upload ID missing");
                return;
            }

            if (!isLoggedIn) {
                document.getElementById('statusText').innerText = "Please login or register to continue";

                setTimeout(() => {
                    window.location.href = "/login";
                }, 2000);

                return;
            }

            rotateMessages();

            // ✅ IMPORTANT: USE FETCH (NOT sendBeacon)
            fetch(`/process-analysis/${uploadId}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .catch(err => console.error(err));

            // ✅ redirect after request is sent
            setTimeout(() => {
                window.location.href = "/dashboard";
            }, 2000);
        };
    </script>
@endsection
