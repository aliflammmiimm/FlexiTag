@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
    @php
        $statusMap = [
            'aktif' => ['Aktif', 'badge-ok'],
            'nonaktif' => ['Nonaktif', 'badge-bad'],
            'belum_aktif' => ['Belum aktif', 'badge-idle'],
        ];
    @endphp

    <h1>Halo, {{ $user->profile?->display_name ?? $user->name }}</h1>
    <p class="section-sub" style="margin-top:0">Ringkasan akun dan tag NFC Anda.</p>

    <div class="grid grid-3" style="margin:1.4rem 0;">
        <div class="card stat">
            <div class="ico ico-maroon">@include('partials.icon', ['name' => 'zap'])</div>
            <div>
                <div class="num">{{ number_format($totalTaps, 0, ',', '.') }}</div>
                <div class="lbl">Total kunjungan profil</div>
            </div>
        </div>

        <div class="card stat">
            <div class="ico ico-maroon">@include('partials.icon', ['name' => 'tag'])</div>
            <div>
                <div class="num">{{ $activeTagCount }}</div>
                <div class="lbl">Tag aktif</div>
            </div>
        </div>

        <div class="card stat">
            <div class="ico ico-maroon">@include('partials.icon', ['name' => 'sliders'])</div>
            <div>
                <div class="num" style="font-size:1.25rem;">{{ $activeModeLabel ?? '—' }}</div>
                <div class="lbl">Mode profil aktif</div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 style="font-size:1.25rem;">Tag NFC Anda</h2>

        @if ($tags->isEmpty())
            <div class="empty">Anda belum memiliki tag yang terhubung ke akun ini.</div>
        @else
            <div class="tbl-wrap"><table class="tbl">
                <thead>
                    <tr>
                        <th>Kode tag</th>
                        <th>Status</th>
                        <th>Diaktifkan</th>
                        <th>Tautan profil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                        @php [$label, $class] = $statusMap[$tag->status] ?? [$tag->status, 'badge-idle']; @endphp
                        <tr>
                            <td><code>{{ $tag->tag_code }}</code></td>
                            <td><span class="badge {{ $class }}">{{ $label }}</span></td>
                            <td>{{ $tag->activated_at?->timezone(config('app.timezone'))->format('d M Y, H:i') ?? '—' }}</td>
                            <td>{{ $tag->public_url }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </div>
@endsection
