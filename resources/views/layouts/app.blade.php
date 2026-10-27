<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIP-Lab')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b3d91;
            --sidebar: #f4f7fb;
            --text: #1f2937;
            --muted: #6b7280;
            --success: #1f9d64;
            --warning: #f59e0b;
            --danger: #dc2626;
            --card: #ffffff;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #f5f8ff 0%, #eef3ff 100%);
            color: var(--text);
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar);
            min-height: 100vh;
            border-right: 1px solid var(--border);
            padding: 20px 16px;
        }

        .nav-link {
            color: var(--text);
            border-radius: 12px;
            padding: 10px 12px;
            font-weight: 500;
        }

        .nav-link.active, .nav-link:hover {
            background: #e8f1ff;
            color: var(--primary-dark);
        }

        .content {
            flex: 1;
            padding: 24px;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 20px;
        }

        .badge-status {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-pending { background: #fff3cd; color: #b45309; }
        .badge-success { background: #d1fae5; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
            border: none;
        }

        .hero {
            background: linear-gradient(135deg, #0d6efd 0%, #1d4ed8 100%);
            color: white;
            border-radius: 22px;
            padding: 28px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        @auth
            <aside class="sidebar">
                <div class="d-flex align-items-center gap-2 mb-4 px-2">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width:42px;height:42px;">
                        <i class="bi bi-flask"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">SIP-Lab</div>
                        <small class="text-muted">Laboratorium Riset</small>
                    </div>
                </div>

                @if(Auth::user()->role === 'mahasiswa')
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link active" href="{{ route('student.dashboard') }}"><i class="bi bi-grid me-2"></i> Dashboard</a>
                        <a class="nav-link" href="{{ route('student.inventory') }}"><i class="bi bi-boxes me-2"></i> Inventaris</a>
                        <a class="nav-link" href="{{ route('booking.create') }}"><i class="bi bi-calendar-plus me-2"></i> Ajukan Peminjaman</a>
                        <a class="nav-link" href="{{ route('student.history') }}"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
                        <a class="nav-link" href="{{ route('student.notifications') }}"><i class="bi bi-bell me-2"></i> Notifikasi</a>
                    </nav>
                @elseif(Auth::user()->role === 'staff')
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link active" href="{{ route('staff.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                        <a class="nav-link" href="{{ route('staff.verification') }}"><i class="bi bi-check2-square me-2"></i> Verifikasi</a>
                    </nav>
                @elseif(Auth::user()->role === 'kepala_lab')
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link active" href="{{ route('lab-head.dashboard') }}"><i class="bi bi-clipboard-check me-2"></i> Dashboard</a>
                        <a class="nav-link" href="{{ route('lab-head.approval') }}"><i class="bi bi-briefcase-check me-2"></i> Persetujuan</a>
                    </nav>
                @elseif(Auth::user()->role === 'admin')
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}"><i class="bi bi-people me-2"></i> Dashboard</a>
                        <a class="nav-link" href="{{ route('admin.reports') }}"><i class="bi bi-bar-chart me-2"></i> Laporan</a>
                    </nav>
                @endif

                <div class="mt-5 pt-3 border-top">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                    </form>
                </div>
            </aside>
        @endauth

        <main class="content flex-grow-1">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
