@extends('layouts.admin')

@section('title', 'Daftar Pesanan & Pelacakan SRS')

@section('content')
<div class="space-y-7">

    <!-- Flash Message Notification -->
    @if (session('success'))
        <div class="p-4 rounded-2xl bg-[#EAF5EC] border border-[#C8E6C9] text-xs text-[#2E7D32] flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-[#2E7D32]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-[#2E7D32] hover:opacity-75 font-bold text-sm">✕</button>
        </div>
    @endif

    <!-- ========================================================= -->
    <!-- TOP STATS ROW (3 SUMMARY CARDS)                           -->
    <!-- ========================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: TOTAL PESANAN -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                    Total Pesanan Masuk
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ number_format($metrics['total_pesanan'], 0, ',', '.') }}
                    </h3>
                    <span class="text-xs font-medium text-[#737373]">({{ $metrics['total_hardware'] }} Unit Ganci)</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#FCF5E3] text-[#B8860B] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: ANTREAN PEMENUHAN (DIPROSES) -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                    Antrean Pemenuhan
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['perlu_dikirim'] }}
                    </h3>
                    <span class="text-xs font-medium text-maroon">Perlu Diproses/Kirim</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#FBE8E8] text-maroon flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25H4.5a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25h1.5m8.25-11.25V16.5"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: SIAP & TERKIRIM -->
        <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ECE8E1] shadow-[0_2px_12px_rgba(0,0,0,0.02)] flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-wider text-[#8A8A85] uppercase block">
                    Siap &amp; Terkirim
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <h3 class="text-3xl font-extrabold text-[#1A1A1A] tracking-tight">
                        {{ $metrics['terkirim'] }}
                    </h3>
                    <span class="text-xs font-medium text-[#2E7D32]">Pesanan Selesai</span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-[#EAF5EC] text-[#2E7D32] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                    Daftar Pesanan &amp; Alur Status Produksi SRS
                </h3>
                <p class="text-xs text-[#737373] mt-0.5 font-normal">
                    Manajemen pesanan custom merchandise ganci akrilik/kulit dan sinkronisasi 7 tahapan status pelacakan.
                </p>
            </div>

            <!-- Filter Status Tabs -->
            <div class="flex overflow-x-auto no-scrollbar max-w-full items-center bg-[#F6F5F2] p-1 rounded-2xl sm:rounded-full text-xs font-semibold gap-1 w-full md:w-auto">
                <a 
                    href="{{ route('admin.orders', ['status' => 'all', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'all' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Semua ({{ $metrics['total_pesanan'] }})
                </a>
                <a 
                    href="{{ route('admin.orders', ['status' => 'pending', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'pending' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Pending Bayar ({{ $metrics['pending'] }})
                </a>
                <a 
                    href="{{ route('admin.orders', ['status' => 'perlu_dikirim', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'perlu_dikirim' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Diproses ({{ $metrics['perlu_dikirim'] }})
                </a>
                <a 
                    href="{{ route('admin.orders', ['status' => 'terkirim', 'search' => $search]) }}" 
                    class="whitespace-nowrap px-4 py-2 rounded-full transition {{ $statusFilter === 'terkirim' ? 'bg-[#380208] text-white shadow-xs font-bold' : 'text-[#737373] hover:text-[#1A1A1A]' }}"
                >
                    Siap/Terkirim ({{ $metrics['terkirim'] }})
                </a>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-5 flex items-center justify-between">
            <form action="{{ route('admin.orders') }}" method="GET" class="w-full max-w-sm relative">
                <input type="hidden" name="status" value="{{ $statusFilter }}">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari No. Pesanan, nama pembeli, varian..." 
                    class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs text-[#1A1A1A] py-2.5 pl-9 pr-4 rounded-full border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition"
                >
                <svg class="w-4 h-4 text-[#8A8A85] absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                </svg>
            </form>

            <span class="text-xs text-[#737373] hidden sm:block">
                Menampilkan <strong>{{ $orders->count() }}</strong> pesanan
            </span>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-[10px] font-bold uppercase tracking-wider text-[#8A8A85] border-b border-[#ECE8E1]">
                        <th class="pb-3.5 px-3">No. Pesanan</th>
                        <th class="pb-3.5 px-3">Pelanggan</th>
                        <th class="pb-3.5 px-3">Spesifikasi Ganci</th>
                        <th class="pb-3.5 px-3">Jumlah &amp; Total</th>
                        <th class="pb-3.5 px-3">Status Tahapan (SRS)</th>
                        <th class="pb-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F2EEE9]">
                    @forelse ($orders as $order)
                        @php
                            $badge = $order->status_badge;
                        @endphp
                        <tr class="hover:bg-[#FAF9F6] transition-colors">
                            
                            <!-- 1. ID Pesanan -->
                            <td class="py-4 px-3 font-bold text-xs text-maroon whitespace-nowrap">
                                {{ $order->order_no }}
                            </td>

                            <!-- 2. Nama Pembeli & Alamat -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="font-bold text-xs text-[#1A1A1A] block">{{ $order->customer_name }}</span>
                                <span class="text-[11px] text-[#737373] font-normal block">{{ $order->whatsapp }}</span>
                            </td>

                            <!-- 3. Jenis Produk (Exclusively Ganci Variants) -->
                            <td class="py-4 px-3">
                                <div class="font-medium text-xs text-[#1A1A1A] flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-[#8A8A85] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $order->shape }}</span>
                                </div>
                                <span class="text-[11px] text-[#737373] block mt-0.5">{{ $order->color }} • {{ $order->size }}</span>
                            </td>

                            <!-- 4. Qty & Total -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="font-bold text-xs text-[#1A1A1A] block">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                <span class="text-[11px] text-[#737373] font-normal block">{{ $order->quantity }} pcs ({{ $order->delivery_method === 'ambil' ? 'Ambil di Tempat' : 'Kirim Kurir' }})</span>
                            </td>

                            <!-- 5. Status Tahapan (SRS Compact Badge) -->
                            <td class="py-4 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 {{ $badge['bg_class'] }} font-bold text-[11px] px-3 py-1 rounded-full shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $badge['dot_class'] }}"></span>
                                    {{ $badge['label'] }}
                                </span>
                                <span class="block text-[10px] text-[#8A8A85] mt-0.5">
                                    {{ $order->detail_status_label }}
                                </span>
                            </td>

                            <!-- 6. Aksi: Detail & Update Button -->
                            <td class="py-4 px-3 text-right whitespace-nowrap">
                                <button 
                                    type="button" 
                                    onclick="openOrderDetailModal({{ json_encode($order) }}, {{ json_encode($order->statusLogs) }})"
                                    class="bg-gold-accent hover:bg-[#E5BC50] text-[#1A1A1A] font-bold text-xs px-4 py-1.5 rounded-full shadow-xs transition inline-flex items-center gap-1 cursor-pointer"
                                >
                                    <span>Kelola Tahap</span>
                                    <span class="text-sm">→</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-xs text-[#737373]">
                                <div class="w-10 h-10 rounded-full bg-[#F6F5F2] flex items-center justify-center mx-auto mb-2 text-[#8A8A85]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5"/>
                                    </svg>
                                </div>
                                Tidak ada data pesanan ganci yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- ========================================================= -->
    <!-- ORDER DETAIL & STAGE UPDATE MODAL                         -->
    <!-- ========================================================= -->
    <div id="order-detail-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-xs p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-[#ECE8E1] my-8 max-h-[90vh] overflow-y-auto">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3.5 border-b border-[#ECE8E1]">
                <div>
                    <h3 class="text-base font-bold text-[#1A1A1A]">Kelola Tahapan Pesanan Ganci</h3>
                    <p class="text-[11px] text-[#737373] mt-0.5">Sistem Pelacakan Publik 7 Tahap (SRS FlexiTag)</p>
                </div>
                <button type="button" onclick="closeOrderDetailModal()" class="text-[#8A8A85] hover:text-[#1A1A1A] p-1.5 rounded-full hover:bg-[#F6F5F2] transition cursor-pointer">✕</button>
            </div>

            <!-- Order Snapshot -->
            <div class="mt-4 bg-[#F8F7F4] rounded-2xl p-4 border border-[#ECE8E1] space-y-2 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">No. Pesanan:</span>
                    <strong id="modal-order-no" class="text-maroon font-bold text-sm">#ORD-0000</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Pemesan:</span>
                    <span id="modal-customer-name" class="font-bold text-[#1A1A1A]">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Kontak WA / Email:</span>
                    <span id="modal-contact" class="text-[#1A1A1A]">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Varian &amp; Qty:</span>
                    <span id="modal-product" class="font-semibold text-right text-[#1A1A1A]">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[#737373]">Metode &amp; Alamat:</span>
                    <span id="modal-shipping" class="text-right text-[#1A1A1A] max-w-[220px] truncate">-</span>
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-[#ECE8E1]">
                    <span class="text-[#737373]">Total Transaksi:</span>
                    <strong id="modal-total" class="font-bold text-sm text-[#1A1A1A]">Rp 0</strong>
                </div>
            </div>

            <!-- Form Update Status SRS -->
            <form id="order-status-update-form" method="POST" action="" class="mt-5 space-y-4">
                @csrf
                
                <div>
                    <label for="status-select" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                        Pembaruan Status Tahapan Pesanan <span class="text-maroon">*</span>
                    </label>
                    <select 
                        name="status" 
                        id="status-select" 
                        required
                        class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs font-semibold text-[#1A1A1A] py-3 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition cursor-pointer"
                    >
                        @foreach ($availableStatuses as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <span class="text-[10.5px] text-[#8A8A85] block mt-1">
                        Perubahan akan langsung sinkron dengan halaman publik "Lacak Pesanan" pelanggan.
                    </span>
                </div>

                <div>
                    <label for="status-note" class="block text-xs font-bold text-[#1A1A1A] mb-1.5">
                        Catatan Admin (Riwayat Perubahan)
                    </label>
                    <input 
                        type="text" 
                        name="note" 
                        id="status-note" 
                        placeholder="Contoh: Pembayaran BCA terverifikasi / chip NFC selesai diprogram"
                        class="w-full bg-[#F6F5F2] hover:bg-[#F2F1ED] focus:bg-white text-xs text-[#1A1A1A] py-2.5 px-4 rounded-2xl border border-[#ECE8E1] focus:outline-none focus:ring-2 focus:ring-[#8B0000]/10 focus:border-[#8B0000] transition"
                    >
                </div>

                <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-[#ECE8E1]">
                    <button 
                        type="button" 
                        onclick="closeOrderDetailModal()" 
                        class="bg-[#F6F5F2] hover:bg-[#EAE8E3] text-[#1A1A1A] text-xs font-semibold px-4 py-2.5 rounded-full transition cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="bg-maroon hover:bg-[#6B0000] text-[#D4AF37] text-xs font-bold px-5 py-2.5 rounded-full shadow-xs transition cursor-pointer inline-flex items-center gap-1.5"
                    >
                        <span>Simpan Status</span>
                        <span>✓</span>
                    </button>
                </div>
            </form>

            <!-- Status Audit Logs History -->
            <div class="mt-6 pt-4 border-t border-[#ECE8E1]">
                <h4 class="text-xs font-bold text-[#1A1A1A] mb-3 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Riwayat Audit Tahapan (order_status_logs)</span>
                </h4>
                
                <div id="modal-status-logs" class="space-y-2 text-[11px] max-h-40 overflow-y-auto pr-1">
                    <!-- Populated via JS -->
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function openOrderDetailModal(order, logs) {
        document.getElementById('modal-order-no').textContent = order.order_no;
        document.getElementById('modal-customer-name').textContent = order.customer_name;
        document.getElementById('modal-contact').textContent = (order.whatsapp || '-') + ' • ' + (order.email || '-');
        document.getElementById('modal-product').textContent = order.shape + ' (' + order.color + ' - ' + order.size + ') x' + order.quantity;
        document.getElementById('modal-shipping').textContent = (order.delivery_method === 'ambil' ? 'Ambil di Tempat' : 'Kirim: ') + (order.address || '-');
        document.getElementById('modal-total').textContent = 'Rp ' + Number(order.total).toLocaleString('id-ID');
        
        // Form action url
        const form = document.getElementById('order-status-update-form');
        form.action = `/admin/orders/${order.id}/status`;

        // Set current status in select dropdown
        const statusSelect = document.getElementById('status-select');
        statusSelect.value = order.status;

        // Reset note input
        document.getElementById('status-note').value = '';

        // Render status logs
        const logsContainer = document.getElementById('modal-status-logs');
        logsContainer.innerHTML = '';
        if (logs && logs.length > 0) {
            logs.forEach(log => {
                const dateStr = new Date(log.changed_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl bg-[#F6F5F2] border border-[#ECE8E1] flex flex-col gap-0.5';
                item.innerHTML = `
                    <div class="flex justify-between items-center font-bold text-xs text-[#1A1A1A]">
                        <span>${log.status.toUpperCase()}</span>
                        <span class="text-[10px] text-[#8A8A85] font-normal">${dateStr}</span>
                    </div>
                    <p class="text-[#737373] text-[11px]">${log.note || 'Pembaruan status sistem'}</p>
                `;
                logsContainer.appendChild(item);
            });
        } else {
            logsContainer.innerHTML = '<p class="text-xs text-[#8A8A85] italic">Belum ada riwayat perubahan status.</p>';
        }

        document.getElementById('order-detail-modal').classList.remove('hidden');
    }

    function closeOrderDetailModal() {
        document.getElementById('order-detail-modal').classList.add('hidden');
    }
</script>
@endpush
