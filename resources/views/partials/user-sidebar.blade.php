{{-- 
    FlexiTag Unified User Sidebar / Navigation Drawer Component
    Reusable partial across Dashboard, Analytics, Settings, and Mode Hilang
--}}
@php
    $sidebarUser = auth()->user();
    $sidebarProfile = $sidebarUser?->profile;
    $sidebarDisplayName = $sidebarProfile?->display_name ?? $sidebarUser?->name ?? 'Pengguna FlexiTag';
    $sidebarHeadline = $sidebarProfile?->headline ?? 'Member MaccaTech';
    $sidebarParts = explode(' ', trim($sidebarDisplayName));
    $sidebarInitials = '';
    foreach (array_slice($sidebarParts, 0, 2) as $p) {
        $sidebarInitials .= mb_substr($p, 0, 1);
    }
    $sidebarInitials = strtoupper($sidebarInitials) ?: 'FT';
@endphp

{{-- Backdrop Overlay --}}
<div class="drawer-backdrop" id="drawerBackdrop" aria-hidden="true" onclick="toggleUserSidebar(false)"></div>

{{-- Slide-Out Navigation Drawer --}}
<aside class="side-drawer user-sidebar" id="sideDrawer" aria-label="Menu Navigasi Samping" role="dialog" aria-modal="true">
    {{-- Pa'barre Allo Toraja Motif Watermark --}}
    <svg class="drawer-watermark" viewBox="0 0 100 100" fill="none" stroke="#FFFFFF" stroke-width="1.5" aria-hidden="true">
        <ellipse cx="50" cy="50" rx="40" ry="20" stroke-dasharray="3 2"/>
        <ellipse cx="50" cy="50" rx="20" ry="40" stroke-dasharray="3 2"/>
        <circle cx="50" cy="50" r="12"/>
    </svg>

    {{-- 1. Drawer Header Brand --}}
    <div class="drawer-header">
        <div class="drawer-brand-wrap">
            {{-- Sulapa Eppa' Emblem Icon --}}
            <div class="drawer-emblem" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#F5BA41" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="4"></rect>
                    <rect x="7" y="7" width="10" height="10" rx="1.5"></rect>
                    <path d="M12 7v10"></path>
                    <path d="M7 12h10"></path>
                </svg>
            </div>
            <div class="drawer-brand-text">
                <span class="drawer-brand-name">FlexiTag</span>
                <span class="drawer-brand-tagline">MENU NAVIGASI</span>
            </div>
        </div>

        <button type="button" class="drawer-close-btn" id="closeDrawerBtn" onclick="toggleUserSidebar(false)" aria-label="Tutup Menu">
            ✕
        </button>
    </div>

    <div class="drawer-divider"></div>

    {{-- 2. Navigation Menu Links --}}
    <nav class="drawer-nav-list" aria-label="Navigasi Utama Pengguna">
        {{-- Home --}}
        <a href="{{ route('user.dashboard') }}" 
           class="drawer-nav-item {{ request()->routeIs('user.dashboard') ? 'is-highlighted is-active' : '' }}"
           @if(request()->routeIs('user.dashboard')) aria-current="page" @endif>
            <span class="drawer-nav-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </span>
            <span>Home</span>
        </a>

        {{-- Merch (Katalog Publik / Modal Merch) --}}
        @if (Route::has('user.merch'))
            <a href="{{ route('user.merch') }}" 
               class="drawer-nav-item {{ request()->routeIs('user.merch') ? 'is-highlighted is-active' : '' }}"
               @if(request()->routeIs('user.merch')) aria-current="page" @endif>
                <span class="drawer-nav-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l1 12h16l1-12z"></path>
                        <path d="M3 9h18"></path>
                        <path d="M16 5a4 4 0 0 0-8 0"></path>
                    </svg>
                </span>
                <span>Merch</span>
            </a>
        @elseif (Route::has('home'))
            <a href="{{ route('home') }}" 
               class="drawer-nav-item {{ request()->routeIs('home') ? 'is-highlighted is-active' : '' }}"
               @if(request()->routeIs('home')) aria-current="page" @endif>
                <span class="drawer-nav-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l1 12h16l1-12z"></path>
                        <path d="M3 9h18"></path>
                        <path d="M16 5a4 4 0 0 0-8 0"></path>
                    </svg>
                </span>
                <span>Merch</span>
            </a>
        @endif

        {{-- Analytics --}}
        <a href="{{ route('user.analytics') }}" 
           class="drawer-nav-item {{ request()->routeIs('user.analytics') ? 'is-highlighted is-active' : '' }}"
           @if(request()->routeIs('user.analytics')) aria-current="page" @endif>
            <span class="drawer-nav-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="8" y1="17" x2="8" y2="13"></line>
                    <line x1="12" y1="17" x2="12" y2="9"></line>
                    <line x1="16" y1="17" x2="16" y2="11"></line>
                </svg>
            </span>
            <span>Analytics</span>
        </a>


        {{-- Settings --}}
        <a href="{{ route('user.settings') }}" 
           class="drawer-nav-item {{ request()->routeIs('user.settings') ? 'is-highlighted is-active' : '' }}"
           @if(request()->routeIs('user.settings')) aria-current="page" @endif>
            <span class="drawer-nav-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" y1="21" x2="4" y2="14"></line>
                    <line x1="4" y1="10" x2="4" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12" y2="3"></line>
                    <line x1="20" y1="21" x2="20" y2="16"></line>
                    <line x1="20" y1="12" x2="20" y2="3"></line>
                    <line x1="1" y1="14" x2="7" y2="14"></line>
                    <line x1="9" y1="8" x2="15" y2="8"></line>
                    <line x1="17" y1="16" x2="23" y2="16"></line>
                </svg>
            </span>
            <span>Settings</span>
        </a>
    </nav>

    {{-- 3. Drawer Bottom User Card & Log Out --}}
    <div class="drawer-footer">
        <a href="{{ route('user.settings') }}" class="drawer-profile-box" title="Buka Pengaturan Profil">
            <div class="drawer-avatar-wrap">
                @if ($sidebarProfile?->photo_path)
                    <img src="{{ asset('storage/' . $sidebarProfile->photo_path) }}" alt="{{ $sidebarDisplayName }}">
                @else
                    <span>{{ $sidebarInitials }}</span>
                @endif
                <span class="avatar-online-dot" aria-hidden="true"></span>
            </div>

            <div class="drawer-profile-info">
                <span class="drawer-user-name">{{ $sidebarDisplayName }}</span>
                <span class="drawer-user-job">{{ $sidebarHeadline }}</span>
            </div>
        </a>

        <form method="POST" action="{{ route('logout') }}" id="sidebarLogoutForm">
            @csrf
            <button type="submit" class="drawer-logout-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</aside>
