@extends('layouts.admin')

@section('title', 'Manajemen Pesanan & Tag')

@section('content')
    <!-- Page Title & Search Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 mb-7">
        <div>
            <h2 class="text-2xl sm:text-[26px] font-bold text-[#1A1A1A] tracking-tight">
                Manajemen Pesanan &amp; Tag
            </h2>
            <p class="text-xs text-[#737373] mt-1 font-normal max-w-xl leading-relaxed">
                Kelola distribusi gantungan kunci (ganci) pintar NFC — variasi bentuk bulat, persegi, motif Toraja, motif Bugis, hingga custom request.
            </p>
        </div>

        <!-- Filter & Action Toolbar -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <!-- Search Input Form -->
            <form action="{{ route('admin.orders') }}" method="GET" class="relative">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <svg class="w-4 h-4 text-[#8A8A85] absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Cari ID pesanan atau nama pembeli..." 
                    class="w-60 sm:w-68 bg-white border border-[#ECE8E1] rounded-2xl pl-10 pr-4 py-2 text-xs text-[#1A1A1A] placeholder-[#9E9E98] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] shadow-xs transition"
                >
            </form>

            <!-- Status Dropdown Filter -->
            <div class="relative">
                <button 
                    type="button" 
                    onclick="toggleOrderStatusDropdown()" 
                    class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] rounded-2xl px-3.5 py-2 text-xs font-semibold text-[#1A1A1A] flex items-center gap-2 shadow-xs transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18m-14 7.5h10m-7 7.5h4"/>
                    </svg>
                    <span>{{ $statusFilter === 'perlu_dikirim' ? 'Status: Pending' : ($statusFilter === 'terkirim' ? 'Status: Terkirim' : 'Semua Status') }}</span>
                    <svg class="w-3 h-3 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div id="order-status-dropdown" class="hidden absolute right-0 mt-1.5 w-44 bg-white rounded-2xl shadow-xl border border-[#ECE8E1] py-1.5 z-40 text-xs">
                    <a href="{{ route('admin.orders', ['status' => 'all', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'all' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Semua Status</a>
                    <a href="{{ route('admin.orders', ['status' => 'perlu_dikirim', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'perlu_dikirim' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Perlu Dikirim (Pending)</a>
                    <a href="{{ route('admin.orders', ['status' => 'terkirim', 'search' => $search]) }}" class="block px-4 py-2 hover:bg-[#F6F5F2] {{ $statusFilter === 'terkirim' ? 'font-bold text-maroon' : 'text-[#4A4A4A]' }}">Terkirim &amp; Selesai</a>
                </div>
            </div>

            <!-- Export Data (CSV) Button -->
            <button 
                type="button" 
                onclick="alert('Mengekspor data pesanan ganci NFC ke file CSV...')" 
                class="bg-white hover:bg-[#F9F7F4] border border-[#ECE8E1] rounded-2xl px-4 py-2 text-xs font-semibold text-[#1A1A1A] flex items-center gap-1.5 shadow-xs transition cursor-pointer"
            >
                <svg class="w-3.5 h-3.5 text-[#737373]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                <span>Export Data (CSV)</span>
            </button>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- TOP 3 METRIC CARDS                                        -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">

        <!-- Card 1: INVENTARIS HARDWARE -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                    Inventaris Hardware
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['total_hardware'] }}
                    </h3>
                    <span class="text-xs font-medium text-[#737373]">Total Tag</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#F7EBEB] text-[#8B3A3A] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="4" y="4" width="16" height="16" rx="2"/>
                    <rect x="9" y="9" width="6" height="6"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: AKTIVASI PELANGGAN -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                    Aktivasi Pelanggan
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['tag_aktif'] }}
                    </h3>
                    <span class="text-xs font-medium text-[#737373]">Tag Aktif</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.53 16.11a6 6 0 0 1 6.95 0m-9.5-3.56a11 11 0 0 1 12.08 0M12 20h.01"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: ANTREAN PEMENUHAN -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                    Antrean Pemenuhan
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['perlu_dikirim'] }}
                    </h3>
                    <span class="text-xs font-medium text-[#737373]">Perlu Dikirim</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25H4.5a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h1.5m8.25-11.25V16.5"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- MAIN ORDERS TABLE CARD                                    -->
    <!-- ========================================================= -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#ECE8E1] shadow-[0_2px_15px_rgba(0,0,0,0.02)]">

        <!-- Header: Title + Status Filter Tabs -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-[#ECE8E1]/80 mb-6">
            <div>
                <h3 class="text-base font-bold text-[#1A1A1A]">
                    Daftar Pesanan &amp; Status NFC Tag
                </h3>
                <p class="text-xs text-[#737373] mt-0.5 font-normal">
                    Pantau status sinkronisasi chip NTAG215 dan pengiriman merchandise gantungan kunci (ganci).
                </p>
            </div>

            <!-- Filter Status Tabs -->
            <div class="inline-flex items-center bg-[#F6F5F2] p-1 rounded-full text-xs font-semibold gap-1 shrink-0">
                <a 
                    href="{{ route('admin.orders', ['status' => 'all', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'all' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Semua ({{ $metrics['total_pesanan'] }})
                </a>
                <a 
                    href="{{ route('admin.orders', ['status' => 'perlu_dikirim', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'perlu_dikirim' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Perlu Dikirim ({{ $metrics['perlu_dikirim'] }})
                </a>
                <a 
                    href="{{ route('admin.orders', ['status' => 'terkirim', 'search' => $search]) }}" 
                    class="px-4 py-2 rounded-full transition {{ $statusFilter === 'terkirim' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Terkirim ({{ $metrics['terkirim'] }})
                </a>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] font-bold uppercase tracking-wider text-[#8A8A85] border-b border-[#ECE8E1]">
                        <th class="pb-3.5 px-3">ID Pesanan</th>
                        <th class="pb-3.5 px-3">Nama Pembeli</th>
                        <th class="pb-3.5 px-3">Jenis Produk (Ganci)</th>
                        <th class="pb-3.5 px-3">Status Tag</th>
                        <th class="pb-3.5 px-3">Pengiriman</th>
                        <th class="pb-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EEE9]">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-[#FAF9F6] transition-colors">
                            
                            <!-- 1. ID Pesanan -->
                            <td class="py-4 px-3 font-bold text-xs text-[#1A1A1A] whitespace-nowrap">
                                {{ $order['id_pesanan'] }}
                            </td>

                            <!-- 2. Nama Pembeli & Kota -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="font-bold text-xs text-[#1A1A1A]">{{ $order['nama_pembeli'] }}</span>
                                <span class="text-[11px] text-[#737373] font-normal ml-1">({{ $order['kota'] }})</span>
                            </td>

                            <!-- 3. Jenis Produk (Exclusively Ganci Variants) -->
                            <td class="py-4 px-3">
                                <div class="font-medium text-xs text-[#1A1A1A] flex items-center gap-2">
                                    <!-- Key / Ganci Icon -->
                                    <svg class="w-3.5 h-3.5 text-[#8A8A85] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                    </svg>
                                    <span>{{ $order['jenis_produk'] }}</span>
                                </div>
                            </td>

                            <!-- 4. Status Tag -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($order['status_tag'] === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 bg-[#EAF5EC] text-[#2E7D32] font-bold text-[11px] px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#2E7D32]"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#F0ECE7] text-[#736B5E] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#736B5E]"></span>
                                        Belum Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- 5. Status Pengiriman -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                @if ($order['pengiriman'] === 'dikirim')
                                    <span class="inline-flex items-center gap-1.5 bg-[#E6F3FA] text-[#1E70A0] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        Dikirim
                                    </span>
                                @elseif ($order['pengiriman'] === 'selesai')
                                    <span class="inline-flex items-center gap-1.5 bg-[#EAF5EC] text-[#2E7D32] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-[#FEF5E7] text-[#A86414] font-semibold text-[11px] px-3 py-1 rounded-full">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <!-- 6. Aksi: Detail Button -->
                            <td class="py-4 px-3 text-right whitespace-nowrap">
                                <button 
                                    type="button" 
                                    onclick="openOrderDetailModal('{{ $order['id_pesanan'] }}', '{{ $order['nama_pembeli'] }}', '{{ $order['kota'] }}', '{{ $order['jenis_produk'] }}', '{{ $order['pengiriman'] }}', '{{ $order['status_tag'] }}')"
                                    class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] font-bold text-xs px-4 py-1.5 rounded-full shadow-xs transition inline-flex items-center gap-1 cursor-pointer"
                                >
                                    <span>Detail</span>
                                    <span class="text-sm">→</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-xs text-[#737373]">
                                Tidak ada data pesanan ganci yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="border-t border-[#ECE8E1] pt-5 mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <span class="text-[#737373]">
                Menampilkan <strong class="text-[#1A1A1A]">1 – {{ count($orders) }}</strong> dari <strong class="text-[#1A1A1A]">48</strong> pesanan
            </span>

            <div class="flex items-center gap-1.5">
                <button class="w-7 h-7 rounded-lg text-[#8A8A85] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    ‹
                </button>
                <button class="w-7 h-7 rounded-lg bg-maroon text-white font-bold flex items-center justify-center shadow-xs">
                    1
                </button>
                <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    2
                </button>
                <button class="w-7 h-7 rounded-lg text-[#737373] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    3
                </button>
                <button class="w-7 h-7 rounded-lg text-[#8A8A85] hover:bg-[#F6F5F2] flex items-center justify-center transition">
                    ›
                </button>
            </div>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- ORDER DETAIL MODAL                                        -->
    <!-- ========================================================= -->
    <div id="order-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-[#ECE8E1] animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECE8E1]">
                <h3 class="text-base font-bold text-[#1A1A1A]">Detail Pesanan Ganci NFC</h3>
                <button type="button" onclick="closeOrderDetailModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1 cursor-pointer">✕</button>
            </div>

            <div class="mt-4 space-y-3.5 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-[#F2EEE9]">
                    <span class="text-[#737373]">Nomor Pesanan:</span>
                    <strong id="modal-order-id" class="text-maroon font-bold text-sm">#ORD-001</strong>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-[#F2EEE9]">
                    <span class="text-[#737373]">Pembeli:</span>
                    <span id="modal-order-buyer" class="font-bold text-[#1A1A1A]">Nama</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-[#F2EEE9]">
                    <span class="text-[#737373]">Kota Tujuan:</span>
                    <span id="modal-order-city" class="text-[#1A1A1A]">Makassar</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-[#F2EEE9]">
                    <span class="text-[#737373]">Varian Ganci:</span>
                    <span id="modal-order-product" class="font-semibold text-right text-[#1A1A1A]">Ganci Akrilik</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-[#F2EEE9]">
                    <span class="text-[#737373]">Status Pengiriman:</span>
                    <span id="modal-order-shipping" class="font-semibold uppercase text-[#1E70A0]">Dikirim</span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-[#737373]">Status Chip NFC:</span>
                    <span id="modal-order-tag-status" class="font-bold text-[#2E7D32]">Aktif</span>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2.5">
                <button type="button" onclick="closeOrderDetailModal()" class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] text-xs font-semibold px-4 py-2 rounded-full transition cursor-pointer">
                    Tutup
                </button>
                <button type="button" onclick="alert('Label resi pengiriman dicetak.'); closeOrderDetailModal();" class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] text-xs font-bold px-4 py-2 rounded-full transition cursor-pointer">
                    Cetak Resi &amp; Label
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleOrderStatusDropdown() {
        const menu = document.getElementById('order-status-dropdown');
        menu.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('order-status-dropdown');
        if (dropdown && !dropdown.contains(e.target) && !e.target.closest('button[onclick="toggleOrderStatusDropdown()"]')) {
            dropdown.classList.add('hidden');
        }
    });

    function openOrderDetailModal(id, buyer, city, product, shipping, tagStatus) {
        document.getElementById('modal-order-id').textContent = id;
        document.getElementById('modal-order-buyer').textContent = buyer;
        document.getElementById('modal-order-city').textContent = city;
        document.getElementById('modal-order-product').textContent = product;
        document.getElementById('modal-order-shipping').textContent = shipping.toUpperCase();
        document.getElementById('modal-order-tag-status').textContent = tagStatus === 'aktif' ? 'Aktif (Terhubung)' : 'Belum Diaktivasi';
        document.getElementById('order-detail-modal').classList.remove('hidden');
    }

    function closeOrderDetailModal() {
        document.getElementById('order-detail-modal').classList.add('hidden');
    }
</script>
@endpush
