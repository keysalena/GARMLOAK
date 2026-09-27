<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin GARML')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/48.5.0/ckeditor5.css">

    <!-- CSS khusus untuk perbaikan layout Navbar dan Main Content -->
    <style>
        .topbar {
            background-color: #ffffff !important;
            position: sticky !important;
            top: 0;
            z-index: 50;
            border-bottom: 1px solid #e5e7eb;

            /* Penyesuaian agar navbar lebih tipis */
            padding-top: 0.75rem !important;
            padding-bottom: 0.75rem !important;
            height: auto !important;
        }

        .admin-main {
            /* Menambahkan ruang di atas agar konten (Overview) tidak menempel ke navbar */
            padding-top: 2rem !important;
        }
    </style>
</head>

<body class="admin-body">
    <div class="admin-shell-panel">
        @include('admin.partials.sidebar')

        <div class="admin-content-wrap">
            <header class="topbar">

                <button type="button" class="sidebar-toggle" aria-label="Buka menu" aria-expanded="false" aria-controls="adminSidebar">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6.5h16v2H4v-2Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z" />
                    </svg>
                </button>

                <!-- ========================================== -->
                <!-- AREA KIRI: TANGGAL & BREADCRUMB -->
                <!-- ========================================== -->
                <div class="topbar-left" style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- Tanggal Hari Ini -->
                    <div style="color: var(--text-muted, #6b7280); font-size: 0.85rem; font-weight: 500;">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>

                    <!-- Breadcrumb Global -->
                    @if (trim($__env->yieldContent('breadcrumb')))
                    <nav aria-label="breadcrumb">
                        <ol style="list-style: none; padding: 0; margin: 0; display: flex; align-items: center; gap: 8px; font-size: 13px;">
                            <li>
                                <a href="{{ route('admin.dashboard') }}" style="color: #6c757d; text-decoration: none; transition: color 0.2s;">Dashboard</a>
                            </li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                    @endif
                </div>
                <!-- ========================================== -->

                <div class="topbar-actions">

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