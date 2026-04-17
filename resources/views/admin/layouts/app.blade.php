<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Admin Panel - TaxFlowAI</title>

    @vite(['resources/css/app.css'])

    {{-- Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-[#05060A] text-white font-sans">

    <div class="flex min-h-screen relative">

        {{-- MOBILE MENU BUTTON - FIXED ALIGNMENT --}}
        <button id="mobileMenuButton"
            class="lg:hidden fixed top-4 left-4 z-50 bg-[#6a4dff] p-2.5 rounded-xl shadow-lg shadow-[#6a4dff]/30 flex items-center justify-center">
            <span class="material-symbols-outlined text-white text-2xl">menu</span>
        </button>

        {{-- OVERLAY FOR MOBILE --}}
        <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden"></div>

        {{-- SIDEBAR --}}
        <aside id="sidebar"
            class="fixed lg:relative w-64 bg-[#07080D] border-r border-white/5 p-6 flex flex-col transition-all duration-300 z-50
                  -translate-x-full lg:translate-x-0 h-full lg:h-auto overflow-y-auto">

            {{-- CLOSE BUTTON (MOBILE ONLY) --}}
            <button id="closeSidebar" class="lg:hidden absolute top-4 right-4 text-slate-400 hover:text-white p-2">
                <span class="material-symbols-outlined">close</span>
            </button>

            {{-- LOGO --}}
            <div class="flex items-center gap-3 mb-10 mt-2 lg:mt-0">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-[#6a4dff] to-[#7c5cff] rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-xl">dashboard</span>
                </div>
                <div>
                    <h2 class="font-bold text-base">TaxFlowAI</h2>
                    <p class="text-[10px] text-[#6a4dff]">ADMIN CONSOLE</p>
                </div>
            </div>

            {{-- NAV --}}
            <nav class="space-y-2 flex-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm
               {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-[#6a4dff] to-[#5a6cff] shadow-lg shadow-[#6a4dff]/20 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span class="material-symbols-outlined text-xl">grid_view</span>
                    <span>Overview</span>
                </a>

                <a href="{{ route('admin.order') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm
               {{ request()->routeIs('admin.order') ? 'bg-gradient-to-r from-[#6a4dff] to-[#5a6cff] shadow-lg shadow-[#6a4dff]/20 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span class="material-symbols-outlined text-xl">shopping_cart</span>
                    <span>Orders</span>
                </a>

                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 text-sm
               {{ request()->routeIs('admin.settings') ? 'bg-gradient-to-r from-[#6a4dff] to-[#5a6cff] shadow-lg shadow-[#6a4dff]/20 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span class="material-symbols-outlined text-xl">settings</span>
                    <span>Settings</span>
                </a>
            </nav>

            {{-- USER --}}


            {{-- LOGOUT --}}


        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-h-screen w-full">

            {{-- TOP HEADER (RESPONSIVE) --}}
          <header
    class="h-16 flex items-center justify-between px-4 sm:px-8 border-b border-white/5 bg-[#05060A]/80 backdrop-blur-sm sticky top-0 z-30">

    {{-- LEFT SIDE --}}
    <div class="flex items-center gap-3">
        {{-- Spacer for mobile --}}
        <div class="lg:hidden w-12"></div>

        <div class="text-xs sm:text-sm text-slate-400 tracking-widest uppercase hidden sm:block">
            @yield('header', 'Secure System / Overview')
        </div>

        {{-- <div class="sm:hidden text-xs text-slate-400 tracking-widest uppercase">
            Admin
        </div> --}}
    </div>

    {{-- RIGHT SIDE --}}
    <div class="flex items-center gap-3">

        {{-- Avatar --}}
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-[#6a4dff] rounded-full flex items-center justify-center text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
        </div>

        {{-- Logout Button --}}
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button
    class="text-xs bg-red-500/10 border border-red-500/20 text-red-400 px-3 py-2 rounded-lg hover:bg-red-500/20 transition-all duration-200 flex items-center gap-2">

    <i class="fa-solid fa-right-from-bracket text-sm"></i>

    <span>Logout</span>

</button>
        </form>

    </div>
</header>

            {{-- MAIN CONTENT AREA --}}
            <main class="p-4 sm:p-6 lg:p-8 flex-1">
                @yield('content')
            </main>

        </div>

    </div>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #07080D;
        }

        ::-webkit-scrollbar-thumb {
            background: #1a1a1a;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #2a2a2a;
        }

        /* Prevent body scroll when sidebar is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }

        /* Smooth transitions */
        #sidebar {
            scrollbar-width: thin;
        }

        /* Responsive table containers */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
    </style>

    <script>
        // Mobile Sidebar Toggle
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebarFunc() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
            document.body.classList.remove('sidebar-open');
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', openSidebar);
        }

        if (closeSidebar) {
            closeSidebar.addEventListener('click', closeSidebarFunc);
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebarFunc);
        }

        // Close sidebar on window resize if screen becomes large
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                overlay.classList.add('hidden');
                document.body.classList.remove('sidebar-open');
            } else {
                // On mobile, ensure sidebar is closed by default
                if (!sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('translate-x-0');
                }
            }
        });

        // Initialize sidebar state for mobile
        if (window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
    </script>

</body>

</html>
