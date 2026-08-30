<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin GARML')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- CSS khusus untuk Topbar (Navbar) -->
    <style>
        .topbar {
            background-color: #ffffff !important; /* Background putih hanya untuk navbar */
            position: sticky !important; /* Membuat navbar menempel */
            top: 0; /* Menempel di paling atas */
            z-index: 50; /* Memastikan navbar berada di atas konten saat di-scroll */
            border-bottom: 1px solid #e5e7eb; /* Garis pemisah tipis dengan area konten */
        }
    </style>
</head>

<body class="admin-body">
    <div class="admin-shell-panel">
        @include('admin.partials.sidebar')

        <div class="admin-content-wrap">
            <header class="topbar">
                
                <!-- Tanggal Hari Ini (Pengganti Search) -->
                <div class="topbar-left" style="color: var(--text-muted, #6b7280); font-size: 0.9rem; font-weight: 500;">
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>

                <div class="topbar-actions">
                    <button class="icon-btn" aria-label="Inbox">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M5 7.5A2.5 2.5 0 0 1 7.5 5h9A2.5 2.5 0 0 1 19 7.5v8.9a2.6 2.6 0 0 1-2.6 2.6H7.6A2.6 2.6 0 0 1 5 16.4V7.5Zm2.5-.5a.5.5 0 0 0-.5.5v.6h10v-.6a.5.5 0 0 0-.5-.5h-9Zm-.5 4.1h10v5.8a1.1 1.1 0 0 1-1.1 1.1H7.6A1.1 1.1 0 0 1 6.5 17.1v-5.8Zm1.8 1.1v1.4h6.4v-1.4H8.3Z" />
                        </svg>
                    </button>
                    <button class="icon-btn" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3.5a5.5 5.5 0 0 1 5.5 5.5v3.4l1.5 3.9h-14l1.5-3.9V9A5.5 5.5 0 0 1 12 3.5Zm0 17a2.2 2.2 0 0 1-2.2-2.2h4.4A2.2 2.2 0 0 1 12 20.5Z" />
                        </svg>
                    </button>
                    
                    <!-- Data User Autentikasi (Dinamis) -->
                    @auth
                    <div class="profile-badge">
                        <div class="avatar">
                            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <strong>{{ auth()->user()->nama ?? auth()->user()->name ?? 'Admin' }}</strong>
                            <small>{{ auth()->user()->email ?? '' }}</small>
                        </div>
                    </div>
                    @endauth
                    
                </div>
            </header>

            <main class="admin-main">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="modal-backdrop" id="globalModal" aria-hidden="true">
        <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <button type="button" class="modal-close" aria-label="Close">×</button>
            <div class="modal-body" id="modalContent"></div>
        </div>
    </div>
</body>

</html>