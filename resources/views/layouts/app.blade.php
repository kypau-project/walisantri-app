<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wali Santri App') — UQI Smart System</title>
    <meta name="description" content="Sistem informasi wali santri untuk monitoring tagihan, pembayaran, tabungan, dan raport santri.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #0D9488;
            --primary-dark: #0F766E;
            --primary-darker: #115E59;
            --primary-light: #14B8A6;
            --primary-lighter: #5EEAD4;
            --primary-50: #F0FDFA;
            --primary-100: #CCFBF1;
            --accent: #F59E0B;
            --accent-light: #FCD34D;
            --danger: #EF4444;
            --success: #22C55E;
            --warning: #F97316;
            --info: #3B82F6;
            --bg: #F0FDFA;
            --bg-card: #FFFFFF;
            --text: #0F172A;
            --text-secondary: #64748B;
            --text-muted: #94A3B8;
            --border: #E2E8F0;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.05);
            --radius: 16px;
            --radius-sm: 10px;
            --radius-full: 9999px;
            --font: 'Plus Jakarta Sans', -apple-system, sans-serif;
            --nav-height: 70px;
            --sidebar-width: 260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== TOP HEADER ===== */
        .app-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            color: white;
            padding: 20px 20px 30px;
            position: relative;
            overflow: hidden;
        }

        .app-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .app-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-brand .logo {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            backdrop-filter: blur(10px);
        }

        .header-brand h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .header-brand span {
            font-size: 11px;
            opacity: 0.8;
            font-weight: 400;
            display: block;
            margin-top: 1px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
            text-decoration: none;
        }

        .header-btn:hover { background: rgba(255,255,255,0.25); transform: scale(1.05); }

        .header-user {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
            position: relative;
            z-index: 2;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), #F97316);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            color: white;
            border: 3px solid rgba(255,255,255,0.3);
        }

        .user-info h3 {
            font-size: 16px;
            font-weight: 700;
        }

        .user-info p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 2px;
        }

        /* ===== MAIN CONTENT ===== */
        .app-content {
            padding: 20px;
            padding-bottom: calc(var(--nav-height) + 30px);
            margin-top: -15px;
            position: relative;
            z-index: 5;
        }

        /* ===== CARDS ===== */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 16px;
            border: 1px solid rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
        }

        .card-subtitle {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .card-badge {
            padding: 5px 12px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 600;
        }

        /* ===== MENU GRID ===== */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 16px 8px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text);
            transition: all 0.25s ease;
            background: transparent;
        }

        .menu-item:hover { background: var(--primary-50); transform: translateY(-2px); }

        .menu-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            transition: transform 0.25s;
        }

        .menu-item:hover .menu-icon { transform: scale(1.1); }

        .menu-icon.teal { background: linear-gradient(135deg, #0D9488, #14B8A6); }
        .menu-icon.blue { background: linear-gradient(135deg, #2563EB, #3B82F6); }
        .menu-icon.amber { background: linear-gradient(135deg, #D97706, #F59E0B); }
        .menu-icon.rose { background: linear-gradient(135deg, #E11D48, #F43F5E); }
        .menu-icon.violet { background: linear-gradient(135deg, #7C3AED, #8B5CF6); }
        .menu-icon.emerald { background: linear-gradient(135deg, #059669, #10B981); }
        .menu-icon.orange { background: linear-gradient(135deg, #EA580C, #F97316); }
        .menu-icon.cyan { background: linear-gradient(135deg, #0891B2, #06B6D4); }

        .menu-label {
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            line-height: 1.3;
            color: var(--text-secondary);
        }

        /* ===== STATS ===== */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0,0,0,0.03);
        }

        .stat-card .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .stat-card .stat-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -0.5px;
        }

        .stat-card .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
            font-weight: 500;
        }

        /* ===== STATUS BADGES ===== */
        .badge { padding: 4px 10px; border-radius: var(--radius-full); font-size: 11px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
        .badge-success { background: #DCFCE7; color: #15803D; }
        .badge-warning { background: #FEF3C7; color: #A16207; }
        .badge-danger { background: #FEE2E2; color: #B91C1C; }
        .badge-info { background: #DBEAFE; color: #1D4ED8; }
        .badge-secondary { background: #F1F5F9; color: #475569; }

        /* ===== LIST ITEMS ===== */
        .list-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            color: inherit;
            transition: background 0.15s;
        }

        .list-item:last-child { border-bottom: none; }

        .list-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .list-content { flex: 1; min-width: 0; }
        .list-content h4 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .list-content p { font-size: 12px; color: var(--text-secondary); }
        .list-amount { text-align: right; flex-shrink: 0; }
        .list-amount .amount { font-size: 14px; font-weight: 700; color: var(--text); }
        .list-amount .amount.debit { color: var(--danger); }
        .list-amount .amount.credit { color: var(--success); }
        .list-amount .date { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

        /* ===== FORMS ===== */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 6px; }
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: var(--font);
            color: var(--text);
            transition: border-color 0.2s, box-shadow 0.2s;
            background: white;
            outline: none;
        }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }
        .form-input::placeholder { color: var(--text-muted); }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: var(--font);
            color: var(--text);
            background: white;
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
        }
        .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }

        /* ===== BUTTONS ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font);
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            line-height: 1;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            box-shadow: 0 4px 12px rgba(13,148,136,0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(13,148,136,0.4); }

        .btn-secondary { background: var(--primary-50); color: var(--primary-dark); }
        .btn-secondary:hover { background: var(--primary-100); }

        .btn-danger { background: linear-gradient(135deg, #DC2626, #EF4444); color: white; }
        .btn-success { background: linear-gradient(135deg, #059669, #10B981); color: white; }
        .btn-warning { background: linear-gradient(135deg, #D97706, #F59E0B); color: white; }

        .btn-block { display: flex; width: 100%; }
        .btn-sm { padding: 8px 14px; font-size: 12px; border-radius: 8px; }
        .btn-lg { padding: 14px 28px; font-size: 16px; }

        /* ===== DESKTOP SIDEBAR NAV ===== */
        .desktop-sidebar {
            display: none;
        }

        /* ===== BOTTOM NAV (Mobile) ===== */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--nav-height);
            background: white;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 100;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .nav-item.active { color: var(--primary); }
        .nav-item.active .nav-icon-wrap {
            background: var(--primary-50);
            color: var(--primary);
        }

        .nav-icon-wrap {
            width: 40px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-full);
            font-size: 18px;
            transition: all 0.2s;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }
        .sidebar-nav-item:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar-nav-item.active { background: var(--primary); color: white; }
        .sidebar-nav-item i { width: 20px; text-align: center; font-size: 15px; }
        .sidebar-nav-item span { white-space: nowrap; }

        .sidebar-divider {
            height: 1px;
            background: rgba(255,255,255,0.06);
            margin: 14px 0;
        }

        .sidebar-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255,255,255,0.3);
            padding: 0 16px;
            margin: 14px 0 8px;
            font-weight: 600;
        }

        /* ===== ALERT ===== */
        .alert {
            padding: 14px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            animation: slideDown 0.3s ease;
        }

        .alert-success { background: #DCFCE7; color: #15803D; border-left: 4px solid #22C55E; }
        .alert-error { background: #FEE2E2; color: #B91C1C; border-left: 4px solid #EF4444; }
        .alert-warning { background: #FEF3C7; color: #A16207; border-left: 4px solid #F59E0B; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--primary-light) 100%);
            color: white;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -60%;
            right: -15%;
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .page-header-inner {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 2;
        }

        .back-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background 0.2s;
        }

        .back-btn:hover { background: rgba(255,255,255,0.25); }

        .page-header h2 { font-size: 20px; font-weight: 700; }
        .page-header p { font-size: 12px; opacity: 0.8; margin-top: 2px; }

        /* ===== BALANCE CARD ===== */
        .balance-card {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary), var(--primary-light));
            border-radius: var(--radius);
            padding: 24px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -15%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }

        .balance-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }

        .balance-label { font-size: 13px; opacity: 0.85; font-weight: 500; }
        .balance-value { font-size: 32px; font-weight: 800; margin: 6px 0; letter-spacing: -1px; position: relative; z-index: 2; }
        .balance-sub { font-size: 12px; opacity: 0.7; }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.3; }
        .empty-state h3 { font-size: 16px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px; }
        .empty-state p { font-size: 13px; }

        /* ===== MODAL ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
            display: none;
            align-items: flex-end;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active { display: flex; }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 560px;
            border-radius: 20px 20px 0 0;
            padding: 24px;
            animation: slideUp 0.3s ease;
        }

        .modal-handle {
            width: 40px;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            margin: 0 auto 16px;
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .section-title h3 { font-size: 16px; font-weight: 700; }
        .section-title a { font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600; }

        /* ===== ANIMATIONS ===== */
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in { animation: fadeIn 0.4s ease; }

        /* ===== TABLE ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .data-table th {
            background: var(--primary-50);
            color: var(--primary-dark);
            font-weight: 600;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 2px solid var(--primary-100);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .data-table tr:hover td { background: var(--primary-50); }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 4px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            color: var(--text-secondary);
            background: white;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .pagination a:hover { background: var(--primary-50); color: var(--primary); border-color: var(--primary); }
        .pagination .active span { background: var(--primary); color: white; border-color: var(--primary); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 400px) {
            .menu-grid { grid-template-columns: repeat(3, 1fr); }
            .stats-row { grid-template-columns: 1fr; }
        }

        /* Tablet */
        @media (min-width: 768px) {
            .app-content { max-width: 900px; margin-left: auto; margin-right: auto; padding: 28px; }
            .stats-row { grid-template-columns: repeat(4, 1fr); }
            .menu-grid { gap: 16px; }
            .menu-icon { width: 58px; height: 58px; font-size: 24px; }
            .menu-label { font-size: 12px; }
            .card { padding: 24px; }
            .list-content h4 { font-size: 15px; }
            .list-amount .amount { font-size: 15px; }
            .balance-value { font-size: 36px; }
            .modal-overlay { align-items: center; }
            .modal-content { border-radius: 20px; max-width: 480px; }
        }

        /* Desktop */
        @media (min-width: 1024px) {
            body {
                background: var(--bg);
            }

            .desktop-sidebar {
                display: flex;
                flex-direction: column;
                position: fixed;
                top: 0;
                left: 0;
                width: var(--sidebar-width);
                height: 100vh;
                background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
                color: white;
                z-index: 150;
                overflow-y: auto;
            }

            .desktop-sidebar .sidebar-header {
                padding: 24px 20px;
                border-bottom: 1px solid rgba(255,255,255,0.08);
            }

            .desktop-sidebar .sidebar-header h2 {
                font-size: 17px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .desktop-sidebar .sidebar-header span {
                font-size: 11px;
                opacity: 0.5;
                display: block;
                margin-top: 3px;
            }

            .desktop-sidebar .sidebar-links {
                padding: 16px 14px;
                flex: 1;
            }

            .desktop-sidebar .sidebar-footer {
                padding: 16px 14px;
                border-top: 1px solid rgba(255,255,255,0.06);
            }

            .desktop-sidebar .user-block {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 14px;
                background: rgba(255,255,255,0.05);
                border-radius: 12px;
                margin-bottom: 4px;
            }

            .desktop-sidebar .user-block .avatar {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                background: linear-gradient(135deg, var(--accent), #F97316);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 14px;
                font-weight: 700;
                flex-shrink: 0;
            }

            .desktop-sidebar .user-block .info {
                min-width: 0;
            }

            .desktop-sidebar .user-block .info h4 {
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .desktop-sidebar .user-block .info p {
                font-size: 10px;
                opacity: 0.5;
            }

            .app-header {
                margin-left: var(--sidebar-width);
                border-radius: 0;
            }

            .page-header {
                margin-left: var(--sidebar-width);
            }

            .app-content {
                margin-left: var(--sidebar-width);
                max-width: none;
                padding: 32px 40px;
                padding-bottom: 40px;
            }

            .bottom-nav {
                display: none;
            }

            .stats-row {
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-card .stat-value {
                font-size: 22px;
            }

            .card {
                padding: 28px;
            }

            .card-title {
                font-size: 17px;
            }

            .menu-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }

            .menu-icon {
                width: 60px;
                height: 60px;
                font-size: 26px;
                border-radius: 16px;
            }

            .menu-label {
                font-size: 13px;
            }

            .menu-item {
                padding: 20px 12px;
            }

            .list-content h4 { font-size: 15px; }
            .list-amount .amount { font-size: 16px; }

            .balance-card {
                padding: 32px;
            }

            .balance-value {
                font-size: 40px;
            }

            /* Desktop two-column layout for some pages */
            .desktop-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                align-items: start;
            }

            .desktop-grid-3 {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 20px;
                align-items: start;
            }

            .desktop-span-full {
                grid-column: 1 / -1;
            }
        }

        /* Large Desktop */
        @media (min-width: 1440px) {
            .app-content {
                padding: 40px 60px;
            }

            .stats-row {
                gap: 20px;
            }

            .stat-card .stat-value {
                font-size: 24px;
            }

            .menu-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 24px;
            }

            .menu-icon {
                width: 68px;
                height: 68px;
                font-size: 28px;
                border-radius: 18px;
            }

            .menu-label {
                font-size: 14px;
            }
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--primary-lighter); border-radius: 4px; }

        /* ===== QR CODE ===== */
        .qr-container {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: var(--radius);
            border: 2px dashed var(--border);
        }

        .qr-container img, .qr-container svg { max-width: 180px; margin: 0 auto; }

        /* ===== PROFILE INFO ===== */
        .profile-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .profile-item:last-child { border-bottom: none; }
        .profile-item .label { font-size: 13px; color: var(--text-secondary); font-weight: 500; }
        .profile-item .value { font-size: 13px; font-weight: 600; color: var(--text); text-align: right; }
    </style>
    @yield('styles')
</head>
<body>
    @hasSection('hide-nav')
    @else
    <!-- Desktop Sidebar Navigation -->
    <aside class="desktop-sidebar" id="desktop-sidebar">
        <div class="sidebar-header">
            <h2>🕌 Wali Santri</h2>
            <span>UQI Smart System</span>
        </div>

        @auth
        <div style="padding: 16px 14px 0;">
            <div class="user-block">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->student->name ?? auth()->user()->name, 0, 1)) }}</div>
                <div class="info">
                    <h4>{{ auth()->user()->student->name ?? auth()->user()->name }}</h4>
                    <p>{{ auth()->user()->student->nis ?? auth()->user()->role }}</p>
                </div>
            </div>
        </div>
        @endauth

        <nav class="sidebar-links">
            <div class="sidebar-label">Menu Utama</div>
            <a href="/dashboard" class="sidebar-nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> <span>Beranda</span>
            </a>
            <a href="/profile" class="sidebar-nav-item {{ request()->is('profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i> <span>Profil Santri</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-label">Keuangan</div>
            <a href="/bills" class="sidebar-nav-item {{ request()->is('bills') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i> <span>Tagihan</span>
            </a>
            <a href="/payments" class="sidebar-nav-item {{ request()->is('payments') ? 'active' : '' }}">
                <i class="fas fa-history"></i> <span>Riwayat Pembayaran</span>
            </a>
            <a href="/savings" class="sidebar-nav-item {{ request()->is('savings') ? 'active' : '' }}">
                <i class="fas fa-piggy-bank"></i> <span>Tabungan</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-label">Akademik</div>
            <a href="/exams" class="sidebar-nav-item {{ request()->is('exams') ? 'active' : '' }}">
                <i class="fas fa-pencil-alt"></i> <span>Ujian Online</span>
            </a>
            <a href="/reports" class="sidebar-nav-item {{ request()->is('reports') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> <span>Raport</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="sidebar-nav-item" style="width:100%;border:none;background:none;cursor:pointer;color:#EF4444;font-family:var(--font);">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>
    @endif

    @yield('content')

    @hasSection('hide-nav')
    @else
    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}" id="nav-home">
            <div class="nav-icon-wrap"><i class="fas fa-home"></i></div>
            <span>Beranda</span>
        </a>
        <a href="/bills" class="nav-item {{ request()->is('bills') ? 'active' : '' }}" id="nav-bills">
            <div class="nav-icon-wrap"><i class="fas fa-file-invoice-dollar"></i></div>
            <span>Tagihan</span>
        </a>
        <a href="/savings" class="nav-item {{ request()->is('savings') ? 'active' : '' }}" id="nav-savings">
            <div class="nav-icon-wrap"><i class="fas fa-piggy-bank"></i></div>
            <span>Tabungan</span>
        </a>
        <a href="/profile" class="nav-item {{ request()->is('profile') ? 'active' : '' }}" id="nav-profile">
            <div class="nav-icon-wrap"><i class="fas fa-user"></i></div>
            <span>Profil</span>
        </a>
    </nav>
    @endif

    @yield('scripts')
</body>
</html>
