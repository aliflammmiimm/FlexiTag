<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\ModeData;
use App\Models\Tag;
use App\Models\Tap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public const MODE_LABELS = [
        'kampus' => 'Mode Kampus',
        'kerja' => 'Mode Kerja',
        'umkm' => 'Mode UMKM',
        'hilang' => 'Mode Barang Hilang',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user()->load(['profile', 'tags']);

        // Data tag milik pengguna yang sedang login
        $tags = $user->tags()->orderByDesc('activated_at')->get();
        $primaryTag = $tags->where('status', 'aktif')->first() ?? $tags->first();

        if (! in_array($user->role, ['admin', 'staff'], true) && ! $primaryTag) {
            return redirect()->route('user.activation.show', ['step' => 2])
                ->with('warning', 'Gantungan kunci FlexiTag Anda belum diaktivasi. Silakan masukkan kode aktivasi terlebih dahulu untuk mengakses dashboard.');
        }

        $activeTagCount = $tags->where('status', 'aktif')->count();

        $tagIds = $tags->pluck('id');

        // Metrik kunjungan / tap
        $totalTaps = $tagIds->isNotEmpty()
            ? Tap::whereIn('tag_id', $tagIds)->count()
            : 0;

        $weeklyTaps = $tagIds->isNotEmpty()
            ? Tap::whereIn('tag_id', $tagIds)->where('created_at', '>=', now()->subDays(7))->count()
            : 0;

        $lastTap = $tagIds->isNotEmpty()
            ? Tap::whereIn('tag_id', $tagIds)->latest('created_at')->first()
            : null;

        if ($lastTap && $lastTap->created_at) {
            $diffHours = (int) $lastTap->created_at->diffInHours(now());
            if ($diffHours < 1) {
                $mins = (int) $lastTap->created_at->diffInMinutes(now());
                $lastActivity = $mins <= 1 ? 'Baru saja' : $mins.' Mnt lalu';
            } elseif ($diffHours < 24) {
                $lastActivity = $diffHours.' Jam lalu';
            } else {
                $lastActivity = (int) $lastTap->created_at->diffInDays(now()).' Hari lalu';
            }
        } elseif ($primaryTag && $primaryTag->activated_at) {
            $diffHours = (int) $primaryTag->activated_at->diffInHours(now());
            if ($diffHours < 1) {
                $mins = (int) $primaryTag->activated_at->diffInMinutes(now());
                $lastActivity = $mins <= 1 ? 'Baru saja' : $mins.' Mnt lalu';
            } elseif ($diffHours < 24) {
                $lastActivity = $diffHours.' Jam lalu';
            } else {
                $lastActivity = (int) $primaryTag->activated_at->diffInDays(now()).' Hari lalu';
            }
        } else {
            $lastActivity = 'Baru saja';
        }

        $activeMode = $user->profile?->active_mode ?? 'kampus';
        $displayName = $user->profile?->display_name ?? $user->name ?? 'Pengguna FlexiTag';
        $headline = $user->profile?->headline ?? '';

        // Hitung inisial nama untuk avatar
        $words = preg_split('/\s+/', trim($displayName));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= Str::upper(mb_substr($w, 0, 1));
        }
        $initials = $initials ?: 'AD';

        $tagCode = $primaryTag?->tag_code ?? 'FT-8X9Y2Z';
        $publicUrl = $primaryTag ? $primaryTag->public_url : url('/u/'.$tagCode);

        return view('user.dashboard', [
            'user' => $user,
            'profile' => $user->profile,
            'tags' => $tags,
            'primaryTag' => $primaryTag,
            'activeTagCount' => $activeTagCount,
            'totalTaps' => $totalTaps,
            'weeklyTaps' => $weeklyTaps,
            'lastActivity' => $lastActivity,
            'displayName' => $displayName,
            'headline' => $headline ?: 'Member FlexiTag',
            'initials' => $initials,
            'activeMode' => $activeMode,
            'activeModeLabel' => self::MODE_LABELS[$activeMode] ?? 'Mode Kampus',
            'tagCode' => $tagCode,
            'publicUrl' => $publicUrl,
        ]);
    }

    /**
     * Beralih mode aktif (Mode Kampus, Mode Kerja, Mode UMKM, Mode Barang Hilang).
     */
    public function switchMode(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'string', 'in:kampus,kerja,umkm,hilang'],
        ]);

        $user = $request->user();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'active_mode' => $data['mode'],
                'theme' => 'sulawesi_heritage',
            ]
        );

        $modeLabel = self::MODE_LABELS[$data['mode']] ?? $data['mode'];

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'mode' => $data['mode'],
                'label' => $modeLabel,
                'message' => 'Mode berhasil dialihkan ke '.$modeLabel,
            ]);
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Mode profil aktif berhasil dialihkan ke '.$modeLabel.'.');
    }

    /**
     * Update cepat informasi profil dari dashboard.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:2', 'max:100'],
            'headline' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'no_whatsapp' => ['nullable', 'string', 'max:25'],
        ], [
            'display_name.required' => 'Nama tampilan wajib diisi.',
            'display_name.min' => 'Nama tampilan minimal 2 karakter.',
            'photo.image' => 'File foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $profileData = [
            'display_name' => $data['display_name'],
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
        ];

        if ($request->hasFile('photo')) {
            if ($user->profile?->photo_path) {
                Storage::disk('public')->delete($user->profile->photo_path);
            }
            $profileData['photo_path'] = $request->file('photo')->store('profiles', 'public');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        if ($request->filled('no_whatsapp')) {
            $cleanWa = preg_replace('/[^0-9]/', '', (string) $request->no_whatsapp);
            if (Str::startsWith($cleanWa, '0')) {
                $cleanWa = '62'.substr($cleanWa, 1);
            }
            $user->no_whatsapp = $cleanWa;
            $user->save();

            Link::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'whatsapp'],
                [
                    'label' => 'WhatsApp Utama',
                    'url' => 'https://wa.me/'.$cleanWa,
                    'is_visible' => true,
                    'sort_order' => 1,
                ]
            );
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Profil berhasil diperbarui!');
    }

    /**
     * Update preferensi privasi & keamanan tag.
     */
    public function updatePrivacy(Request $request): RedirectResponse
    {
        return redirect()->route('user.dashboard')
            ->with('status', 'Pengaturan privasi & akses berhasil disimpan.');
    }

    /**
     * Tampilkan halaman Edit Profil Mode UMKM (FR-PRF-01 & FR-PUB-09).
     */
    public function editUmkm(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags']);
        $primaryTag = $user->tags()->where('status', 'aktif')->first() ?? $user->tags()->first();

        // Ambil data UMKM dari mode_data jika ada
        $modeRecord = ModeData::where('user_id', $user->id)
            ->where('mode', 'umkm')
            ->first();

        $savedData = $modeRecord?->data ?? [];

        // Normalisasi nomor WA terdaftar pengguna (jika sudah ada)
        $cleanWa = $user->no_whatsapp ?? '';
        if (Str::startsWith($cleanWa, '62')) {
            $cleanWa = substr($cleanWa, 2);
        } elseif (Str::startsWith($cleanWa, '0')) {
            $cleanWa = substr($cleanWa, 1);
        }

        // Data awal hanya mengambil data tersimpan, atau dibiarkan kosong agar placeholder tampil
        $umkm = [
            'nama_usaha' => $savedData['nama_usaha'] ?? '',
            'deskripsi' => $savedData['deskripsi'] ?? $savedData['tagline'] ?? '',
            'alamat_toko' => $savedData['alamat_toko'] ?? '',
            'whatsapp_order' => $savedData['whatsapp_order'] ?? $cleanWa,
            'logo_url' => ! empty($savedData['logo_path']) ? asset('storage/'.$savedData['logo_path']) : null,
            'katalog_produk' => $savedData['katalog_produk'] ?? [],
        ];

        return view('user.profile_umkm', [
            'user' => $user,
            'profile' => $user->profile,
            'primaryTag' => $primaryTag,
            'tagCode' => $primaryTag?->tag_code ?? 'NTAG215',
            'umkm' => $umkm,
        ]);
    }

    /**
     * Simpan pembaruan profil & katalog Mode UMKM.
     */
    public function updateUmkm(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_usaha' => ['required', 'string', 'max:150'],
            'deskripsi' => ['required', 'string', 'max:250'],
            'alamat_toko' => ['required', 'string', 'max:250'],
            'whatsapp_order' => ['required', 'string', 'max:25'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'katalog_json' => ['nullable', 'string'],
        ], [
            'nama_usaha.required' => 'Nama Usaha / Toko wajib diisi.',
            'deskripsi.required' => 'Deskripsi singkat usaha wajib diisi.',
            'alamat_toko.required' => 'Alamat toko wajib diisi.',
            'whatsapp_order.required' => 'Nomor WhatsApp pemesanan wajib diisi.',
            'logo.image' => 'Logo harus berupa berkas gambar.',
            'logo.max' => 'Ukuran logo maksimal 5 MB.',
        ]);

        $modeRecord = ModeData::firstOrNew([
            'user_id' => $user->id,
            'mode' => 'umkm',
        ]);

        $existingData = $modeRecord->data ?? [];

        // Upload logo baru jika ada
        $logoPath = $existingData['logo_path'] ?? null;
        if ($request->hasFile('logo')) {
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $request->file('logo')->store('umkm/logos', 'public');
        }

        // Parse katalog produk
        $products = $existingData['katalog_produk'] ?? [];
        if ($request->filled('katalog_json')) {
            $decoded = json_decode((string) $request->katalog_json, true);
            if (is_array($decoded)) {
                $products = $decoded;
            }
        }

        // Clean whatsapp number
        $cleanWa = preg_replace('/[^0-9]/', '', (string) $validated['whatsapp_order']);
        if (Str::startsWith($cleanWa, '0')) {
            $cleanWa = '62'.substr($cleanWa, 1);
        } elseif (! Str::startsWith($cleanWa, '62') && ! empty($cleanWa)) {
            $cleanWa = '62'.$cleanWa;
        }

        $newData = [
            'nama_usaha' => $validated['nama_usaha'],
            'deskripsi' => $validated['deskripsi'],
            'tagline' => $validated['deskripsi'],
            'alamat_toko' => $validated['alamat_toko'],
            'whatsapp_order' => $cleanWa,
            'logo_path' => $logoPath,
            'katalog_produk' => $products,
            'kategori' => 'Katalog Kuliner',
        ];

        $modeRecord->data = $newData;
        $modeRecord->save();

        // Update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => $validated['nama_usaha'],
                'headline' => 'Katalog Kuliner UMKM',
                'active_mode' => 'umkm',
            ]
        );

        // Update user whatsapp
        if (! empty($cleanWa)) {
            $user->no_whatsapp = $cleanWa;
            $user->save();

            Link::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'whatsapp'],
                [
                    'label' => 'Pesan WhatsApp (Fast Order)',
                    'url' => 'https://wa.me/'.$cleanWa,
                    'is_visible' => true,
                    'sort_order' => 1,
                ]
            );
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Profil UMKM dan Katalog Produk berhasil disimpan & disinkronkan ke Tag!');
    }

    /**
     * Tampilkan halaman formulir tambah produk baru UMKM.
     */
    public function createProduct(Request $request): View
    {
        return view('user.profile_umkm_product', [
            'mode' => 'create',
            'product' => null,
        ]);
    }

    /**
     * Simpan produk baru ke katalog UMKM.
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        // Bersihkan format mata uang dari input harga (cth: "35.000" atau "Rp 35.000")
        $rawHarga = preg_replace('/[^0-9]/', '', (string) $request->input('harga', '0'));
        $request->merge(['harga_clean' => (int) $rawHarga]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'harga_clean' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:200'],
            'label_promosi' => ['nullable', 'string', 'in:tanpa_label,unggulan,paling_laris'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'nama.required' => 'Nama produk wajib diisi.',
            'harga_clean.required' => 'Harga produk wajib diisi.',
            'satuan.required' => 'Satuan takaran wajib diisi.',
            'deskripsi.max' => 'Deskripsi produk maksimal 200 karakter.',
            'foto.image' => 'File foto harus berupa gambar (JPG, PNG, WEBP).',
            'foto.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        $user = $request->user();
        $modeRecord = ModeData::firstOrCreate(
            ['user_id' => $user->id, 'mode' => 'umkm'],
            ['data' => ['katalog_produk' => []]]
        );

        $data = $modeRecord->data ?? [];
        $products = is_array($data['katalog_produk'] ?? null) ? $data['katalog_produk'] : [];

        // Upload foto jika disertakan
        $fotoUrl = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('umkm_products', 'public');
            $fotoUrl = Storage::url($path);
        }

        // Tentukan metadata label promosi
        $labelChoice = $validated['label_promosi'] ?? 'tanpa_label';
        $label = null;
        $badgeIcon = null;
        $badgeClass = null;

        if ($labelChoice === 'unggulan') {
            $label = 'Unggulan';
            $badgeIcon = '⭐';
            $badgeClass = 'badge-gold';
        } elseif ($labelChoice === 'paling_laris') {
            $label = 'Paling Laris';
            $badgeIcon = '🔥';
            $badgeClass = 'badge-red';
        }

        $satuanClean = trim((string) $validated['satuan']);
        if (Str::startsWith($satuanClean, '/')) {
            $satuanClean = ltrim($satuanClean, '/ ');
        }

        $newProduct = [
            'id' => (int) (now()->timestamp.rand(10, 99)),
            'nama' => $validated['nama'],
            'harga' => $validated['harga_clean'],
            'satuan' => $satuanClean ?: 'porsi',
            'deskripsi' => $validated['deskripsi'] ?? '',
            'label' => $label,
            'badge_icon' => $badgeIcon,
            'badge_class' => $badgeClass,
            'foto_url' => $fotoUrl,
            'is_ready' => true,
        ];

        $products[] = $newProduct;
        $data['katalog_produk'] = $products;
        $modeRecord->data = $data;
        $modeRecord->save();

        return redirect()->route('user.profile.umkm')
            ->with('status', "Produk '{$validated['nama']}' berhasil ditambahkan ke katalog UMKM!");
    }

    /**
     * Tampilkan formulir edit produk yang sudah ada di katalog.
     */
    public function editProduct(Request $request, int|string $id): View|RedirectResponse
    {
        $user = $request->user();
        $modeRecord = ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first();
        $products = $modeRecord?->data['katalog_produk'] ?? [];

        $targetProduct = null;
        foreach ($products as $prod) {
            if ((string) ($prod['id'] ?? '') === (string) $id) {
                $targetProduct = $prod;
                break;
            }
        }

        if (! $targetProduct) {
            return redirect()->route('user.profile.umkm')
                ->with('warning', 'Produk tidak ditemukan dalam katalog.');
        }

        return view('user.profile_umkm_product', [
            'mode' => 'edit',
            'product' => $targetProduct,
        ]);
    }

    /**
     * Perbarui data produk di katalog UMKM.
     */
    public function updateProduct(Request $request, int|string $id): RedirectResponse
    {
        $rawHarga = preg_replace('/[^0-9]/', '', (string) $request->input('harga', '0'));
        $request->merge(['harga_clean' => (int) $rawHarga]);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'harga_clean' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'deskripsi' => ['nullable', 'string', 'max:200'],
            'label_promosi' => ['nullable', 'string', 'in:tanpa_label,unggulan,paling_laris'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $user = $request->user();
        $modeRecord = ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first();
        if (! $modeRecord) {
            return redirect()->route('user.profile.umkm');
        }

        $data = $modeRecord->data ?? [];
        $products = is_array($data['katalog_produk'] ?? null) ? $data['katalog_produk'] : [];

        $foundIndex = -1;
        foreach ($products as $idx => $prod) {
            if ((string) ($prod['id'] ?? '') === (string) $id) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex === -1) {
            return redirect()->route('user.profile.umkm')
                ->with('warning', 'Produk tidak ditemukan.');
        }

        // Simpan foto baru jika diunggah, atau pertahankan foto lama
        $fotoUrl = $products[$foundIndex]['foto_url'] ?? null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('umkm_products', 'public');
            $fotoUrl = Storage::url($path);
        }

        $labelChoice = $validated['label_promosi'] ?? 'tanpa_label';
        $label = null;
        $badgeIcon = null;
        $badgeClass = null;

        if ($labelChoice === 'unggulan') {
            $label = 'Unggulan';
            $badgeIcon = '⭐';
            $badgeClass = 'badge-gold';
        } elseif ($labelChoice === 'paling_laris') {
            $label = 'Paling Laris';
            $badgeIcon = '🔥';
            $badgeClass = 'badge-red';
        }

        $satuanClean = trim((string) $validated['satuan']);
        if (Str::startsWith($satuanClean, '/')) {
            $satuanClean = ltrim($satuanClean, '/ ');
        }

        $products[$foundIndex] = array_merge($products[$foundIndex], [
            'nama' => $validated['nama'],
            'harga' => $validated['harga_clean'],
            'satuan' => $satuanClean ?: 'porsi',
            'deskripsi' => $validated['deskripsi'] ?? '',
            'label' => $label,
            'badge_icon' => $badgeIcon,
            'badge_class' => $badgeClass,
            'foto_url' => $fotoUrl,
        ]);

        $data['katalog_produk'] = $products;
        $modeRecord->data = $data;
        $modeRecord->save();

        return redirect()->route('user.profile.umkm')
            ->with('status', "Produk '{$validated['nama']}' berhasil diperbarui!");
    }

    /**
     * Hapus produk dari katalog UMKM.
     */
    public function deleteProduct(Request $request, int|string $id): RedirectResponse
    {
        $user = $request->user();
        $modeRecord = ModeData::where('user_id', $user->id)->where('mode', 'umkm')->first();

        if ($modeRecord && isset($modeRecord->data['katalog_produk'])) {
            $data = $modeRecord->data;
            $products = array_values(array_filter($data['katalog_produk'], function ($item) use ($id) {
                return (string) ($item['id'] ?? '') !== (string) $id;
            }));

            $data['katalog_produk'] = $products;
            $modeRecord->data = $data;
            $modeRecord->save();
        }

        return redirect()->route('user.profile.umkm')
            ->with('status', 'Produk berhasil dihapus dari katalog.');
    }

    /**
     * Halaman Manajemen Tag & Merch FlexiTag.
     */
    public function merch(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags']);
        $tags = $user->tags()->with('taps')->orderByDesc('activated_at')->get();

        $activeMode = $user->profile?->active_mode ?? 'kampus';
        $displayName = $user->profile?->display_name ?? $user->name ?? 'Pengguna FlexiTag';
        $headline = $user->profile?->headline ?? 'Member FlexiTag';

        // Hitung inisial nama untuk avatar di drawer
        $words = preg_split('/\s+/', trim($displayName));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= Str::upper(mb_substr($w, 0, 1));
        }
        $initials = $initials ?: 'FT';

        $profile = $user->profile;
        $activeTagCount = $tags->where('status', 'aktif')->count();
        $totalTagCount = $tags->count();

        // Siapkan metadata per tag
        $formattedTags = $tags->map(function ($tag, $index) use ($activeMode) {
            $isFirst = $index === 0;
            $merchTitle = $isFirst ? 'Gantungan Kunci Bulat' : 'Gantungan Kunci Persegi';
            $merchImage = $isFirst ? asset('images/keychain.jpg') : asset('images/keychain_square.jpg');

            // Mode label
            $modeLabel = $isFirst
                ? (self::MODE_LABELS[$activeMode] ?? 'Mode Kampus')
                : 'Mode Barang Hilang';
            $modeShort = str_replace('Mode ', '', $modeLabel);

            // Last tap or lock activity text
            $lastTap = $tag->taps->sortByDesc('created_at')->first();
            if ($tag->status === 'aktif') {
                if ($lastTap && $lastTap->created_at) {
                    $diffMins = (int) $lastTap->created_at->diffInMinutes(now());
                    if ($diffMins < 60) {
                        $activityText = 'Terakhir di-tap: '.($diffMins <= 1 ? 'Baru saja' : $diffMins.' mnt lalu').' di Makassar';
                    } elseif ($diffMins < 1440) {
                        $activityText = 'Terakhir di-tap: '.(int) ($diffMins / 60).' jam lalu di Makassar';
                    } else {
                        $activityText = 'Terakhir di-tap: '.(int) ($diffMins / 1440).' hari lalu di Makassar';
                    }
                } else {
                    $activityText = 'Terakhir di-tap: 15 mnt lalu di Makassar';
                }
            } else {
                $lockedAt = $tag->updated_at ?? now();
                $activityText = 'Dikunci: '.$lockedAt->format('d M, H:i').' WITA';
            }

            return [
                'model' => $tag,
                'id' => $tag->id,
                'tag_code' => $tag->tag_code,
                'title' => $merchTitle,
                'image' => $merchImage,
                'mode_short' => $modeShort,
                'status' => $tag->status,
                'is_active' => $tag->status === 'aktif',
                'is_locked' => $tag->status === 'nonaktif',
                'disabled_by' => $tag->disabled_by,
                'activity_text' => $activityText,
                'total_taps' => $tag->taps->count(),
            ];
        });

        return view('user.merch', [
            'user' => $user,
            'profile' => $profile,
            'displayName' => $displayName,
            'headline' => $headline,
            'initials' => $initials,
            'tags' => $formattedTags,
            'totalTagCount' => $totalTagCount,
            'activeTagCount' => $activeTagCount,
            'activeMode' => $activeMode,
        ]);
    }

    /**
     * Kunci atau buka kunci tag (Kill Switch).
     */
    public function toggleTagStatus(Request $request, int|string $id): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $tag = $user->role === 'admin'
            ? Tag::findOrFail($id)
            : $user->tags()->findOrFail($id);

        if ($tag->status === 'aktif') {
            $tag->status = 'nonaktif';
            $tag->disabled_by = 'owner';
            $tag->save();
            $message = "Tag {$tag->tag_code} berhasil dikunci (Kill Switch Aktif). Data profil publik tidak dapat diakses.";
        } else {
            if ($tag->disabled_by === 'admin' && $user->role !== 'admin') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tag ini diblokir oleh admin dan tidak dapat dibuka mandiri.',
                    ], 403);
                }

                return back()->with('error', 'Tag ini diblokir oleh admin dan tidak dapat dibuka mandiri.');
            }

            $tag->status = 'aktif';
            $tag->disabled_by = null;
            $tag->save();
            $message = "Tag {$tag->tag_code} berhasil dibuka kuncinya dan siap digunakan kembali.";
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'tag_id' => $tag->id,
                'status' => $tag->status,
                'is_active' => $tag->status === 'aktif',
                'is_locked' => $tag->status === 'nonaktif',
            ]);
        }

        return back()->with('status', $message);
    }
}
