<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIP-LAB - Sistem Informasi Peminjaman Laboratorium')</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --brand-navy: #0f172a;        /* Figma Slate 900 */
            --brand-navy-light: #1e293b;  /* Figma Slate 800 */
            --brand-blue: #2563eb;        /* Figma Royal Blue */
            --brand-blue-hover: #1d4ed8;
            --brand-blue-light: #eff6ff;
            --sidebar-bg: #ffffff;
            --body-bg: #f8fafc;           /* Clean soft slate */
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
            --text-main: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --shadow-subtle: 0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.05);
            --shadow-card: 0 4px 6px -1px rgba(15, 23, 42, 0.04), 0 2px 4px -2px rgba(15, 23, 42, 0.04);
            --shadow-hover: 0 12px 24px -4px rgba(15, 23, 42, 0.08), 0 4px 6px -2px rgba(15, 23, 42, 0.04);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-body);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Layout Structure */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 270px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            padding: 24px 16px 20px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 24px 8px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.3rem;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .brand-text-title {
            font-weight: 800;
            font-size: 1.15rem;
            line-height: 1.2;
            letter-spacing: -0.02em;
            color: var(--text-main);
        }

        .brand-text-sub {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .sidebar-nav-section {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-light);
            padding: 12px 12px 6px 12px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 4px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 4px;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        .nav-item-link i {
            font-size: 1.15rem;
            transition: transform 0.15s ease;
        }

        .nav-item-link:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
            transform: translateX(3px);
        }

        .nav-item-link.active {
            background-color: var(--brand-navy);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        }

        .nav-item-link.active i {
            color: #60a5fa;
        }

        .sidebar-footer {
            padding-top: 16px;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        .user-profile-widget {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius-md);
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            margin-bottom: 12px;
        }

        .user-avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .user-info-text {
            flex-grow: 1;
            min-width: 0;
        }

        .user-name-label {
            font-size: 0.84rem;
            font-weight: 600;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-badge {
            font-size: 0.68rem;
            font-weight: 600;
            color: #2563eb;
            text-transform: capitalize;
            background: #eff6ff;
            padding: 2px 8px;
            border-radius: 999px;
            display: inline-block;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            flex-grow: 1;
            margin-left: 270px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-wrapper.no-sidebar {
            margin-left: 0;
        }

        /* Topbar Header */
        .topbar-header {
            height: 70px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1030;
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-title-section h1 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .topbar-title-section p {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin: 0;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .date-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background-color: #f1f5f9;
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background: #ffffff;
            color: var(--text-muted);
            text-decoration: none;
            position: relative;
            transition: all 0.15s ease;
        }

        .btn-icon:hover {
            background: #f8fafc;
            color: var(--text-main);
            border-color: var(--border-hover);
        }

        .indicator-dot {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border: 2px solid #ffffff;
            border-radius: 50%;
            position: absolute;
            top: 6px;
            right: 6px;
        }

        /* Page Content Canvas */
        .page-content {
            padding: 32px;
            flex-grow: 1;
        }

        /* Cards & Containers */
        .card-custom {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            transition: all 0.2s ease;
        }

        .card-custom:hover {
            border-color: var(--border-hover);
        }

        /* Metric / Stat Card */
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 22px 20px;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: var(--border-hover);
        }

        .stat-card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .stat-card-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon-blue { background: #eff6ff; color: #2563eb; }
        .stat-icon-emerald { background: #ecfdf5; color: #059669; }
        .stat-icon-amber { background: #fef3c7; color: #d97706; }
        .stat-icon-purple { background: #f5f3ff; color: #7c3aed; }

        .stat-card-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .stat-card-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .stat-card-footer {
            margin-top: 10px;
            font-size: 0.76rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Modern Tables */
        .table-custom-wrapper {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-card);
        }

        .table-custom {
            width: 100%;
            margin-bottom: 0;
            vertical-align: middle;
            border-color: #f1f5f9;
        }

        .table-custom thead {
            background-color: #f8fafc;
        }

        .table-custom thead th {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .table-custom tbody td {
            padding: 16px 20px;
            font-size: 0.86rem;
            color: var(--text-body);
            border-bottom: 1px solid #f1f5f9;
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .table-custom tbody tr:hover td {
            background-color: #f8fafc;
        }

        /* Status Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 0.74rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .badge-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        .badge-pending::before { background: #d97706; }

        .badge-success, .badge-approved {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .badge-success::before, .badge-approved::before { background: #10b981; }

        .badge-danger, .badge-rejected {
            background: #fff1f2;
            color: #9f1239;
            border: 1px solid #fecdd3;
        }
        .badge-danger::before, .badge-rejected::before { background: #f43f5e; }

        .badge-info {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .badge-info::before { background: #3b82f6; }

        .badge-warning {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fef3c7;
        }
        .badge-warning::before { background: #f59e0b; }

        /* Buttons & Forms */
        .btn-brand-primary {
            background-color: var(--brand-navy);
            color: #ffffff;
            border: 1px solid var(--brand-navy);
            border-radius: var(--radius-md);
            padding: 9px 18px;
            font-size: 0.86rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.08);
        }

        .btn-brand-primary:hover {
            background-color: #1e293b;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
        }

        .btn-brand-blue {
            background-color: var(--brand-blue);
            color: #ffffff;
            border: 1px solid var(--brand-blue);
            border-radius: var(--radius-md);
            padding: 9px 18px;
            font-size: 0.86rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-brand-blue:hover {
            background-color: var(--brand-blue-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-brand-outline {
            background-color: #ffffff;
            color: var(--text-body);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 9px 18px;
            font-size: 0.86rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s ease;
        }

        .btn-brand-outline:hover {
            background-color: #f8fafc;
            color: var(--text-main);
            border-color: var(--border-hover);
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 10px 14px;
            font-size: 0.88rem;
            color: var(--text-main);
            background-color: #ffffff;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        /* Banner Hero inside dashboard */
        .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            border-radius: var(--radius-xl);
            padding: 32px 36px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, rgba(59, 130, 246, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* Mobile drawer toggle */
        .mobile-toggle-btn {
            display: none;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .mobile-toggle-btn {
                display: flex;
            }
            .page-content {
                padding: 20px 16px;
            }
            .topbar-header {
                padding: 0 16px;
            }
        }
    </style>
</head>
<body>

    <div class="app-wrapper">
        @auth
            <!-- Sidebar Navigation -->
            <aside class="sidebar" id="appSidebar">
                <!-- Brand Header -->
                <a href="{{ url('/') }}" class="sidebar-brand">
                    <div class="brand-logo-icon">
                        <i class="bi bi-flask"></i>
                    </div>
                    <div>
                        <div class="brand-text-title">SIP-LAB</div>
                        <div class="brand-text-sub">Laboratorium Riset</div>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="sidebar-nav">
                    @if(Auth::user()->role === 'mahasiswa')
                        <div class="sidebar-nav-section">Menu Mahasiswa</div>
                        <a class="nav-item-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('student.inventory*') ? 'active' : '' }}" href="{{ route('student.inventory') }}">
                            <i class="bi bi-box-seam-fill"></i>
                            <span>Ruang & Inventaris</span>
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('booking.create') ? 'active' : '' }}" href="{{ route('booking.create') }}">
                            <i class="bi bi-calendar-plus-fill"></i>
                            <span>Ajukan Peminjaman</span>
                        </a>

                        <div class="sidebar-nav-section mt-2">Aktivitas</div>
                        <a class="nav-item-link {{ request()->routeIs('student.history*') ? 'active' : '' }}" href="{{ route('student.history') }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Peminjaman</span>
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('student.notifications*') ? 'active' : '' }}" href="{{ route('student.notifications') }}">
                            <i class="bi bi-bell-fill"></i>
                            <span>Notifikasi</span>
                            @php $notifCount = Auth::user()->notifikasi()->where('sudah_dibaca', false)->count(); @endphp
                            @if($notifCount > 0)
                                <span class="badge bg-primary rounded-pill ms-auto" style="font-size:0.7rem;">{{ $notifCount }}</span>
                            @endif
                        </a>
                    @elseif(Auth::user()->role === 'staff')
                        <div class="sidebar-nav-section">Staf Sarpras</div>
                        <a class="nav-item-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('staff.verification*') ? 'active' : '' }}" href="{{ route('staff.verification') }}">
                            <i class="bi bi-check2-square"></i>
                            <span>Antrean Verifikasi</span>
                            @php $pendingCount = \App\Models\Peminjaman::whereIn('status', ['Menunggu Verifikasi', 'Perlu Revisi'])->count(); @endphp
                            @if($pendingCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size:0.7rem;">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('staff.inventory*') ? 'active' : '' }}" href="{{ route('staff.inventory') }}">
                            <i class="bi bi-box-seam"></i>
                            <span>Katalog & Inventaris</span>
                        </a>
                    @elseif(Auth::user()->role === 'kepala_lab')
                        <div class="sidebar-nav-section">Pimpinan Lab</div>
                        <a class="nav-item-link {{ request()->routeIs('lab-head.dashboard') ? 'active' : '' }}" href="{{ route('lab-head.dashboard') }}">
                            <i class="bi bi-bar-chart-steps"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('lab-head.approval*') ? 'active' : '' }}" href="{{ route('lab-head.approval') }}">
                            <i class="bi bi-clipboard2-check-fill"></i>
                            <span>Persetujuan Tingkat II</span>
                            @php $headPending = \App\Models\Peminjaman::where('status', 'Menunggu Persetujuan Kepala Lab')->count(); @endphp
                            @if($headPending > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:0.7rem;">{{ $headPending }}</span>
                            @endif
                        </a>
                        <a class="nav-item-link {{ request()->routeIs('lab-head.reports*') ? 'active' : '' }}" href="{{ route('lab-head.reports') }}">
                            <i class="bi bi-file-earmark-bar-graph-fill"></i>
                            <span>Laporan & Audit Lab</span>
                        </a>
                    @endif
                </div>

                <!-- Footer User Profile Widget & Logout -->
                <div class="sidebar-footer">
                    <div class="user-profile-widget">
                        <div class="user-avatar-circle">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info-text">
                            <div class="user-name-label">{{ Auth::user()->name }}</div>
                            <div class="user-role-badge">{{ str_replace('_', ' ', Auth::user()->role) }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-brand-outline w-100 justify-content-center text-danger border-danger-subtle">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Keluar Sistem</span>
                        </button>
                    </form>
                </div>
            </aside>
        @endauth

        <!-- Main Body Wrapper -->
        <div class="main-wrapper @guest no-sidebar @endguest">
            @auth
                <!-- Sticky Topbar Header -->
                <header class="topbar-header">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-icon mobile-toggle-btn" id="sidebarToggleBtn" type="button">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <div class="topbar-title-section">
                            <h1>@yield('header-title', 'Portal Laboratorium')</h1>
                            <p>@yield('header-sub', 'Kelola jadwal, inventaris alat, dan verifikasi laboratorium secara efisien')</p>
                        </div>
                    </div>

                    <div class="topbar-actions">
                        <div class="date-badge d-none d-md-inline-flex">
                            <i class="bi bi-calendar3 text-primary"></i>
                            <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>

                        @if(Auth::user()->role === 'mahasiswa')
                            <a href="{{ route('student.notifications') }}" class="btn-icon" title="Notifikasi">
                                <i class="bi bi-bell"></i>
                                @if(Auth::user()->notifikasi()->where('sudah_dibaca', false)->count() > 0)
                                    <span class="indicator-dot"></span>
                                @endif
                            </a>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-icon dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold">{{ Auth::user()->name }}</div>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger py-2">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>
            @endauth

            <!-- Main Page View Content -->
            <main class="page-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert" style="border-radius:12px; background:#ecfdf5; color:#065f46;">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" role="alert" style="border-radius:12px; background:#fff1f2; color:#9f1239;">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius:12px; background:#fff1f2; color:#9f1239;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-exclamation-circle-fill text-danger"></i>
                            <strong>Terdapat kesalahan pengisian formulir:</strong>
                        </div>
                        <ul class="mb-0 ps-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('appSidebar');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
