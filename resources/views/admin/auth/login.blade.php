<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login | GARML</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="login-page">
    <div class="login-shell">
        <aside class="login-visual">
            <div class="login-visual-top">
                <div class="visual-badge">
                    <span class="brand-mark">G</span>
                    <span>GARML Admin</span>
                </div>

                <h2>Organisasi &amp; Arsitektur Komputer</h2>
                <p>Platform belajar interaktif berbasis AR untuk memahami struktur, komponen, dan cara kerja sistem komputer.</p>

                <ul class="visual-points">
                    <li>
                        <span class="point-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.2A2.2 2.2 0 0 1 6.2 4h11.6A2.2 2.2 0 0 1 20 6.2v11.6A2.2 2.2 0 0 1 17.8 20H6.2A2.2 2.2 0 0 1 4 17.8V6.2Zm2.2-.7a.7.7 0 0 0-.7.7v11.6c0 .39.31.7.7.7h11.6a.7.7 0 0 0 .7-.7V6.2a.7.7 0 0 0-.7-.7H6.2Zm2.3 2.2h7.1v1.6H8.5V7.7Zm0 3.1h7.1v1.6H8.5v-1.6Zm0 3.1h4.6v1.6H8.5v-1.6Z" /></svg>
                        </span>
                        Kelola kelas &amp; modul pembelajaran
                    </li>
                    <li>
                        <span class="point-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4 6.5v5.2c0 4.68 3.42 8.98 8 10.3 4.58-1.32 8-5.62 8-10.3V6.5L12 3Zm-1.1 12.6-3.4-3.4 1.27-1.27 2.13 2.13 4.83-4.83 1.27 1.27-6.1 6.1Z" /></svg>
                        </span>
                        Materi interaktif &amp; konten AR
                    </li>
                    <li>
                        <span class="point-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19h2v-6H4v6Zm14-9h2v9h-2v-9ZM4 13V4h6v9H4Zm8-9h8v6h-8V4Zm0 8h8v7h-8v-7Z" /></svg>
                        </span>
                        Pantau progres mahasiswa real-time
                    </li>
                </ul>
            </div>

            <div class="login-chip" aria-hidden="true">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <g class="chip-pulse" stroke="#fff" stroke-width="1.4" fill="none">
                        <line x1="50" y1="0" x2="50" y2="18" />
                        <line x1="50" y1="82" x2="50" y2="100" />
                        <line x1="0" y1="50" x2="18" y2="50" />
                        <line x1="82" y1="50" x2="100" y2="50" />
                        <line x1="18" y1="18" x2="30" y2="30" />
                        <line x1="82" y1="18" x2="70" y2="30" />
                        <line x1="18" y1="82" x2="30" y2="70" />
                        <line x1="82" y1="82" x2="70" y2="70" />
                    </g>
                    <rect x="24" y="24" width="52" height="52" rx="8" fill="rgba(255,255,255,0.12)" stroke="#fff" stroke-width="1.4" />
                    <rect x="36" y="36" width="28" height="28" rx="4" fill="none" stroke="#fff" stroke-width="1.2" opacity="0.8" />
                    <circle cx="50" cy="50" r="4" fill="#fff" />
                </svg>
            </div>
        </aside>

        <div class="login-panel">
            <div class="login-panel-inner">
                <div class="login-brand">
                    <div class="brand-mark">G</div>
                    <div>
                        <p class="eyebrow">Admin Portal</p>
                        <h1>GARML</h1>
                    </div>
                </div>

                <div class="login-copy">
                    <h2>Masuk ke pusat kendali</h2>
                    <p>Kelola kelas, modul, materi, challenge, dan konten AR untuk program belajar.</p>
                </div>

                @if ($errors->any())
                <div class="alert alert-error">
                    {{ $errors->first() }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" class="login-form">
                    @csrf

                    <label class="field-group">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@garml.id" required />
                    </label>

                    <label class="field-group">
                        <span>Password</span>
                        <input type="password" name="password" placeholder="••••••••" required />
                    </label>

                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>