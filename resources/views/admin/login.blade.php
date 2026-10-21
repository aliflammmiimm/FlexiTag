<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FlexiTag — Admin Console Login</title>

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

        /* Subtle Toraja geometric background watermarks */
        .bg-cream {
            background-color: #FAF9F6;
        }

        .text-charcoal {
            color: #1A1A1A;
        }

        .text-maroon {
            color: #8B0000;
        }

        .text-gold {
            color: #D4AF37;
        }

        .bg-maroon-dark {
            background-color: #4A0000;
        }

        /* Custom soft transitions */
        .input-glow:focus-within {
            box-shadow: 0 0 0 3px rgba(139, 0, 0, 0.12);
            border-color: #8B0000;
        }
    </style>
</head>
<body class="bg-cream text-charcoal min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-[#8B0000] selection:text-[#D4AF37]">

    <!-- ========================================================= -->
    <!-- BACKGROUND WATERMARK: GEOMETRIC COMPASS / TORAJA SUNBURST -->
    <!-- ========================================================= -->
    <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
        <svg class="w-[680px] h-[680px] sm:w-[840px] sm:h-[840px] opacity-[0.06] text-[#8B0000]" viewBox="0 0 800 800" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Concentric delicate circles -->
            <circle cx="400" cy="400" r="380" stroke="currentColor" stroke-width="1.5" stroke-dasharray="8 6"/>
            <circle cx="400" cy="400" r="320" stroke="currentColor" stroke-width="1.2"/>
            <circle cx="400" cy="400" r="240" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4"/>
            <circle cx="400" cy="400" r="160" stroke="currentColor" stroke-width="1.2"/>
            <circle cx="400" cy="400" r="80" stroke="currentColor" stroke-width="1.5"/>

            <!-- Compass Crosshairs -->
            <line x1="20" y1="400" x2="780" y2="400" stroke="currentColor" stroke-width="1" stroke-dasharray="6 6"/>
            <line x1="400" y1="20" x2="400" y2="780" stroke="currentColor" stroke-width="1" stroke-dasharray="6 6"/>

            <!-- Major Cardinal Diamond Points -->
            <path d="M400 40 L425 375 L400 400 L375 375 Z" fill="currentColor"/>
            <path d="M400 760 L425 425 L400 400 L375 425 Z" fill="currentColor"/>
            <path d="M40 400 L375 425 L400 400 L375 375 Z" fill="currentColor"/>
            <path d="M760 400 L425 425 L400 400 L425 375 Z" fill="currentColor"/>

            <!-- Diagonal Ordinal Points (Pa'barre Allo rays) -->
            <path d="M145 145 L385 385 L400 400 L385 415 Z" fill="currentColor" opacity="0.6"/>
            <path d="M655 145 L415 385 L400 400 L415 415 Z" fill="currentColor" opacity="0.6"/>
            <path d="M145 655 L385 415 L400 400 L385 385 Z" fill="currentColor" opacity="0.6"/>
            <path d="M655 655 L415 415 L400 400 L415 385 Z" fill="currentColor" opacity="0.6"/>
        </svg>
    </div>

    <!-- ========================================================= -->
    <!-- TOP STATUS HEADERS                                        -->
    <!-- ========================================================= -->
    <div class="relative z-10 flex flex-col items-center mb-6 sm:mb-8 text-center">
        <!-- Archival Hardware System Indicator -->
        <div class="flex items-center gap-2 mb-3">
            <span class="w-2.5 h-2.5 rounded-full bg-[#D4AF37] shadow-[0_0_8px_rgba(212,175,55,0.6)]"></span>
            <span class="text-[11px] font-bold tracking-[0.22em] text-[#D4AF37] uppercase">
                Archival Hardware System
            </span>
        </div>

        <!-- Regional Node Pill Badge -->
        <div class="inline-flex items-center gap-2 bg-white/90 backdrop-blur-sm border border-[#EBE6DE] shadow-sm rounded-full px-4 py-1.5 text-xs text-charcoal">
            <span class="w-2 h-2 rounded-full bg-[#3B4728]"></span>
            <span class="text-[#555555]">Regional Node:</span>
            <span class="font-semibold text-charcoal">Makassar Core (UPG-01)</span>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- MAIN LOGIN CARD (PURE WHITE #FFFFFF)                      -->
    <!-- ========================================================= -->
    <div class="relative z-10 w-full max-w-[440px] bg-white rounded-[32px] shadow-[0_25px_60px_-15px_rgba(26,26,26,0.08),0_0_1px_1px_rgba(0,0,0,0.03)] border border-[#F0EBE1]/60 overflow-hidden">

        <!-- Top Maroon Accent Trim -->
        <div class="h-[5px] w-full bg-[#4E0000]"></div>

        <div class="p-8 sm:p-10">

            <!-- Card Header: Logo & Badge -->
            <div class="flex flex-col items-center text-center">
                
                <!-- Toraja / NFC Brand Icon -->
                <div class="w-14 h-14 rounded-[18px] bg-gradient-to-b from-[#5C0303] to-[#3B0000] shadow-[0_8px_20px_rgba(74,0,0,0.28)] flex items-center justify-center relative mb-4">
                    <svg class="w-7 h-7 text-[#D4AF37]" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Left NFC arcs -->
                        <path d="M7.5 9C6.2 10.4 5.5 12.1 5.5 14C5.5 15.9 6.2 17.6 7.5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10 11C9.4 11.9 9 12.9 9 14C9 15.1 9.4 16.1 10 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        
                        <!-- Right NFC arcs -->
                        <path d="M20.5 9C21.8 10.4 22.5 12.1 22.5 14C22.5 15.9 21.8 17.6 20.5 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M18 11C18.6 11.9 19 12.9 19 14C19 15.1 18.6 16.1 18 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        
                        <!-- Center Diamond / Toraja Core Motif -->
                        <path d="M14 9.5L17.5 14L14 18.5L10.5 14L14 9.5Z" stroke="currentColor" stroke-width="1.6" fill="#D4AF37" fill-opacity="0.25"/>
                        <circle cx="14" cy="14" r="1.5" fill="currentColor"/>
                    </svg>
                </div>

                <!-- Brand Title -->
                <h1 class="text-2xl font-bold tracking-tight text-charcoal">
                    Flexi<span class="text-maroon">Tag</span>
                </h1>

                <!-- Admin Console Pill -->
                <div class="mt-1.5 inline-block bg-[#F5F2EB] text-[#8C6D23] font-bold text-[10px] tracking-[0.2em] px-3 py-0.5 rounded-full uppercase">
                    Admin Console
                </div>

                <!-- Welcome Text -->
                <h2 class="mt-6 text-[22px] sm:text-[23px] font-bold text-charcoal tracking-tight">
                    Selamat Datang Pengelola
                </h2>
                <p class="mt-1 text-xs sm:text-sm text-[#737373]">
                    Silakan masuk untuk mengelola sistem.
                </p>
            </div>

            <!-- Error Notification Alert -->
            @if (isset($errors) && $errors->any())
                <div class="mt-5 p-3.5 rounded-2xl bg-[#FFF5F5] border border-[#FFD6D6] text-xs text-[#8B0000] flex items-start gap-2.5">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="font-medium">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-6 space-y-4">
                @csrf

                <!-- Email Input Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-charcoal mb-1.5">
                        Email
                    </label>
                    <div class="relative flex items-center bg-[#F6F5F2] hover:bg-[#F2F1ED] rounded-2xl px-4 py-3.5 border border-[#ECE8E1] transition-all duration-200 input-glow">
                        <svg class="w-5 h-5 text-[#8A8A85] mr-3 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                        </svg>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email', 'admin@flexitag.id') }}" 
                            required 
                            autofocus 
                            placeholder="admin@flexitag.id"
                            class="w-full bg-transparent text-sm text-charcoal placeholder-[#9E9E98] focus:outline-none font-medium"
                        >
                    </div>
                </div>

                <!-- Password Input Field -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-charcoal mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative flex items-center bg-[#F6F5F2] hover:bg-[#F2F1ED] rounded-2xl px-4 py-3.5 border border-[#ECE8E1] transition-all duration-200 input-glow">
                        <svg class="w-5 h-5 text-[#8A8A85] mr-3 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            placeholder="••••••••" 
                            class="w-full bg-transparent text-sm text-charcoal placeholder-[#9E9E98] focus:outline-none font-medium pr-2"
                        >
                        <!-- Password Toggle Button -->
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility()" 
                            class="text-[#8A8A85] hover:text-charcoal focus:outline-none transition p-1"
                            title="Tampilkan / Sembunyikan Sandi"
                        >
                            <!-- Eye icon (shown when hidden) -->
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <!-- Eye-slash icon (shown when visible) -->
                            <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password Row -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember" 
                            class="w-4 h-4 rounded border-[#D1CCC4] text-[#8B0000] focus:ring-[#8B0000] focus:ring-offset-0 cursor-pointer accent-[#8B0000]"
                        >
                        <span class="text-[#4A4A4A] font-medium">Ingat Saya</span>
                    </label>

                    <a href="javascript:void(0)" onclick="alert('Silakan hubungi Super Admin di Makassar Core untuk reset kredensial pengelola.')" class="text-maroon hover:text-[#5A0000] font-semibold transition">
                        Lupa Sandi?
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="mt-6 w-full bg-[#460000] hover:bg-[#5C0303] active:scale-[0.99] text-[#D4AF37] font-semibold text-sm py-3.5 px-6 rounded-2xl shadow-[0_10px_25px_rgba(70,0,0,0.30)] hover:shadow-[0_14px_30px_rgba(70,0,0,0.40)] flex items-center justify-center gap-2 transition-all duration-200 cursor-pointer"
                >
                    <span>Masuk ke Dashboard</span>
                    <span class="text-base font-bold transition-transform group-hover:translate-x-1">→</span>
                </button>

                <!-- Security Assurance Pill -->
                <div class="mt-6 bg-[#F6F5F2] rounded-full py-2.5 px-4 flex items-center justify-center gap-2 border border-[#EFECE6]/80">
                    <svg class="w-3.5 h-3.5 text-[#8A8A85]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                    <span class="text-[11px] font-medium text-[#737373]">
                        Enkripsi AES-256 <span class="mx-1">•</span> Otorisasi Node Resmi
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================= -->
    <!-- FOOTER OUTSIDE CARD                                       -->
    <!-- ========================================================= -->
    <footer class="relative z-10 mt-8 text-center px-4">
        <p class="text-xs text-[#8A8A85] font-normal leading-relaxed">
            &copy; 2025 Toraja NFC Authentication Core. <span class="mx-1">•</span> Artisanal Hardware &amp; Cryptographic Handshake Registry.
        </p>
    </footer>

    <!-- JavaScript for Password Toggle -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
