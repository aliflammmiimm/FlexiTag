<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Console') — FlexiTag</title>

    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (Vite / CDN Fallback) -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif

    <style>
        * {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        /* Color Tokens */
        .bg-sidebar {
            background-color: #380208;
        }

        .bg-cream {
            background-color: #FAF9F6;
        }

        .bg-gold-accent {
            background-color: #F0C75E;
        }

        .text-maroon {
            color: #8B0000;
        }

        .text-gold {
            color: #D4AF37;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #F4F1EA;
        }
        ::-webkit-scrollbar-thumb {
            background: #D4C9BC;
            border-radius: 9999px;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-cream text-[#1A1A1A] min-h-screen flex antialiased selection:bg-[#8B0000] selection:text-[#F0C75E]">

    <!-- ========================================================= -->
    <!-- LEFT SIDEBAR: STICKY / FIXED IN PLACE ON SCROLL           -->
    <!-- ========================================================= -->
    <aside class="w-64 sm:w-72 bg-sidebar h-screen sticky top-0 shrink-0 flex flex-col justify-between p-5 overflow-y-auto z-30 shadow-xl">
        
        <div>
            <!-- Sidebar Header: FlexiTag Logo & NFC PRO Pill -->
            <div class="flex items-center justify-between px-2 py-3 mb-6">
                <div class="flex items-center gap-3">
                    <!-- Circular Gold Logo Icon -->
                    <div class="w-10 h-10 rounded-full bg-[#D4AF37] shadow-[0_0_15px_rgba(212,175,55,0.35)] flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-[#380208]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"/>
                            <path d="M1.42 9a16 16 0 0 1 21.16 0"/>
                            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
                            <line x1="12" y1="20" x2="12.01" y2="20"/>
                        </svg>
                    </div>

                    <!-- Brand Title -->
                    <div>
                        <h1 class="text-xl font-bold tracking-tight leading-none text-white">
                            Flexi<span class="text-[#F0C75E]">Tag</span>
                        </h1>
                        <span class="text-[9.5px] uppercase font-bold tracking-[0.22em] text-[#D4AF37]/90 block mt-1">
                            Admin Console
                        </span>
                    </div>
                </div>

                <span class="bg-[#4A0A12] text-[#F0C75E] text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-md border border-[#6E1420] shrink-0">
                    NFC PRO
                </span>
            </div>

            <!-- Navigation Section Heading -->
            <div class="px-3 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#C49A5E]">
                    Navigasi Konsol
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5 text-xs font-semibold">
                
                <!-- 1. Dashboard -->
                <a 
                    href="{{ route('admin.dashboard') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] font-bold' : 'text-[#E2D4C8] hover:text-white hover:bg-white/5' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Generate Tag -->
                <a 
                    href="{{ route('admin.generate') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('admin.generate*') ? 'bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] font-bold' : 'text-[#E2D4C8] hover:text-white hover:bg-white/5' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v7.5m3.75-3.75h-7.5"/>
                    </svg>
                    <span>Generate Tag</span>
                </a>

                <!-- 3. Daftar Tag -->
                <a 
                    href="{{ route('admin.tags') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('admin.tags*') ? 'bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] font-bold' : 'text-[#E2D4C8] hover:text-white hover:bg-white/5' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                        <circle cx="6" cy="6" r="1"/>
                    </svg>
                    <span>Daftar Tag</span>
                </a>

                <!-- 4. Daftar Pesanan -->
                <a 
                    href="{{ route('admin.orders') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('admin.orders*') ? 'bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] font-bold' : 'text-[#E2D4C8] hover:text-white hover:bg-white/5' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <span>Daftar Pesanan</span>
                </a>

                <!-- 5. Analitik & Tap -->
                <a 
                    href="{{ route('admin.analytics') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('admin.analytics*') ? 'bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] font-bold' : 'text-[#E2D4C8] hover:text-white hover:bg-white/5' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    <span>Analitik &amp; Tap</span>
                </a>

                <!-- 6. Pengaturan -->
                <a 
                    href="{{ route('admin.settings') }}" 
                    class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('admin.settings*') ? 'bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] font-bold' : 'text-[#E2D4C8] hover:text-white hover:bg-white/5' }}"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Card (Pinned Bottom) -->
        <div class="bg-[#240105] border border-[#520A14] rounded-2xl p-3.5 mt-8 shadow-inner shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold-accent text-[#380208] flex items-center justify-center shrink-0 font-bold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name ?? 'Baharuddin' }}</p>
                    <p class="text-[11px] text-[#C4B5A5] truncate">{{ Auth::user()->jabatan_admin ?? 'Ops Lead Makassar' }}</p>
                </div>
            </div>

            <!-- Online Status & Logout Row -->
            <div class="mt-3 pt-2.5 border-t border-[#3A040B] flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-xs text-[#E2D4C8]">
                    <span class="w-2 h-2 rounded-full bg-[#E5B32A] animate-pulse"></span>
                    <span class="text-[11px] font-medium">Online</span>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-[#D4AF37] hover:text-white flex items-center gap-1 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ========================================================= -->
    <!-- MAIN CONTENT AREA                                         -->
    <!-- ========================================================= -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen bg-cream">

        <!-- ===================================================== -->
        <!-- TOP HEADER: STICKY AT TOP ON SCROLL                   -->
        <!-- ===================================================== -->
        <header class="sticky top-0 z-20 bg-cream/95 backdrop-blur-md px-6 lg:px-9 py-4 border-b border-[#ECE8E1]/80 flex items-center justify-between transition-shadow">
            <!-- Console Fleet Path -->
            <div class="flex items-center gap-2 text-xs text-[#737373] font-medium">
                <svg class="w-4 h-4 text-[#8C6D23]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
                <span>Console</span>
                <span class="text-[#8A8A85]">›</span>
                <span class="font-bold text-maroon">Makassar Core Node</span>
                <span class="text-[#8A8A85]">›</span>
                <span>Workspace</span>
            </div>

            <!-- Right Toolbar: Regional Node Pill, Notification, Avatar -->
            <div class="flex items-center gap-3.5">
                <div class="hidden sm:inline-flex items-center gap-2 bg-[#FCF5E3] text-[#8C6D23] px-3.5 py-1.5 rounded-full text-xs font-semibold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#8C6D23] animate-pulse"></span>
                    <span>Regional Node: <strong>South Sulawesi (UPG-01)</strong> ⚡</span>
                </div>

                <!-- Notification Bell -->
                <button class="relative p-2 rounded-full text-[#4A4A4A] hover:bg-white hover:text-black transition cursor-pointer" title="Notifikasi Sistem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#8B0000] ring-2 ring-white"></span>
                </button>

                <!-- Admin Avatar Profile -->
                <div class="w-9 h-9 rounded-full bg-[#380208] text-white flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'B', 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Main Body Content Slot -->
        <main class="flex-1 p-6 lg:p-9">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
