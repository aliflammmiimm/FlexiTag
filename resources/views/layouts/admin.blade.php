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

        .no-scrollbar::-webkit-scrollbar,
        .scrollbar-none::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar,
        .scrollbar-none {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-cream text-[#1A1A1A] min-h-screen flex antialiased selection:bg-[#8B0000] selection:text-[#F0C75E]">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div 
        id="mobile-sidebar-backdrop" 
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none lg:hidden" 
        onclick="toggleMobileSidebar()"
    ></div>

    <!-- ========================================================= -->
    <!-- LEFT SIDEBAR: RESPONSIVE OFF-CANVAS (MOBILE) / STICKY (DESKTOP) -->
    <!-- ========================================================= -->
    <aside 
        id="admin-sidebar" 
        class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-sidebar h-full flex flex-col justify-between p-5 overflow-y-auto shadow-2xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:sticky lg:top-0 lg:h-screen lg:shrink-0 lg:shadow-xl lg:z-30"
    >
        
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

                <div class="flex items-center gap-2">
                    <span class="bg-[#4A0A12] text-[#F0C75E] text-[9.5px] font-extrabold uppercase px-2 py-0.5 rounded-md border border-[#6E1420] shrink-0">
                        NFC PRO
                    </span>
                    <!-- Mobile Close Button -->
                    <button 
                        type="button" 
                        onclick="toggleMobileSidebar()" 
                        class="lg:hidden text-[#E2D4C8] hover:text-white p-1 rounded-lg hover:bg-white/10 transition cursor-pointer"
                        aria-label="Tutup Menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
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
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-[11px] text-[#C4B5A5] truncate">{{ Auth::user()->jabatan_admin ?? (Auth::user()->role === 'admin' ? 'Administrator' : ucfirst(Auth::user()->role)) }}</p>
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
        <header class="sticky top-0 z-20 bg-cream/95 backdrop-blur-md px-4 sm:px-6 lg:px-9 py-3 sm:py-4 border-b border-[#ECE8E1]/80 flex items-center justify-between transition-shadow">
            <!-- Left: Mobile Hamburger Toggle + Console Path -->
            <div class="flex items-center gap-2.5 sm:gap-3">
                <!-- Hamburger Button (Mobile Only) -->
                <button 
                    type="button" 
                    onclick="toggleMobileSidebar()" 
                    class="lg:hidden p-2 rounded-xl text-[#1A1A1A] hover:bg-[#F2EEE9] active:scale-95 transition cursor-pointer -ml-1"
                    aria-label="Buka Menu Navigasi"
                >
                    <svg class="w-6 h-6 text-[#380208]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <!-- Console Fleet Path -->
                <div class="flex items-center gap-1.5 sm:gap-2 text-xs text-[#737373] font-medium">
                    <svg class="w-4 h-4 text-[#8C6D23] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Console</span>
                    <span class="hidden sm:inline text-[#8A8A85]">›</span>
                    <span class="font-bold text-maroon truncate max-w-[140px] sm:max-w-none">Makassar Core Node</span>
                    <span class="hidden md:inline text-[#8A8A85]">›</span>
                    <span class="hidden md:inline">Workspace</span>
                </div>
            </div>

            <!-- Right Toolbar: Regional Node Pill, Notification, Avatar -->
            <div class="flex items-center gap-2 sm:gap-3.5">
                <div class="hidden sm:inline-flex items-center gap-2 bg-[#FCF5E3] text-[#8C6D23] px-3.5 py-1.5 rounded-full text-xs font-semibold shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-[#8C6D23] animate-pulse"></span>
                    <span>Regional Node: <strong>South Sulawesi (UPG-01)</strong> ⚡</span>
                </div>

                <!-- Notification Bell with Unread Badge (FR-ADM-09) -->
                <button 
                    type="button" 
                    onclick="toggleNotificationDrawer()" 
                    class="relative p-2 rounded-full text-[#4A4A4A] hover:bg-white hover:text-black transition cursor-pointer" 
                    title="Pusat Pesan & Laporan (FR-ADM-09)"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    @if (($unreadMessagesCount ?? 0) > 0)
                        <span class="absolute -top-0.5 -right-0.5 flex h-4 min-w-4 px-1 items-center justify-center rounded-full bg-[#8B0000] text-[9.5px] font-extrabold text-[#FAF9F6] ring-2 ring-white animate-pulse">
                            {{ $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount }}
                        </span>
                    @endif
                </button>

                <!-- Admin Avatar Profile -->
                <div class="w-9 h-9 rounded-full bg-[#380208] text-white flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Main Body Content Slot -->
        <main class="flex-1 p-4 sm:p-6 lg:p-9 max-w-full overflow-x-hidden">
            @yield('content')
        </main>
    </div>

    <!-- ========================================================= -->
    <!-- SLIDE-OVER DRAWER: PUSAT PESAN & LAPORAN (FR-ADM-09)      -->
    <!-- ========================================================= -->
    <div 
        id="notification-drawer-backdrop" 
        class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs transition-opacity duration-300 opacity-0 pointer-events-none" 
        onclick="toggleNotificationDrawer()"
    ></div>

    <aside 
        id="notification-drawer" 
        class="fixed inset-y-0 right-0 z-50 w-full sm:w-[460px] bg-white shadow-2xl border-l border-[#ECE8E1] flex flex-col transform translate-x-full transition-transform duration-300 ease-in-out"
    >
        <!-- Drawer Header -->
        <div class="p-5 border-b border-[#ECE8E1] bg-[#FAF9F6] flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-[#FCF5E3] text-[#8C6D23] flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#1A1A1A]">Pusat Pesan &amp; Laporan</h3>
                    <p class="text-[10px] text-[#737373]">Notifikasi insiden dan formulir publik (FR-ADM-09)</p>
                </div>
            </div>

            <div class="flex items-center gap-1.5">
                @if (($unreadMessagesCount ?? 0) > 0)
                    <form action="{{ route('admin.messages.readAll') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[10px] font-semibold text-[#8B0000] hover:underline px-2.5 py-1 rounded-full bg-[#FFF0F0] border border-[#FFD6D6] cursor-pointer">
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif
                <button type="button" onclick="toggleNotificationDrawer()" class="p-1.5 rounded-full text-[#8A8A85] hover:text-[#1A1A1A] hover:bg-[#F2EEE9] transition cursor-pointer">
                    ✕
                </button>
            </div>
        </div>

        <!-- Filter Tabs: 1. Semua, 2. Laporan Abuse, 3. Pesan Kontak -->
        <div class="p-3 border-b border-[#ECE8E1] bg-white flex gap-1.5 text-xs font-semibold">
            <button type="button" onclick="filterDrawerTab('all')" id="tab-btn-all" class="drawer-tab-btn flex-1 py-1.5 px-3 rounded-full text-center transition bg-[#380208] text-white">
                Semua
            </button>
            <button type="button" onclick="filterDrawerTab('abuse_report')" id="tab-btn-abuse_report" class="drawer-tab-btn flex-1 py-1.5 px-3 rounded-full text-center transition bg-[#F6F5F2] text-[#737373] hover:text-[#1A1A1A]">
                Laporan Abuse
            </button>
            <button type="button" onclick="filterDrawerTab('contact')" id="tab-btn-contact" class="drawer-tab-btn flex-1 py-1.5 px-3 rounded-full text-center transition bg-[#F6F5F2] text-[#737373] hover:text-[#1A1A1A]">
                Pesan Kontak
            </button>
        </div>

        <!-- Messages List Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3" id="drawer-messages-list">
            @forelse (($adminNotifications ?? []) as $msg)
                <div class="drawer-msg-item p-4 rounded-2xl border transition-all {{ is_null($msg->read_at) ? 'bg-[#FCFBF8] border-[#E8DFC9] shadow-xs' : 'bg-white border-[#ECE8E1]' }}" data-type="{{ $msg->type }}">
                    
                    <!-- Header card -->
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider {{ $msg->type === 'abuse_report' ? 'bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA]' : 'bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD]' }}">
                            {{ $msg->type === 'abuse_report' ? '🚨 Laporan Abuse' : '✉️ Pesan Kontak' }}
                        </span>
                        <span class="text-[10px] text-[#8A8A85] font-normal">
                            {{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Sender Info -->
                    <p class="text-xs font-bold text-[#1A1A1A] mb-1">
                        {{ $msg->sender_name ?? 'Pengirim Anonim' }}
                        @if ($msg->location)
                            <span class="text-[10px] font-normal text-[#737373]">({{ $msg->location }})</span>
                        @endif
                    </p>

                    <!-- Message Body -->
                    <p class="text-xs text-[#4A4A4A] leading-relaxed mb-3 bg-[#F8F7F4] p-3 rounded-xl border border-[#ECE8E1]/70">
                        {{ $msg->message }}
                    </p>

                    @if ($msg->contact)
                        <div class="text-[11px] text-[#737373] mb-3 flex items-center gap-1.5">
                            <span class="font-semibold text-[#1A1A1A]">Kontak Balik:</span>
                            <span class="font-mono text-maroon font-bold">{{ $msg->contact }}</span>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="flex items-center justify-between pt-2.5 border-t border-[#F2EEE9] text-xs">
                        <div class="flex items-center gap-2">
                            @if ($msg->type === 'abuse_report')
                                <form action="{{ route('admin.messages.deactivateTag', $msg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan chip NFC terkait laporan ini demi keamanan?')">
                                    @csrf
                                    <button type="submit" class="bg-[#8B0000] hover:bg-[#680000] text-[#D4AF37] font-bold text-[10.5px] px-3 py-1 rounded-full transition cursor-pointer shadow-2xs">
                                        Nonaktifkan Tag
                                    </button>
                                </form>
                            @endif

                            @if ($msg->contact)
                                @php
                                    $isPhone = preg_match('/^[0-9\+\s\-]+$/', $msg->contact);
                                    $cleanedPhone = preg_replace('/[^0-9]/', '', $msg->contact);
                                    if (str_starts_with($cleanedPhone, '0')) {
                                        $cleanedPhone = '62' . substr($cleanedPhone, 1);
                                    }
                                @endphp
                                @if ($isPhone)
                                    <a href="https://wa.me/{{ $cleanedPhone }}" target="_blank" class="bg-[#25D366] hover:bg-[#1EBE5D] text-white font-bold text-[10.5px] px-3 py-1 rounded-full transition inline-flex items-center gap-1 shadow-2xs">
                                        Balas WA ↗
                                    </a>
                                @elseif (filter_var($msg->contact, FILTER_VALIDATE_EMAIL))
                                    <a href="mailto:{{ $msg->contact }}" class="bg-[#1E70A0] hover:bg-[#155982] text-white font-bold text-[10.5px] px-3 py-1 rounded-full transition inline-flex items-center gap-1 shadow-2xs">
                                        Balas Email ↗
                                    </a>
                                @endif
                            @endif
                        </div>

                        <div>
                            @if (is_null($msg->read_at))
                                <form action="{{ route('admin.messages.read', $msg->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-[#737373] hover:text-[#1A1A1A] font-semibold underline cursor-pointer">
                                        Tandai Dibaca
                                    </button>
                                </form>
                            @else
                                <span class="text-[10px] text-[#2E7D32] font-semibold">✓ Telah Dibaca</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-xs text-[#8A8A85]">
                    <div class="w-10 h-10 rounded-full bg-[#FCF5E3] text-[#8C6D23] flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="font-bold text-[#1A1A1A]">Semua Bersih</p>
                    <p class="mt-0.5">Tidak ada laporan abuse atau pesan masuk yang tertunda.</p>
                </div>
            @endforelse
        </div>
    </aside>

    <script>
        function toggleNotificationDrawer() {
            const backdrop = document.getElementById('notification-drawer-backdrop');
            const drawer = document.getElementById('notification-drawer');
            if (!drawer || !backdrop) return;

            const isClosed = drawer.classList.contains('translate-x-full');
            if (isClosed) {
                backdrop.classList.remove('pointer-events-none', 'opacity-0');
                backdrop.classList.add('opacity-100');
                drawer.classList.remove('translate-x-full');
            } else {
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.add('translate-x-full');
            }
        }

        function filterDrawerTab(type) {
            const items = document.querySelectorAll('.drawer-msg-item');
            items.forEach(item => {
                if (type === 'all' || item.getAttribute('data-type') === type) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });

            document.querySelectorAll('.drawer-tab-btn').forEach(btn => {
                btn.classList.remove('bg-[#380208]', 'text-white');
                btn.classList.add('bg-[#F6F5F2]', 'text-[#737373]');
            });

            const activeBtn = document.getElementById('tab-btn-' + type);
            if (activeBtn) {
                activeBtn.classList.remove('bg-[#F6F5F2]', 'text-[#737373]');
                activeBtn.classList.add('bg-[#380208]', 'text-white');
            }
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            if (!sidebar || !backdrop) return;

            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('pointer-events-none', 'opacity-0');
                backdrop.classList.add('opacity-100');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('admin-sidebar');
                const backdrop = document.getElementById('mobile-sidebar-backdrop');
                if (sidebar && backdrop) {
                    backdrop.classList.remove('opacity-100');
                    backdrop.classList.add('opacity-0', 'pointer-events-none');
                    document.body.classList.remove('overflow-hidden');
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
