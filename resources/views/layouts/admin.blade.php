<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') — UQI Smart System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-50: #F0FDFA;
            --primary-100: #CCFBF1;
            --text: #0F172A;
            --text-secondary: #64748B;
            --border: #E2E8F0;
            --bg: #F8FAFC;
            --font: 'Plus Jakarta Sans', sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font); background: var(--bg); color: var(--text); }

        .admin-layout { display: flex; min-height: 100vh; }

        .admin-sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
            color: white;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 50;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-header h2 { font-size: 16px; font-weight: 700; }
        .sidebar-header span { font-size: 11px; opacity: 0.5; display: block; margin-top: 2px; }

        .sidebar-nav { padding: 16px 12px; }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar-nav a.active { background: var(--primary); color: white; }
        .sidebar-nav a i { width: 18px; text-align: center; font-size: 14px; }

        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.06);
            margin: 12px 0;
        }

        .sidebar-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.3);
            padding: 0 14px;
            margin: 12px 0 6px;
        }

        .admin-main {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
        }

        .admin-topbar {
            background: white;
            padding: 16px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .admin-topbar h1 { font-size: 20px; font-weight: 700; }

        .admin-content { padding: 28px; }

        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .admin-stat-card {
            background: white;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
        }

        .admin-stat-card .icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .admin-stat-card .value { font-size: 24px; font-weight: 800; }
        .admin-stat-card .label { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }

        .admin-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .admin-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 16px; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
        .form-input, .form-select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: var(--font);
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus, .form-select:focus { border-color: var(--primary); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-danger { background: #EF4444; color: white; }
        .btn-secondary { background: #F1F5F9; color: var(--text-secondary); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background: var(--primary-50); color: var(--primary-dark); font-weight: 600; padding: 12px 14px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .data-table td { padding: 12px 14px; border-bottom: 1px solid var(--border); font-size: 13px; }
        .data-table tr:hover td { background: #FAFAFA; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-success { background: #DCFCE7; color: #15803D; }
        .badge-warning { background: #FEF3C7; color: #A16207; }
        .badge-danger { background: #FEE2E2; color: #B91C1C; }
        .badge-info { background: #DBEAFE; color: #1D4ED8; }

        .alert { padding: 12px 16px; border-radius: 10px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: #DCFCE7; color: #15803D; }
        .alert-error { background: #FEE2E2; color: #B91C1C; }

        .pagination { display: flex; gap: 4px; margin-top: 16px; justify-content: center; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; font-size: 12px; border: 1px solid var(--border); text-decoration: none; color: var(--text-secondary); }
        .pagination .active span { background: var(--primary); color: white; border-color: var(--primary); }

        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-main { margin-left: 0; }
            .form-row { grid-template-columns: 1fr; }
            .admin-stats { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2>🕌 UQI Admin</h2>
            <span>Smart System Panel</span>
        </div>
        <nav class="sidebar-nav">
            <a href="/admin/dashboard" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="sidebar-divider"></div>
            <div class="sidebar-label">Manajemen</div>
            <a href="/admin/students" class="{{ request()->is('admin/students*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Santri
            </a>
            <a href="/admin/bills" class="{{ request()->is('admin/bills*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> Tagihan
            </a>
            <a href="/admin/payments" class="{{ request()->is('admin/payments*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i> Pembayaran
            </a>
            <a href="/admin/exams" class="{{ request()->is('admin/exams*') ? 'active' : '' }}">
                <i class="fas fa-pencil-alt"></i> Ujian
            </a>
            <a href="/admin/reports" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> Raport
            </a>
            <div class="sidebar-divider"></div>
            <form method="POST" action="/logout">
                @csrf
                <a href="#" onclick="this.closest('form').submit()" style="color:#EF4444;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </form>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <h1>@yield('page-title', 'Admin Panel')</h1>
            <div style="font-size:13px;color:var(--text-secondary);">
                <i class="fas fa-user-shield"></i> {{ auth()->user()->name }}
            </div>
        </div>

        <div class="admin-content">
            @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
