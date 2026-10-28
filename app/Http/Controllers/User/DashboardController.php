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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
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

        if (! $primaryTag) {
            if (in_array($user->role, ['admin', 'staff'], true)) {
                return redirect()->route('admin.dashboard');
            }

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

        // Ambil data nama usaha jika user punya mode UMKM
        $umkmRecord = $user->modeData?->firstWhere('mode', 'umkm');
        $storeName = $umkmRecord?->data['nama_usaha'] ?? null;

        // Nama personal pengguna (prioritaskan nama asli registrasi untuk identitas pengguna)
        $displayName = $user->name;
        if (empty($displayName)) {
            $displayName = $user->profile?->display_name ?? 'Pengguna FlexiTag';
        }

        // Self-heal: jika display_name profil sempat tertimpa nama toko UMKM, kembalikan ke nama personal user
        if ($user->profile && $storeName && $user->profile->display_name === $storeName && ! empty($user->name)) {
            $user->profile->update(['display_name' => $user->name]);
        }

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

        // Ambil pesan penemuan barang masuk untuk pemilik tag (Owner Inbox / FR-LST-03)
        $lostMessages = $user->messages()
            ->where('type', 'lost_item')
            ->orderByDesc('created_at')
            ->get();
        $unreadMessagesCount = $lostMessages->whereNull('read_at')->count();

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
            'lostMessages' => $lostMessages,
            'unreadMessagesCount' => $unreadMessagesCount,
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

        // Sinkronisasi ke tag fisik pengguna
        if ($data['mode'] === 'hilang') {
            // Mode Hilang bersifat darurat: sinkronkan seluruh tag aktif milik pengguna
            $user->tags()->where('status', 'aktif')->update(['mode' => 'hilang']);
        } else {
            // Sinkronkan tag utama/terbaru pengguna agar Tampilan Publik di Home selalu sesuai
            $primaryTag = $user->tags()->where('status', 'aktif')->orderByDesc('activated_at')->first() ?? $user->tags()->first();
            if ($primaryTag) {
                $primaryTag->update(['mode' => $data['mode']]);
            }
        }

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
     * Buka profil digital publik milik pengguna yang sedang login.
     */
    public function myProfile(Request $request): View|RedirectResponse
    {
        $user = $request->user()->load(['tags', 'profile', 'links' => function ($q) {
            $q->where('is_visible', true)->orderBy('sort_order');
        }, 'privacySetting', 'modeData']);

        $tag = $user->tags()->where('status', 'aktif')->first() ?? $user->tags()->first();

        if ($tag) {
            return redirect()->route('public.tag', $tag->tag_code);
        }

        // Jika user belum punya tag terdaftar, render profil virtual langsung sesuai data user
        $virtualTag = new Tag([
            'tag_code' => 'FT-'.Str::upper(Str::random(6)),
            'status' => 'aktif',
            'batch_id' => 'EDISI PUSAKA KUNCI PA\'BARRE ALLO',
        ]);
        $virtualTag->id = 0;
        $virtualTag->setRelation('user', $user);

        $profile = $user->profile;
        $links = $user->links ?? collect();
        $privacy = $user->privacySetting;
        $activeMode = $profile?->active_mode ?? 'kerja';
        $modeData = $user->modeData?->firstWhere('mode', $activeMode)?->data ?? [];

        return view('user.public_profile', [
            'tag' => $virtualTag,
            'user' => $user,
            'profile' => $profile,
            'links' => $links,
            'privacy' => $privacy,
            'activeMode' => $activeMode,
            'modeData' => $modeData,
            'isLocked' => false,
        ]);
    }

    /**
     * Tampilkan halaman Edit Profil Mode Kampus (Jejaring Akademik).
     */
    public function editKampus(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags', 'links']);
        $primaryTag = $user->tags()->where('status', 'aktif')->first() ?? $user->tags()->first();

        $modeRecord = ModeData::where('user_id', $user->id)
            ->where('mode', 'kampus')
            ->first();

        $savedData = $modeRecord?->data ?? [];

        // Normalisasi nomor WA / telepon terdaftar
        $cleanWa = $user->no_whatsapp ?? '';
        if (Str::startsWith($cleanWa, '+62')) {
            $cleanWa = substr($cleanWa, 3);
        } elseif (Str::startsWith($cleanWa, '62')) {
            $cleanWa = substr($cleanWa, 2);
        } elseif (Str::startsWith($cleanWa, '0')) {
            $cleanWa = substr($cleanWa, 1);
        }

        $avatarUrl = null;
        if ($user->profile?->photo_path) {
            $avatarUrl = file_exists(public_path($user->profile->photo_path))
                ? asset($user->profile->photo_path)
                : asset('storage/'.$user->profile->photo_path);
        } elseif (str_contains(strtolower($user->profile?->display_name ?? $user->name ?? ''), 'andi') && file_exists(public_path('images/avatar_andi.jpg'))) {
            $avatarUrl = asset('images/avatar_andi.jpg');
        }

        $displayName = $savedData['nama_mahasiswa'] ?? $user->profile?->display_name ?? $user->name ?? 'Andi Muhammad';
        $userHandle = Str::slug($displayName, '');
        if (empty($userHandle)) {
            $userHandle = 'mahasiswa';
        }
        $userSlug = Str::slug($displayName);

        // Parsing program studi & universitas jika belum ada di savedData
        $programStudi = $savedData['jurusan'] ?? $savedData['program_studi'] ?? null;
        $universitas = $savedData['universitas'] ?? null;
        if (! $programStudi && $user->profile?->headline && str_contains($user->profile->headline, '•')) {
            $parts = explode('•', $user->profile->headline);
            $programStudi = trim($parts[0] ?? '');
            $universitas = trim($parts[1] ?? '');
        }
        $programStudi = $programStudi ?: 'Teknik Komputer';
        $universitas = $universitas ?: 'Universitas Negeri Makassar';

        // Parsing tahun angkatan & domisili jika belum ada di savedData
        $angkatan = $savedData['angkatan'] ?? null;
        $domisili = $savedData['domisili'] ?? null;
        if (! $angkatan && $user->profile?->bio && str_contains($user->profile->bio, 'Angkatan')) {
            if (preg_match('/Angkatan\s*(\d+)/i', $user->profile->bio, $matches)) {
                $angkatan = $matches[1];
            }
            if (str_contains($user->profile->bio, '•')) {
                $parts = explode('•', $user->profile->bio);
                $domisili = trim($parts[1] ?? '');
            }
        }
        $angkatan = $angkatan ?: '2024';
        $domisili = $domisili ?: 'Makassar, Sulawesi Selatan';

        // Tautan Jejaring Kampus: prioritaskan data tersimpan kampus, atau tautan non-whatsapp dari DB
        $links = $savedData['links'] ?? null;
        if (is_array($links)) {
            $links = array_values(array_filter($links, function ($item) {
                $type = $item['type'] ?? '';
                $title = strtolower($item['title'] ?? '');

                return ! in_array($type, ['whatsapp', 'phone', 'email']) && ! str_contains($title, 'fast order');
            }));
        }
        if (empty($links) && $user->links && $user->links->count() > 0) {
            $campusOnlyLinks = $user->links->filter(fn ($l) => ! in_array($l->type, ['whatsapp', 'phone', 'email']));
            if ($campusOnlyLinks->count() > 0) {
                $links = $campusOnlyLinks->map(function ($l) {
                    $hasPipe = str_contains($l->label, '|');

                    return [
                        'id' => $l->id,
                        'type' => $l->type,
                        'title' => $hasPipe ? trim(explode('|', $l->label)[0]) : $l->label,
                        'subtitle' => $hasPipe ? trim(explode('|', $l->label)[1]) : '',
                        'url' => $l->url,
                        'is_visible' => (bool) $l->is_visible,
                    ];
                })->values()->toArray();
            }
        }

        if (empty($links)) {
            $links = [
                [
                    'id' => 1,
                    'type' => 'academic',
                    'title' => 'HIMA '.$programStudi.' UNM',
                    'subtitle' => 'Pengurus Divisi Riset & Teknologi',
                    'url' => 'https://himatekkom.unm.ac.id',
                    'is_visible' => true,
                ],
                [
                    'id' => 2,
                    'type' => 'linkedin',
                    'title' => 'LinkedIn Mahasiswa',
                    'subtitle' => 'Jejaring akademik, lomba, & sertifikasi',
                    'url' => 'https://linkedin.com/in/'.$userSlug,
                    'is_visible' => true,
                ],
                [
                    'id' => 3,
                    'type' => 'instagram',
                    'title' => 'Instagram (@'.$userHandle.')',
                    'subtitle' => 'Aktivitas kampus & dokumentasi kegiatan',
                    'url' => 'https://instagram.com/'.$userHandle,
                    'is_visible' => true,
                ],
                [
                    'id' => 4,
                    'type' => 'github',
                    'title' => 'Proyek Kuliah & Tugas Akhir',
                    'subtitle' => 'Kumpulan laporan riset IoT & sistem tertanam',
                    'url' => 'https://github.com/'.$userHandle,
                    'is_visible' => true,
                ],
            ];
        }

        $kampus = [
            'nama_lengkap' => $displayName,
            'program_studi' => $programStudi,
            'universitas' => $universitas,
            'angkatan' => $angkatan,
            'domisili' => $domisili,
            'telepon_kampus' => $savedData['telepon_kampus'] ?? $cleanWa,
            'email_kampus' => $savedData['email_kampus'] ?? $user->email ?? 'student@unm.ac.id',
            'avatar_url' => $avatarUrl,
            'links' => $links,
        ];

        return view('user.profile_kampus', [
            'user' => $user,
            'profile' => $user->profile,
            'primaryTag' => $primaryTag,
            'tagCode' => $primaryTag?->tag_code ?? 'NTAG215',
            'kampus' => $kampus,
        ]);
    }

    /**
     * Simpan pembaruan profil Mode Kampus (FR-PRF-01 & FR-PUB-09).
     */
    public function updateKampus(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'program_studi' => ['required', 'string', 'max:100'],
            'universitas' => ['required', 'string', 'max:150'],
            'angkatan' => ['required', 'string', 'max:30'],
            'domisili' => ['required', 'string', 'max:100'],
            'telepon_kampus' => ['required', 'string', 'max:30'],
            'email_kampus' => ['required', 'email', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'links_json' => ['nullable', 'string'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap mahasiswa wajib diisi.',
            'program_studi.required' => 'Program studi wajib diisi.',
            'universitas.required' => 'Nama universitas / perguruan tinggi wajib diisi.',
            'angkatan.required' => 'Tahun angkatan wajib diisi.',
            'domisili.required' => 'Domisili / kota kampus wajib diisi.',
            'telepon_kampus.required' => 'Nomor WhatsApp & seluler wajib diisi.',
            'email_kampus.required' => 'Email akademik wajib diisi.',
            'email_kampus.email' => 'Format email akademik tidak valid.',
            'photo.image' => 'File foto profil harus berupa gambar.',
            'photo.max' => 'Ukuran foto profil maksimal 5 MB.',
        ]);

        $photoPath = $user->profile?->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && ! str_starts_with($photoPath, 'images/') && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('profiles', 'public');
        }

        // Normalisasi nomor WhatsApp
        $rawWa = (string) $validated['telepon_kampus'];
        $cleanWa = preg_replace('/[^0-9]/', '', $rawWa);
        if (Str::startsWith($cleanWa, '0')) {
            $cleanWa = '62'.substr($cleanWa, 1);
        } elseif (! Str::startsWith($cleanWa, '62') && ! empty($cleanWa)) {
            $cleanWa = '62'.$cleanWa;
        }

        // Parse JSON Links
        $links = [];
        if ($request->filled('links_json')) {
            $decoded = json_decode((string) $request->links_json, true);
            if (is_array($decoded)) {
                $links = $decoded;
            }
        }

        $headline = $validated['program_studi'].' • '.$validated['universitas'];
        $angkatanClean = preg_replace('/^Angkatan\s+/i', '', trim($validated['angkatan']));
        $bio = 'Angkatan '.$angkatanClean.' • '.$validated['domisili'];

        $newData = [
            'nama_mahasiswa' => $validated['nama_lengkap'],
            'jurusan' => $validated['program_studi'],
            'program_studi' => $validated['program_studi'],
            'universitas' => $validated['universitas'],
            'angkatan' => $angkatanClean,
            'domisili' => $validated['domisili'],
            'headline' => $headline,
            'bio' => $bio,
            'telepon_kampus' => ! empty($cleanWa) ? '+'.$cleanWa : $validated['telepon_kampus'],
            'email_kampus' => $validated['email_kampus'],
            'links' => $links,
        ];

        $modeRecord = ModeData::firstOrNew([
            'user_id' => $user->id,
            'mode' => 'kampus',
        ]);
        $modeRecord->data = $newData;
        $modeRecord->save();

        // Update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => $validated['nama_lengkap'],
                'headline' => $headline,
                'bio' => $bio,
                'photo_path' => $photoPath,
                'active_mode' => 'kampus',
            ]
        );

        if (! empty($cleanWa)) {
            $user->no_whatsapp = '+'.$cleanWa;
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

        // Sinkronisasi daftar tautan kampus ke tabel links
        if (! empty($links)) {
            foreach ($links as $idx => $linkItem) {
                if (empty($linkItem['url'])) {
                    continue;
                }
                $type = $linkItem['type'] ?? 'academic';
                $title = $linkItem['title'] ?? 'Tautan Kampus';
                $subtitle = $linkItem['subtitle'] ?? '';
                $label = ! empty($subtitle) ? ($title.'|'.$subtitle) : $title;

                Link::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'url' => $linkItem['url'],
                    ],
                    [
                        'type' => $type,
                        'label' => $label,
                        'sort_order' => $idx + 2,
                        'is_visible' => $linkItem['is_visible'] ?? true,
                    ]
                );
            }
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Profil Mode Kampus berhasil disimpan & disinkronkan ke Tag!');
    }

    /**
     * Tampilkan halaman Edit Profil Mode Kerja (Portofolio & Karir Profesional).
     */
    public function editKerja(Request $request): View
    {
        $user = $request->user()->load(['profile', 'tags', 'links']);
        $primaryTag = $user->tags()->where('status', 'aktif')->first() ?? $user->tags()->first();

        // 1. Ambil data tersimpan Mode Kerja
        $kerjaRecord = ModeData::where('user_id', $user->id)
            ->where('mode', 'kerja')
            ->first();
        $savedData = $kerjaRecord?->data ?? [];

        // 2. Ambil data Mode Kampus untuk fitur "Salin dari Profil Kampus"
        $kampusRecord = ModeData::where('user_id', $user->id)
            ->where('mode', 'kampus')
            ->first();
        $kampusSaved = $kampusRecord?->data ?? [];

        // Avatar foto resolusi
        $avatarUrl = null;
        if ($user->profile?->photo_path) {
            $avatarUrl = file_exists(public_path($user->profile->photo_path))
                ? asset($user->profile->photo_path)
                : asset('storage/'.$user->profile->photo_path);
        } elseif (file_exists(public_path('images/avatar_andi.jpg')) && str_contains(strtolower($user->name), 'andi')) {
            $avatarUrl = asset('images/avatar_andi.jpg');
        }

        // WhatsApp normalisasi
        $cleanWa = $user->no_whatsapp ?? '';
        if (Str::startsWith($cleanWa, '62')) {
            $cleanWa = substr($cleanWa, 2);
        } elseif (Str::startsWith($cleanWa, '0')) {
            $cleanWa = substr($cleanWa, 1);
        } elseif (Str::startsWith($cleanWa, '+62')) {
            $cleanWa = substr($cleanWa, 3);
        }

        $displayName = $user->profile?->display_name ?? $user->name ?? 'Andi Muhammad';
        $userSlug = Str::slug($displayName);

        // Siapkan tautan default profesional jika belum ada
        $links = $savedData['links'] ?? null;
        if (empty($links)) {
            $links = [
                [
                    'id' => 1,
                    'type' => 'portfolio',
                    'title' => 'Portofolio Digital',
                    'subtitle' => 'Koleksi aplikasi web, mobile, & riset IoT',
                    'url' => 'https://'.$userSlug.'.dev',
                    'is_visible' => true,
                ],
                [
                    'id' => 2,
                    'type' => 'linkedin',
                    'title' => 'LinkedIn Profesional',
                    'subtitle' => 'Rekam jejak karir, pengalaman, & keahlian',
                    'url' => 'https://linkedin.com/in/'.$userSlug,
                    'is_visible' => true,
                ],
                [
                    'id' => 3,
                    'type' => 'github',
                    'title' => 'GitHub & Repositori Proyek',
                    'subtitle' => 'Source code eksperimen embedded & web',
                    'url' => 'https://github.com/'.$userSlug,
                    'is_visible' => true,
                ],
                [
                    'id' => 4,
                    'type' => 'behance',
                    'title' => 'Behance / Galeri Desain UI',
                    'subtitle' => 'Studi kasus perancangan antarmuka sistem',
                    'url' => 'https://behance.net/'.$userSlug,
                    'is_visible' => true,
                ],
            ];
        }

        $kerja = [
            'nama_lengkap' => $savedData['nama_profesional'] ?? $savedData['nama_lengkap'] ?? $displayName,
            'posisi' => $savedData['posisi'] ?? $savedData['headline'] ?? 'Software Engineer & IoT Specialist',
            'perusahaan' => $savedData['perusahaan'] ?? 'PT Celebes Digital Solusindo',
            'lokasi_kantor' => $savedData['lokasi_kantor'] ?? $savedData['lokasi'] ?? 'Makassar, Sulawesi Selatan',
            'bio' => $savedData['bio'] ?? 'Fokus membangun sistem web modern dan perangkat keras pintar berbasis mikrokontroler.',
            'telepon_kerja' => $savedData['telepon_kerja'] ?? $cleanWa,
            'email_kerja' => $savedData['email_kerja'] ?? ($user->email ? (str_contains($user->email, '@student') ? str_replace('@student.unm.ac.id', '@flexitag.id', $user->email) : $user->email) : 'contact@flexitag.id'),
            'avatar_url' => $avatarUrl,
            'cv_file_path' => $savedData['cv_file_path'] ?? null,
            'cv_original_name' => $savedData['cv_original_name'] ?? null,
            'links' => $links,
        ];

        // Format data kampus untuk disalin dengan 1 klik
        $prodiKampus = $kampusSaved['program_studi'] ?? 'Teknik Komputer';
        $univKampus = $kampusSaved['universitas'] ?? 'Universitas Negeri Makassar';
        $domisiliKampus = $kampusSaved['domisili'] ?? 'Makassar, Sulawesi Selatan';
        $waKampus = $kampusSaved['telepon_kampus'] ?? $user->no_whatsapp ?? '';
        $cleanWaKampus = preg_replace('/[^0-9]/', '', $waKampus);
        if (Str::startsWith($cleanWaKampus, '62')) {
            $cleanWaKampus = substr($cleanWaKampus, 2);
        } elseif (Str::startsWith($cleanWaKampus, '0')) {
            $cleanWaKampus = substr($cleanWaKampus, 1);
        }

        $kampusForCopy = [
            'nama_lengkap' => $kampusSaved['nama_mahasiswa'] ?? $kampusSaved['nama_lengkap'] ?? $displayName,
            'posisi' => 'Mahasiswa '.$prodiKampus.' • Aspiring Tech Professional',
            'perusahaan' => $univKampus.' / Project Intern',
            'lokasi_kantor' => $domisiliKampus,
            'bio' => 'Mahasiswa '.$prodiKampus.' di '.$univKampus.' yang aktif membangun portofolio proyek terapan dan siap berkontribusi di industri profesional.',
            'telepon_kerja' => $cleanWaKampus,
            'email_kerja' => $kampusSaved['email_kampus'] ?? $user->email,
            'links' => [
                [
                    'id' => 1,
                    'type' => 'portfolio',
                    'title' => 'Portofolio Proyek Mahasiswa',
                    'subtitle' => 'Koleksi tugas akhir & laporan riset praktikum',
                    'url' => 'https://'.$userSlug.'.dev',
                    'is_visible' => true,
                ],
                [
                    'id' => 2,
                    'type' => 'linkedin',
                    'title' => 'LinkedIn Mahasiswa & Karir',
                    'subtitle' => 'Jejaring akademik, lomba, & sertifikasi',
                    'url' => 'https://linkedin.com/in/'.$userSlug,
                    'is_visible' => true,
                ],
                [
                    'id' => 3,
                    'type' => 'github',
                    'title' => 'GitHub Repositori Proyek',
                    'subtitle' => 'Kumpulan kode riset & eksperimen perkuliahan',
                    'url' => 'https://github.com/'.$userSlug,
                    'is_visible' => true,
                ],
                [
                    'id' => 4,
                    'type' => 'behance',
                    'title' => 'Galeri Desain & Karya Kreatif',
                    'subtitle' => 'Dokumentasi perancangan UI/UX & media sistem',
                    'url' => 'https://behance.net/'.$userSlug,
                    'is_visible' => true,
                ],
            ],
        ];

        return view('user.profile_kerja', [
            'user' => $user,
            'profile' => $user->profile,
            'primaryTag' => $primaryTag,
            'tagCode' => $primaryTag?->tag_code ?? 'NTAG215',
            'kerja' => $kerja,
            'kampusForCopy' => $kampusForCopy,
        ]);
    }

    /**
     * Simpan pembaruan profil Mode Kerja (Portofolio & Karir Profesional).
     */
    public function updateKerja(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'posisi' => ['required', 'string', 'max:120'],
            'perusahaan' => ['nullable', 'string', 'max:150'],
            'lokasi_kantor' => ['required', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:500'],
            'telepon_kerja' => ['required', 'string', 'max:30'],
            'email_kerja' => ['required', 'email', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'links_json' => ['nullable', 'string'],
        ], [
            'nama_lengkap.required' => 'Nama lengkap profesional wajib diisi.',
            'posisi.required' => 'Posisi / jabatan karir wajib diisi.',
            'lokasi_kantor.required' => 'Lokasi kerja / domisili wajib diisi.',
            'telepon_kerja.required' => 'Nomor WhatsApp & seluler kerja wajib diisi.',
            'email_kerja.required' => 'Email profesional wajib diisi.',
            'email_kerja.email' => 'Format email profesional tidak valid.',
            'photo.image' => 'File foto profil harus berupa gambar.',
            'photo.max' => 'Ukuran foto profil maksimal 5 MB.',
            'cv_file.mimes' => 'File CV / resume harus berformat PDF.',
            'cv_file.max' => 'Ukuran file CV maksimal 10 MB.',
        ]);

        $photoPath = $user->profile?->photo_path;
        if ($request->hasFile('photo')) {
            if ($photoPath && ! str_starts_with($photoPath, 'images/') && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('profiles', 'public');
        }

        // Ambil data mode_data kerja lama jika ada
        $modeRecord = ModeData::firstOrNew([
            'user_id' => $user->id,
            'mode' => 'kerja',
        ]);
        $existingData = is_array($modeRecord->data) ? $modeRecord->data : (json_decode($modeRecord->data ?? '[]', true) ?: []);

        $cvFilePath = $existingData['cv_file_path'] ?? null;
        $cvOriginalName = $existingData['cv_original_name'] ?? null;

        if ($request->hasFile('cv_file')) {
            if ($cvFilePath && Storage::disk('public')->exists($cvFilePath)) {
                Storage::disk('public')->delete($cvFilePath);
            }
            $cvFile = $request->file('cv_file');
            $cvOriginalName = $cvFile->getClientOriginalName();
            $cvFilePath = $cvFile->store('uploads/cv', 'public');
        } elseif ($request->boolean('delete_cv')) {
            if ($cvFilePath && Storage::disk('public')->exists($cvFilePath)) {
                Storage::disk('public')->delete($cvFilePath);
            }
            $cvFilePath = null;
            $cvOriginalName = null;
        }

        // Normalisasi WhatsApp kerja
        $rawWa = (string) $validated['telepon_kerja'];
        $cleanWa = preg_replace('/[^0-9]/', '', $rawWa);
        if (Str::startsWith($cleanWa, '0')) {
            $cleanWa = '62'.substr($cleanWa, 1);
        } elseif (! Str::startsWith($cleanWa, '62') && ! empty($cleanWa)) {
            $cleanWa = '62'.$cleanWa;
        }

        // Parse JSON Links
        $links = [];
        if ($request->filled('links_json')) {
            $decoded = json_decode((string) $request->links_json, true);
            if (is_array($decoded)) {
                $links = $decoded;
            }
        }

        $headline = $validated['posisi'].(! empty($validated['perusahaan']) ? ' • '.$validated['perusahaan'] : '');

        $newData = [
            'nama_profesional' => $validated['nama_lengkap'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'posisi' => $validated['posisi'],
            'perusahaan' => $validated['perusahaan'] ?? '',
            'headline' => $headline,
            'lokasi_kantor' => $validated['lokasi_kantor'],
            'lokasi' => $validated['lokasi_kantor'],
            'bio' => $validated['bio'] ?? '',
            'telepon_kerja' => ! empty($cleanWa) ? '+'.$cleanWa : $validated['telepon_kerja'],
            'email_kerja' => $validated['email_kerja'],
            'cv_file_path' => $cvFilePath,
            'cv_original_name' => $cvOriginalName,
            'links' => $links,
        ];

        $modeRecord->data = $newData;
        $modeRecord->save();

        // Jika mode aktif pengguna saat ini adalah 'kerja', sinkronkan ke profil publik
        if (($user->profile?->active_mode ?? 'kampus') === 'kerja') {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name' => $validated['nama_lengkap'],
                    'headline' => $headline,
                    'bio' => $validated['bio'] ?? $validated['lokasi_kantor'],
                    'photo_path' => $photoPath,
                ]
            );
        }

        return redirect()->route('user.dashboard')
            ->with('status', 'Profil Mode Kerja berhasil disimpan & disinkronkan ke Tag!');
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

        // Jaga nama personal pengguna agar tidak tertimpa oleh nama usaha toko UMKM
        $personalName = $user->name ?: ($user->profile?->display_name ?? 'Pengguna FlexiTag');

        // Update profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'display_name' => $personalName,
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
        $displayName = $user->name ?: ($user->profile?->display_name ?? 'Pengguna FlexiTag');
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

            // Mode label mandiri per ganci (fallback ke activeMode profil jika belum diset)
            $tagMode = $tag->mode ?: $activeMode;
            $modeLabel = self::MODE_LABELS[$tagMode] ?? 'Mode Kampus';
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
                'mode' => $tagMode,
                'mode_label' => $modeLabel,
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

    /**
     * Ubah mode mandiri untuk gantungan kunci / tag tertentu.
     */
    public function updateTagMode(Request $request, int|string $id): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $tag = $user->role === 'admin'
            ? Tag::findOrFail($id)
            : $user->tags()->findOrFail($id);

        $validated = $request->validate([
            'mode' => ['required', 'string', 'in:kampus,kerja,umkm,hilang'],
        ], [
            'mode.required' => 'Pilihan mode tag wajib ditentukan.',
            'mode.in' => 'Pilihan mode tidak valid.',
        ]);

        $tag->mode = $validated['mode'];
        $tag->save();

        $modeLabel = self::MODE_LABELS[$tag->mode] ?? ucfirst($tag->mode);
        $message = "Mode untuk Tag {$tag->tag_code} berhasil diubah ke {$modeLabel}!";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'tag_id' => $tag->id,
                'mode' => $tag->mode,
                'mode_label' => $modeLabel,
            ]);
        }

        return back()->with('status', $message);
    }

    /**
     * Klaim dan hubungkan tag / merchandise baru langsung dari halaman Merch.
     */
    public function claimTag(Request $request): RedirectResponse
    {
        $user = $request->user();

        $key = 'tag-activation:'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);

            return back()->with('error', "Terlalu banyak percobaan gagal. Silakan coba lagi dalam {$minutes} menit.");
        }

        $validated = $request->validate([
            'activation_code' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9\-\s]+$/'],
        ], [
            'activation_code.required' => 'Kode aktivasi wajib diisi.',
            'activation_code.max' => 'Kode aktivasi terlalu panjang.',
            'activation_code.regex' => 'Kode hanya boleh berisi huruf, angka, dan tanda hubung (-).',
        ]);

        $rawCode = strtoupper(trim((string) $validated['activation_code']));
        $cleanCode = preg_replace('/[^A-Z0-9]/', '', $rawCode);

        $result = DB::transaction(function () use ($rawCode, $cleanCode, $user) {
            $tag = Tag::where(function ($query) use ($rawCode, $cleanCode) {
                $query->where('activation_code', $rawCode)
                    ->orWhere('tag_code', $rawCode)
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(activation_code, '-', ''), ' ', '')) = ?", [$cleanCode])
                    ->orWhereRaw("UPPER(REPLACE(REPLACE(tag_code, '-', ''), ' ', '')) = ?", [$cleanCode]);
            })->lockForUpdate()->first();

            if (! $tag) {
                return ['wrong', null];
            }

            if ($tag->status === 'nonaktif') {
                return ['disabled', null];
            }

            if ($tag->status === 'aktif') {
                return [(int) $tag->user_id === (int) $user->id ? 'own' : 'used', null];
            }

            if ($tag->status !== 'belum_aktif' || $tag->user_id !== null) {
                return ['used', null];
            }

            $tag->user_id = $user->id;
            $tag->status = 'aktif';
            $tag->mode = $user->profile?->active_mode ?? 'kampus';
            $tag->activated_at = now();
            $tag->save();

            return ['ok', $tag];
        });

        [$state, $tag] = $result;

        if ($state === 'wrong') {
            RateLimiter::hit($key, 600);

            return back()->with('error', 'Kode aktivasi tidak ditemukan. Pastikan Anda memasukkan kode yang tertera pada kemasan.');
        }

        if ($state === 'disabled') {
            RateLimiter::hit($key, 600);

            return back()->with('error', 'Tag ini sedang dinonaktifkan oleh sistem. Hubungi administrator.');
        }

        if ($state === 'own') {
            return back()->with('info', 'Tag ini sudah terhubung ke akun Anda.');
        }

        if ($state === 'used') {
            RateLimiter::hit($key, 600);

            return back()->with('error', 'Tag ini sudah diaktivasi oleh pengguna lain.');
        }

        RateLimiter::clear($key);
        Log::info('Tag diklaim via halaman Merch', ['user_id' => $user->id, 'tag_id' => $tag->id]);

        return redirect()->route('user.merch')
            ->with('status', "Tag baru ({$tag->tag_code}) berhasil ditambahkan ke akun Anda dan terhubung ke profil aktif!");
    }
}
