@extends('layouts.app')

@section('title', $title ?? 'Tag Tidak Ditemukan — FlexiTag')
@section('meta_description', 'Status aktivasi dan ketersediaan tag identitas digital FlexiTag.')

@section('content')
<main id="main-content" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem;">
    <div class="ft-card ft-card-accent-maroon" style="max-width: 480px; width: 100%; text-align: center;">
        <div class="ft-card-body" style="padding: 2.5rem 1.5rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background-color: rgba(139, 0, 0, 0.08); color: var(--ft-maroon); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                @if(($status ?? '') === 'belum_aktif')
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                @elseif(($status ?? '') === 'nonaktif')
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                    </svg>
                @else
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                @endif
            </div>

            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--ft-charcoal); margin-bottom: 0.75rem;">
                {{ $heading ?? 'Tag Belum Tersedia' }}
            </h1>

            <p style="font-size: 0.95rem; color: var(--ft-muted); line-height: 1.6; margin-bottom: 2rem;">
                {{ $message ?? 'Informasi identitas digital untuk tag ini tidak ditemukan atau belum dipublikasikan oleh pemilik.' }}
            </p>

            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @if(($status ?? '') === 'belum_aktif')
                    <a href="{{ route('login') }}" class="ft-btn ft-btn-primary" style="justify-content: center;">
                        Aktivasi Tag Ini Sekarang
                    </a>
                @endif
                <a href="{{ route('landing') }}" class="ft-btn ft-btn-secondary" style="justify-content: center;">
                    Kembali ke Beranda FlexiTag
                </a>
            </div>
        </div>
        <div class="ft-card-footer" style="text-align: center;">
            <span style="font-size: 0.75rem; color: var(--ft-muted);">FlexiTag Smart Identity &bull; MaccaTech</span>
        </div>
    </div>
</main>
@endsection
