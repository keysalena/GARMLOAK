<aside class="admin-sidebar" id="adminSidebar">
    <div class="brand-box">
        <div class="brand-mark">D</div>
        <div>
            <p class="eyebrow">Menu</p>
            <h2>Donezo</h2>
        </div>
    </div>

    <div class="sidebar-section">
        <span class="sidebar-label">Menu</span>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 13.5h7V20H4v-6.5Zm9-9h7v7h-7V4.5ZM13 13.5h7V20h-7v-6.5ZM4 4.5h7v7H4v-7Z" />
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.kelas.index') }}" class="nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 7.2A2.2 2.2 0 0 1 6.2 5h11.6A2.2 2.2 0 0 1 20 7.2v9.6A2.2 2.2 0 0 1 17.8 19H6.2A2.2 2.2 0 0 1 4 16.8V7.2Zm2.2-.7a.7.7 0 0 0-.7.7v9.6c0 .39.31.7.7.7h11.6a.7.7 0 0 0 .7-.7V7.2a.7.7 0 0 0-.7-.7H6.2Zm2.3 2.2h7.1v1.6H8.5V8.7Zm0 3.1h5.7v1.6H8.5v-1.6Z" />
                    </svg>
                </span>
                <span>Kelas</span>
            </a>
            <a href="{{ route('admin.modul.index') }}" class="nav-link {{ request()->routeIs('admin.modul.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 4.5h8.5L18 7.1v12.4a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 19.5V6A1.5 1.5 0 0 1 6 4.5Zm8 1.8V7h1.2l1.3 1.2H14Zm-7 3.2h8.5v1.5H7V9.5Zm0 3h8.5v1.5H7v-1.5Zm0 3h6.2v1.5H7v-1.5Z" />
                    </svg>
                </span>
                <span>Modul</span>
            </a>
            <a href="{{ route('admin.ar-content.index') }}" class="nav-link {{ request()->routeIs('admin.ar-content.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2.6 4.8 6.2v11.6L12 21.4l7.2-3.6V6.2L12 2.6Zm0 2.1 5.3 2.7L12 10.2 6.7 7.4 12 4.7Zm-5.3 4.2 4.8 2.4v6.5L6.7 15.9V8.9Zm10.6 0v7l-4.8 2.4v-6.5l4.8-2.4Z" />
                    </svg>
                </span>
                <span>AR Content</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-section sidebar-footer">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-button">Keluar</button>
        </form>
    </div>
</aside>