<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ringkasan Operasional — Admin Console FlexiTag</title>

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
</head>
<body class="bg-cream text-[#1A1A1A] min-h-screen flex antialiased selection:bg-[#8B0000] selection:text-[#F0C75E]">

    <!-- ========================================================= -->
    <!-- LEFT SIDEBAR (DEEP MAROON)                                -->
    <!-- ========================================================= -->
    <aside class="w-64 sm:w-72 bg-sidebar shrink-0 flex flex-col justify-between p-5 min-h-screen sticky top-0 text-white z-30">
        
        <div>
            <!-- Sidebar Header: FlexiTag Logo -->
            <div class="flex items-center gap-3 px-2 py-3 mb-6">
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

            <!-- Navigation Section Heading -->
            <div class="px-3 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-[#C49A5E]">
                    Navigasi Konsol
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5 text-xs font-semibold">
                
                <!-- 1. Dashboard (ACTIVE) -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-gold-accent text-[#380208] shadow-[0_4px_12px_rgba(240,199,94,0.3)] transition font-bold">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Generate Tag -->
                <a href="javascript:void(0)" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#E2D4C8] hover:text-white hover:bg-white/5 transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                    <span>Generate Tag</span>
                </a>

                <!-- 3. Daftar Tag -->
                <a href="javascript:void(0)" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#E2D4C8] hover:text-white hover:bg-white/5 transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75zM6.75 16.5h.75v.75h-.75zM16.5 6.75h.75v.75h-.75zM13.5 13.5h3v3h-3zM16.5 16.5h3v3h-3zM19.5 13.5h.75v.75h-.75zM13.5 19.5h.75v.75h-.75z"/>
                    </svg>
                    <span>Daftar Tag</span>
                </a>

                <!-- 4. Daftar Pesanan -->
                <a href="javascript:void(0)" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#E2D4C8] hover:text-white hover:bg-white/5 transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <span>Daftar Pesanan</span>
                </a>

                <!-- 5. Analitik & Tap -->
                <a href="javascript:void(0)" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#E2D4C8] hover:text-white hover:bg-white/5 transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    <span>Analitik &amp; Tap</span>
                </a>

                <!-- 6. Pengaturan -->
                <a href="javascript:void(0)" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-[#E2D4C8] hover:text-white hover:bg-white/5 transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>Pengaturan</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Card (Pinned Bottom) -->
        <div class="bg-[#240105] border border-[#520A14] rounded-2xl p-3.5 mt-8 shadow-inner">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold-accent text-[#380208] flex items-center justify-center shrink-0 font-bold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-white truncate">{{ $user->name ?? 'Saparudding' }}</p>
                    <p class="text-[11px] text-[#C4B5A5] truncate">{{ $user->jabatan_admin ?? 'Ops Lead Makassar' }}</p>
                </div>
            </div>

            <!-- Logout Button -->
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="w-full bg-[#180003] hover:bg-[#300008] active:scale-[0.98] text-[#D4AF37] text-xs font-semibold py-2 px-3 rounded-xl border border-[#48030C] flex items-center justify-center gap-1.5 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ========================================================= -->
    <!-- MAIN CONTENT AREA                                         -->
    <!-- ========================================================= -->
    <main class="flex-1 min-w-0 p-6 lg:p-9 overflow-y-auto">

        <!-- Top Header Status Bar -->
        <div class="flex items-center justify-between pb-6 mb-6 border-b border-[#ECE8E1]/80">
            <!-- Node & Region Breadcrumb -->
            <div class="flex items-center gap-2 text-[11px] font-semibold tracking-wider text-[#5C5549] uppercase">
                <span class="w-2 h-2 rounded-full bg-[#4E6333]"></span>
                <span>Regional Node: <strong class="text-[#1A1A1A]">Makassar Core-01</strong></span>
                <span class="text-[#A0988A]">•</span>
                <span>Sulawesi Selatan</span>
            </div>

            <!-- Notification & Avatar -->
            <div class="flex items-center gap-4">
                <button class="relative p-2 rounded-full text-[#4A4A4A] hover:bg-white hover:text-black transition cursor-pointer" title="Notifikasi Sistem">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#8B0000] ring-2 ring-white"></span>
                </button>

                <!-- Admin Avatar Profile -->
                <div class="w-9 h-9 rounded-full bg-[#380208] text-white flex items-center justify-center font-bold text-xs ring-2 ring-white shadow-sm">
                    {{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}
                </div>
            </div>
        </div>

        <!-- Page Title & Action Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
            <div>
                <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                    Ringkasan Operasional
                </h2>
                <p class="text-xs text-[#737373] mt-1 font-normal">
                    Pemantauan ekosistem perangkat NFC FlexiTag dan integrasi digital Sulawesi Selatan.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <!-- Date Filter Pill -->
                <div class="bg-white border border-[#E8E4DC] rounded-full px-4 py-2 text-xs font-medium text-[#1A1A1A] shadow-sm flex items-center gap-2 cursor-pointer hover:border-[#D4AF37] transition">
                    <svg class="w-4 h-4 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                    </svg>
                    <span>Pekan Ini (12 – 18 Mei 2025)</span>
                </div>

                <!-- Unduh Ringkasan Button -->
                <button onclick="window.print()" class="bg-gold-accent hover:bg-[#E5BC50] active:scale-95 text-[#1A1A1A] font-semibold text-xs px-4 py-2.5 rounded-full shadow-sm flex items-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    <span>Unduh Ringkasan</span>
                </button>
            </div>
        </div>

        <!-- ========================================================= -->
        <!-- 4 TOP METRIC CARDS                                        -->
        <!-- ========================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-7">

            <!-- Card 1: TOTAL PENDAPATAN -->
            <div class="bg-white rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Total Pendapatan
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-[#F0EBE1] text-[#7A735E] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['total_pendapatan'] }}
                    </h3>
                    <p class="text-xs font-semibold text-[#2E7D32] flex items-center gap-1 mt-1">
                        <span>📈 {{ $metrics['growth_pendapatan'] }}</span>
                        <span class="text-[#737373] font-normal">vs pekan lalu</span>
                    </p>
                </div>
            </div>

            <!-- Card 2: TOTAL PESANAN -->
            <div class="bg-white rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Total Pesanan
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-[#F7EBEB] text-[#8B3A3A] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['total_pesanan'] }}
                    </h3>
                    <p class="text-xs font-semibold text-[#1A1A1A] flex items-center gap-1 mt-1">
                        <span>↑ {{ $metrics['new_orders'] }}</span>
                    </p>
                </div>
            </div>

            <!-- Card 3: TAG AKTIF -->
            <div class="bg-white rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Tag Aktif
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                            <circle cx="6" cy="6" r="1"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['tag_aktif'] }}
                    </h3>
                    <p class="text-xs text-[#555] flex items-center gap-1.5 mt-1 font-medium">
                        <span class="w-2 h-2 rounded-full bg-[#7F6B2A]"></span>
                        <span>{{ $metrics['rasio_aktif'] }} rasio tap aktif</span>
                    </p>
                </div>
            </div>

            <!-- Card 4: PERLU DIKIRIM -->
            <div class="bg-white rounded-2xl p-5 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold tracking-wider text-[#8A8A85] uppercase">
                        Perlu Dikirim
                    </span>
                    <div class="w-8 h-8 rounded-lg bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25H4.5a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h1.5m8.25-11.25V16.5"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl font-bold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['perlu_dikirim'] }}
                    </h3>
                    <p class="text-xs font-semibold text-[#8B0000] flex items-center gap-1 mt-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
                        </svg>
                        <span>Siap pickup kurir</span>
                    </p>
                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- MIDDLE ROW: WEEKLY CHART + CUSTOMER MODE DISTRIBUTION     -->
        <!-- ========================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-7">

            <!-- LEFT: Tren Penjualan & Aktivasi Mingguan (Col 7) -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <!-- Card Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-[#1A1A1A]">
                                Tren Penjualan &amp; Aktivasi Mingguan
                            </h3>
                            <p class="text-xs text-[#737373] mt-0.5 font-normal">
                                Perbandingan volume transaksi produk fisik vs aktivasi digital tap
                            </p>
                        </div>

                        <!-- Legend -->
                        <div class="flex items-center gap-4 text-xs font-medium shrink-0">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-xs bg-[#540404]"></span>
                                <span class="text-[#4A4A4A]">Penjualan Fisik</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-xs bg-[#7F6B2A]"></span>
                                <span class="text-[#4A4A4A]">Aktivasi Tap</span>
                            </div>
                        </div>
                    </div>

                    <!-- Paired Bar Chart Visualization -->
                    <div class="pt-6 pb-2">
                        <div class="h-44 sm:h-48 flex items-end justify-between gap-2 sm:gap-6 px-2 sm:px-4 border-b border-[#F0ECE7]">
                            @foreach ($weeklyTrends as $trend)
                                <div class="flex flex-col items-center flex-1 h-full justify-end relative group">
                                    
                                    <!-- Peak Badge on Friday (Jum) -->
                                    @if ($trend['is_peak'])
                                        <div class="absolute -top-7 z-10">
                                            <span class="bg-[#F0C75E] text-[#380208] text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full shadow-sm">
                                                Puncak
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Bars Container -->
                                    <div class="flex items-end gap-1 sm:gap-1.5 w-full justify-center">
                                        <!-- Penjualan Bar (Maroon) -->
                                        <div 
                                            class="w-3 sm:w-4 bg-[#540404] rounded-t-xs hover:brightness-125 transition-all duration-300"
                                            style="height: {{ $trend['penjualan'] * 1.8 }}px;"
                                            title="{{ $trend['day'] }} - Penjualan: {{ $trend['penjualan'] }}"
                                        ></div>

                                        <!-- Aktivasi Bar (Olive/Gold) -->
                                        <div 
                                            class="w-3 sm:w-4 bg-[#7F6B2A] rounded-t-xs hover:brightness-125 transition-all duration-300"
                                            style="height: {{ $trend['aktivasi'] * 1.8 }}px;"
                                            title="{{ $trend['day'] }} - Aktivasi: {{ $trend['aktivasi'] }}"
                                        ></div>
                                    </div>

                                    <!-- Day Label -->
                                    <span class="text-[11px] mt-2 font-medium {{ $trend['is_peak'] ? 'font-bold text-[#540404]' : 'text-[#737373]' }}">
                                        {{ $trend['day'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Footer Summary Info -->
                <div class="border-t border-[#F2EEE9] pt-4 mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-1.5 text-[#737373]">
                        <svg class="w-4 h-4 text-[#4E6333]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                        <span>Sinkronisasi otomatis dengan Node Makassar Core-01</span>
                    </div>

                    <div class="font-bold text-[#1A1A1A]">
                        Rerata 24 tap / perangkat aktif
                    </div>
                </div>
            </div>

            <!-- RIGHT: Distribusi Mode Pelanggan (Col 5) -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-base font-bold text-[#1A1A1A]">
                                Distribusi Mode Pelanggan
                            </h3>
                            <p class="text-xs text-[#737373] mt-0.5 font-normal">
                                Preferensi fungsional chip NTAG215 pelanggan aktif
                            </p>
                        </div>

                        <!-- Sliders Filter Icon -->
                        <button class="text-[#8A8A85] hover:text-[#1A1A1A] p-1.5 transition rounded-lg hover:bg-[#F6F5F2]" title="Filter Mode">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Mode Bars List -->
                    <div class="space-y-4">
                        @foreach ($modeDistribution as $mode)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $mode['bg_dot'] }}"></span>
                                        <span class="font-semibold text-[#1A1A1A]">{{ $mode['name'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="bg-[#F6F5F2] text-[#666666] text-[11px] font-medium px-2.5 py-0.5 rounded-full">
                                            {{ $mode['count'] }} Pengguna
                                        </span>
                                        <span class="font-bold text-xs text-[#1A1A1A] w-7 text-right">
                                            {{ $mode['percentage'] }}%
                                        </span>
                                    </div>
                                </div>

                                <!-- Progress Track -->
                                <div class="w-full bg-[#F0ECE7] h-2 rounded-full overflow-hidden">
                                    <div 
                                        class="{{ $mode['bg_bar'] }} h-full rounded-full transition-all duration-500" 
                                        style="width: {{ $mode['percentage'] }}%;"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Mode Switch Notice Banner -->
                <div class="bg-[#F6F5F2] rounded-2xl p-3.5 flex items-start gap-2.5 mt-6 border border-[#EFECE6]/70">
                    <svg class="w-4 h-4 text-[#8A8A85] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p class="text-[11px] text-[#737373] leading-relaxed font-normal">
                        Aktivasi mode dapat dialihkan mandiri oleh pemegang kartu via tap.
                    </p>
                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- BOTTOM SECTION: PESANAN TERBARU (5 TERAKHIR)              -->
        <!-- ========================================================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">
            
            <!-- Table Section Header -->
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">
                        Pesanan Terbaru (5 Terakhir)
                    </h3>
                    <p class="text-xs text-[#737373] mt-0.5 font-normal">
                        Transaksi fisik yang baru diproses di Sulawesi Selatan
                    </p>
                </div>

                <a href="javascript:void(0)" class="text-xs font-semibold text-maroon hover:text-[#5A0000] flex items-center gap-1 transition">
                    <span>Lihat Semua</span>
                    <span class="text-sm">→</span>
                </a>
            </div>

            <!-- Table Container -->
            <div class="overflow-x-auto rounded-2xl border border-[#ECE8E1]/80">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#FAF8F5] text-[#8A8A85] text-[10.5px] font-bold uppercase tracking-wider border-b border-[#ECE8E1]">
                            <th class="py-3.5 px-5">ID Pesanan</th>
                            <th class="py-3.5 px-5">Nama Pembeli</th>
                            <th class="py-3.5 px-5">Produk</th>
                            <th class="py-3.5 px-5">Status Pengiriman</th>
                            <th class="py-3.5 px-5">Status Tag</th>
                            <th class="py-3.5 px-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F2EEE9]">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-[#FAF9F6] transition-colors">
                                <!-- ID Pesanan -->
                                <td class="py-4 px-5 font-bold text-maroon">
                                    {{ $order['id_pesanan'] }}
                                </td>

                                <!-- Nama Pembeli -->
                                <td class="py-4 px-5">
                                    <div class="font-bold text-[#1A1A1A]">{{ $order['nama_pembeli'] }}</div>
                                    <div class="text-[11px] text-[#737373] font-normal">({{ $order['kota'] }})</div>
                                </td>

                                <!-- Produk dengan Ikon -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2.5 font-medium text-[#1A1A1A]">
                                        @if ($order['icon_type'] === 'tag')
                                            <!-- Tag Icon -->
                                            <svg class="w-4 h-4 text-[#737373] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                                                <circle cx="6" cy="6" r="1"/>
                                            </svg>
                                        @elseif ($order['icon_type'] === 'bracelet')
                                            <!-- Bracelet / Watch Icon -->
                                            <svg class="w-4 h-4 text-[#737373] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="7"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3l2 1M9 2h6M9 22h6"/>
                                            </svg>
                                        @elseif ($order['icon_type'] === 'card')
                                            <!-- Card Icon -->
                                            <svg class="w-4 h-4 text-[#737373] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <rect x="3" y="6" width="18" height="12" rx="2"/>
                                                <line x1="3" y1="10" x2="21" y2="10"/>
                                            </svg>
                                        @elseif ($order['icon_type'] === 'key')
                                            <!-- Key Icon -->
                                            <svg class="w-4 h-4 text-[#737373] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                            </svg>
                                        @else
                                            <!-- Lanyard Icon -->
                                            <svg class="w-4 h-4 text-[#737373] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v10m-4-7l4 7 4-7M8 17h8a2 2 0 012 2v2H6v-2a2 2 0 012-2z"/>
                                            </svg>
                                        @endif
                                        <span>{{ $order['produk'] }}</span>
                                    </div>
                                </td>

                                <!-- Status Pengiriman -->
                                <td class="py-4 px-5">
                                    @if ($order['status_pengiriman'] === 'dikirim')
                                        <span class="inline-flex items-center gap-1.5 bg-[#FDF3D6] text-[#B8860B] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#B8860B]"></span>
                                            Dikirim
                                        </span>
                                    @elseif ($order['status_pengiriman'] === 'pending')
                                        <span class="inline-flex items-center gap-1.5 bg-[#F0ECE7] text-[#736B5E] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#736B5E]"></span>
                                            Pending
                                        </span>
                                    @elseif ($order['status_pengiriman'] === 'selesai')
                                        <span class="inline-flex items-center gap-1.5 bg-[#FBE8E8] text-maroon font-semibold text-[11px] px-3 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-maroon"></span>
                                            Selesai
                                        </span>
                                    @endif
                                </td>

                                <!-- Status Tag -->
                                <td class="py-4 px-5">
                                    @if ($order['status_tag'] === 'aktif')
                                        <span class="inline-flex items-center gap-1 bg-[#EAF5EC] text-[#2E7D32] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 bg-[#F0ECE7] text-[#736B5E] font-semibold text-[11px] px-3 py-1 rounded-full">
                                            <span class="w-2 h-2 rounded-full border border-current"></span>
                                            Belum Aktif
                                        </span>
                                    @endif
                                </td>

                                <!-- Aksi -->
                                <td class="py-4 px-5 text-center">
                                    <button class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] font-semibold text-[11px] px-3.5 py-1 rounded-full border border-[#E5E0D8] transition cursor-pointer">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </main>

</body>
</html>
