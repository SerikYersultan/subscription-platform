<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SubTrack</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-main: #f5f4f0;
            --card-white: #ffffff;
            --card-dark: #1b1b1b;
            --text-primary: #111111;
            --text-muted: #7e7d7a;
            --brand: #1b1b1b;
            --brand-dark: #333333;
            --brand-soft: #f0eee8;
            --green: #16a34a;
            --green-dark: #166534;
            --green-soft: #dcfce7;
            --sidebar-w: 240px;
        }

        body {
            background: var(--bg-main) !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: var(--text-primary);
            height: 100vh;
            padding: 16px;
        }

        .app {
            display: flex;
            height: calc(100vh - 32px);
            overflow: hidden;
            gap: 16px;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--card-white);
            border-radius: 24px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
            box-shadow: 0 4px 24px rgba(0,0,0,0.015);
            padding: 12px;
            border-right: 1px solid #e2e1dd;
        }

        .sidebar-logo {
            padding: 12px;
            margin-bottom: 8px;
        }

        .logo-mark { display: flex; align-items: center; justify-content: flex-start; width: 100%; }
        .logo-text { font-size: 15px; font-weight: 700; color: #111827; }
        .logo-sub { font-size: 11px; color: var(--text-muted); }

        .nav { flex: 1; }

        .nav-section {
            font-size: 10px;
            font-weight: 700;
            color: #b5b4b0;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 14px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 16px;
            font-size: 13px;
            color: #4a4947;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 2px;
            font-weight: 500;
        }

        .nav-item:hover {
            background: #faf9f5;
            color: var(--text-primary);
        }

        .nav-item.active {
            background: #f0eee8;
            color: var(--text-primary);
            font-weight: 600;
        }

        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; opacity: 0.8; }

        .nav-badge {
            margin-left: auto;
            background: var(--card-dark);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .nav-badge.blue { background: #e2e1dd; color: #111; }

        .sidebar-footer {
            padding: 12px 4px 4px;
            border-top: 1px solid #f0eee8;
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: 16px;
            transition: background 0.2s;
        }

        .user-row:hover { background: #faf9f5; }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e0ded9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-primary);
            flex-shrink: 0;
        }

        .user-name { font-size: 12px; font-weight: 700; color: var(--text-primary); }
        .user-plan { font-size: 11px; color: var(--text-muted); }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 12px;
            font-size: 11px;
            color: var(--text-muted);
            cursor: pointer;
            width: 100%;
            background: none;
            border: none;
            margin-top: 4px;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: #faf9f5;
            color: #dc2626;
        }

        .main {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .topbar {
            background: var(--card-white);
            border-radius: 24px;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            box-shadow: 0 4px 24px rgba(0,0,0,0.01);
        }

        .page-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            flex: 1;
        }

        .topbar-actions { display: flex; gap: 8px; align-items: center; }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #e2e1dd;
            background: var(--card-white);
            color: var(--text-primary);
            transition: all 0.15s ease;
        }

        .btn:hover {
            background: #faf9f5;
            border-color: #d2d1cd;
        }

        .btn.primary {
            background: var(--card-dark);
            color: white;
            border: none;
        }

        .btn.primary:hover {
            background: #333333;
            transform: translateY(-1px);
        }

        .btn svg { width: 13px; height: 13px; }

        /* ── Content wrapper ── */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 0 0 16px;
        }

        /* ── Flash messages ── */
        .flash-success {
            background: #e6f7ed;
            border-radius: 16px;
            padding: 12px 20px;
            font-size: 13px;
            color: #166534;
        }

        .flash-error {
            background: #fef2f2;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 12px;
            color: #991b1b;
        }

        /* ── Alert strip ── */
        .alert-strip {
            background: var(--card-white);
            border-radius: 20px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.01);
            cursor: pointer;
        }

        .alert-strip:hover { background: #faf9f5; }
        .alert-dot { width: 8px; height: 8px; border-radius: 50%; background: #dc2626; flex-shrink: 0; }
        .alert-text { font-size: 13px; color: var(--text-primary); flex: 1; }
        .alert-count { font-size: 11px; font-weight: 600; color: var(--text-primary); background: #f0eee8; padding: 4px 10px; border-radius: 12px; white-space: nowrap; }


        .metrics-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .metric-card {
            background: var(--card-white);
            border-radius: 24px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.015);
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.03);
        }

        .metric-label { font-size: 12px; color: var(--text-muted); margin-bottom: 8px; font-weight: 500; }
        .metric-value { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; letter-spacing: -0.02em; }
        .metric-sub { font-size: 11px; color: var(--text-muted); }

        .metric-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-blue { background: #f0f4ff; }
        .icon-green { background: #e6f7ed; }
        .icon-amber { background: #fff9eb; }
        .icon-red { background: #fff5f5; }

        /* ── Grid ── */
        .grid-2 {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr);
            gap: 16px;
        }

        /* ── Panels ── */
        .panel {
            background: var(--card-white);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.015);
            padding: 24px;
        }

        .panel-head {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .panel-title { font-size: 15px; font-weight: 700; color: var(--text-primary); flex: 1; }
        .panel-action { font-size: 12px; color: var(--text-muted); cursor: pointer; font-weight: 600; }
        .panel-action:hover { color: var(--text-primary); text-decoration: underline; }


        .sub-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #f5f4f0;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sub-row:last-child { border-bottom: none; }
        .sub-row:hover { background: #faf9f5; border-radius: 12px; padding-left: 8px; padding-right: 8px; }

        .sub-logo {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sub-info { flex: 1; min-width: 0; }
        .sub-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .sub-meta { font-size: 11px; color: var(--text-muted); margin-top: 1px; }
        .sub-right { text-align: right; flex-shrink: 0; }
        .sub-amount { font-size: 14px; font-weight: 700; color: var(--text-primary); }
        .sub-next { font-size: 11px; color: var(--text-muted); }

        .conf-pill { display: inline-block; font-size: 10px; font-weight: 700; padding: 1px 6px; border-radius: 10px; margin-left: 4px; }
        .conf-high { background: #dcfce7; color: #166534; }
        .conf-med { background: #fef3c7; color: #92400e; }

        .tag { display: inline-flex; align-items: center; font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 10px; }
        .tag-blue { background: #e8eeff; color: #1d4ed8; }
        .tag-amber { background: #fef3c7; color: #92400e; }
        .tag-red { background: #fef2f2; color: #991b1b; }
        .tag-green { background: #dcfce7; color: #166534; }
        .tag-gray { background: #f0eee8; color: #4a4947; }

        .upcoming-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f4f0;
            transition: background 0.1s;
        }

        .upcoming-row:last-child { border-bottom: none; }
        .upcoming-date { font-size: 11px; font-weight: 700; color: var(--text-muted); width: 42px; flex-shrink: 0; }
        .upcoming-name { font-size: 12px; color: var(--text-primary); flex: 1; }
        .upcoming-amt { font-size: 12px; font-weight: 700; color: var(--text-primary); }
        .due-soon { color: #dc2626; }


        .alert-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px solid #f5f4f0;
            cursor: pointer;
            transition: background 0.1s;
        }

        .alert-row:last-child { border-bottom: none; }
        .alert-icon { width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0; margin-top: 1px; display: flex; align-items: center; justify-content: center; }
        .alert-body { flex: 1; }
        .alert-title { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .alert-desc { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
        .alert-time { font-size: 10px; color: #b5b4b0; flex-shrink: 0; margin-top: 2px; }

        .empty-state { padding: 40px 0; text-align: center; color: var(--text-muted); font-size: 13px; }


        .filter-bar {
            background: var(--card-white);
            border-radius: 20px;
            padding: 16px 20px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-end;
            box-shadow: 0 4px 18px rgba(0,0,0,0.01);
        }

        .filter-bar label { font-size: 10px; font-weight: 600; color: var(--text-muted); display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.04em; }
        .filter-input { padding: 8px 12px; border: 1px solid #e2e1dd; border-radius: 12px; font-size: 12px; color: var(--text-primary); outline: none; background: #faf9f5; transition: border-color 0.15s; }
        .filter-input:focus { border-color: #b5b4b0; background: #fff; }

        /* ── Profile cards ── */
        .profile-card {
            background: var(--card-white);
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.015);
        }

        .profile-card h3 { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f5f4f0; }
        .profile-field { margin-bottom: 14px; }
        .profile-label { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-bottom: 5px; display: block; text-transform: uppercase; letter-spacing: 0.04em; }
        .profile-input { width: 100%; padding: 9px 13px; border: 1px solid #e2e1dd; border-radius: 12px; font-size: 13px; color: var(--text-primary); outline: none; background: #faf9f5; transition: border-color 0.15s; }
        .profile-input:focus { border-color: #b5b4b0; background: #fff; }
        .profile-error { font-size: 11px; color: #dc2626; margin-top: 3px; }
        .profile-success { background: #e6f7ed; border-radius: 12px; padding: 12px 16px; font-size: 12px; color: #166534; margin-bottom: 14px; }
        .profile-danger { background: #fef2f2; border-radius: 20px; padding: 24px; }
        .profile-danger h3 { font-size: 13px; font-weight: 700; color: #991b1b; margin-bottom: 8px; border-bottom: none; padding-bottom: 0; }
        .profile-danger p { font-size: 12px; color: var(--text-muted); margin-bottom: 14px; }

        /* ── Merchant table ── */
        .merchant-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .merchant-table th { text-align: left; padding: 10px 0; font-size: 11px; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid #f0eee8; text-transform: uppercase; letter-spacing: 0.04em; }
        .merchant-table td { padding: 12px 0; border-bottom: 1px solid #f5f4f0; color: var(--text-primary); }
        .merchant-table tr:last-child td { border-bottom: none; }
        .merchant-table tr:hover td { background: #faf9f5; }

        /* ── Modal ── */
        #edit-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .uploading {
            opacity: 0.9;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }

        .upload-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.4);
            border-top: 2px solid white;
            border-radius: 50%;
            display: inline-block;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── My Cards widget ── */
        .cards-widget {
            background: var(--card-white);
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.015);
            margin-top: 16px;
        }

        .cards-widget-head {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .cards-widget-title { font-size: 15px; font-weight: 700; color: var(--text-primary); flex: 1; }

        .add-card-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            background: var(--card-dark);
            color: white;
            border: none;
            transition: background 0.15s;
        }

        .add-card-btn:hover { background: #333; }

        .cards-scroll {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: none;
        }

        .cards-scroll::-webkit-scrollbar { display: none; }

        .bank-card {
            min-width: 200px;
            height: 118px;
            border-radius: 18px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex-shrink: 0;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .bank-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(0,0,0,0.14); }

        .bank-card::before {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 110px; height: 110px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }

        .bank-card::after {
            content: '';
            position: absolute;
            bottom: -40px; left: 20px;
            width: 130px; height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .bank-card.dark { background: linear-gradient(135deg, #1b1b1b 0%, #2d2d2d 100%); }
        .bank-card.blue { background: linear-gradient(135deg, #1a3a5c 0%, #2563eb 100%); }
        .bank-card.green { background: linear-gradient(135deg, #064e3b 0%, #059669 100%); }

        .card-number {
            font-size: 11px;
            color: rgba(255,255,255,0.55);
            letter-spacing: 0.08em;
            font-weight: 500;
        }

        .card-bottom { display: flex; align-items: flex-end; justify-content: space-between; }

        .card-balance-label { font-size: 9px; color: rgba(255,255,255,0.5); margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.06em; }
        .card-balance { font-size: 17px; font-weight: 700; color: #fff; letter-spacing: -0.01em; }
        .card-name { font-size: 10px; color: rgba(255,255,255,0.6); font-weight: 500; }

        .card-network {
            font-size: 10px;
            font-weight: 800;
            color: rgba(255,255,255,0.7);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            align-self: flex-end;
        }

        .add-card-placeholder {
            min-width: 200px;
            height: 118px;
            border-radius: 18px;
            border: 2px dashed #e2e1dd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            background: #faf9f5;
        }

        .add-card-placeholder:hover { border-color: #b5b4b0; color: var(--text-primary); background: #f5f4f0; }
    </style>
</head>
<body>

<div class="app">
    <div class="sidebar">
        <div class="sidebar-logo" onclick="setPage('dashboard')" style="cursor:pointer;">
            <div class="logo-mark">
                <div style="display: flex; flex-direction: column; line-height: 1.15;">
                    <div style="font-weight: 700; font-size: 15px; color: var(--text-primary);">Recurly</div>
                </div>
            </div>
        </div>

        <div class="nav">
            <div class="nav-section">Overview</div>
            <div class="nav-item active" onclick="setPage('dashboard')">
                <svg viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="1" width="6" height="6" rx="1.5"/><rect x="9" y="1" width="6" height="6" rx="1.5"/><rect x="1" y="9" width="6" height="6" rx="1.5"/><rect x="9" y="9" width="6" height="6" rx="1.5"/></svg>
                Dashboard
            </div>
            <div class="nav-item" onclick="setPage('subscriptions')">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 4h12v2H2zm0 3h12v2H2zm0 3h8v2H2z"/></svg>
                Subscriptions
                @if ($subscriptions->count() > 0)
                    <span class="nav-badge blue">{{ $subscriptions->count() }}</span>
                @endif
            </div>
            <div class="nav-section">Detection</div>
            <div class="nav-item" onclick="setPage('detected')">
                <svg viewBox="0 0 16 16" fill="currentColor"><circle cx="7" cy="7" r="5" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="m11 11 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                Detected
                @if ($detected->count() > 0)
                    <span class="nav-badge">{{ $detected->count() }}</span>
                @endif
            </div>
            <div class="nav-item" onclick="setPage('transactions')">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 5h12M2 8h8M2 11h6" stroke="currentColor" stroke-width="1.2" fill="none"/></svg>
                Transactions
            </div>
            <div class="nav-section">Intelligence</div>
            <div class="nav-item" onclick="setPage('alerts')">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a5 5 0 0 1 5 5c0 2.5 1 3.5 1 5H2c0-1.5 1-2.5 1-5a5 5 0 0 1 5-5zm-1 13h2a1 1 0 0 1-2 0z"/></svg>
                Alerts
                @if ($unreadAlertsCount > 0)
                    <span class="nav-badge">{{ $unreadAlertsCount }}</span>
                @endif
            </div>
            <div class="nav-item" onclick="setPage('reports')">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M3 2h10a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm2 3v6M8 7v4M11 5v6" stroke="currentColor" stroke-width="1.2" fill="none"/></svg>
                Reports
            </div>
            <div class="nav-item" onclick="setPage('merchants')">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 3h12l-1.5 5H3.5L2 3zm1.5 5v5h9V8" stroke="currentColor" stroke-width="1.2" fill="none" stroke-linecap="round"/></svg>
                Merchants
                @if ($merchants->count() > 0)
                    <span class="nav-badge blue">{{ $merchants->count() }}</span>
                @endif
            </div>
            <div class="nav-item" onclick="setPage('import')">
                <svg viewBox="0 0 16 16" fill="currentColor"><path d="M8 10V3M5 7l3 3 3-3M3 13h10" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
                Import
            </div>
            <div class="nav-section">Account</div>
            <div class="nav-item" onclick="setPage('profile')">
                <svg viewBox="0 0 16 16" fill="currentColor"><circle cx="8" cy="5" r="3" fill="none" stroke="currentColor" stroke-width="1.3"/><path d="M2 14c0-3.3 2.7-5 6-5s6 1.7 6 5" stroke="currentColor" stroke-width="1.3" fill="none" stroke-linecap="round"/></svg>
                Profile
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="user-row" onclick="setPage('profile')" style="cursor:pointer">
                <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-plan">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 3H3a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h3M10 11l3-3-3-3M13 8H6"/></svg>
                    Log out
                </button>
            </form>
        </div>
    </div>

    <div class="main">

        <!-- ══ DASHBOARD ══ -->
        <div id="page-dashboard">
            <div class="topbar">
                <div class="page-title">Dashboard</div>
                <div class="topbar-actions">
                    <button class="btn" onclick="setPage('import')">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 10V3M5 7l3 3 3-3M3 13h10"/></svg>
                        Import
                    </button>
                    @if ($subscriptions->isNotEmpty() || $detected->isNotEmpty())
                        <button class="btn" onclick="setPage('detected')" style="position:relative">
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 3v2M8 11v2M3 8H1M15 8h-2M5.05 5.05 3.64 3.64M12.36 12.36l-1.41-1.41M10.95 5.05l1.41-1.41M3.64 12.36l1.41-1.41" stroke-linecap="round"/></svg>
                            Review Detections
                            @if ($detected->count() > 0)
                                <span style="position:absolute;top:-6px;right:-6px;background:#1b1b1b;color:#fff;border-radius:9999px;font-size:10px;font-weight:700;padding:1px 5px;line-height:16px">{{ $detected->count() }}</span>
                            @endif
                        </button>
                    @endif
                    <form method="POST" action="{{ route('detect') }}" style="display:inline" id="detect-form">
                        @csrf
                        <button type="submit" class="btn primary" id="detect-btn">
                            <svg viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.5"><path d="M13 8A5 5 0 1 1 8 3" stroke-linecap="round"/><path d="M13 3v3h-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Re-scan
                        </button>
                    </form>
                </div>
            </div>

            <div class="content">
                @if (session('success'))
                    <div class="flash-success" id="flash-success">✅ {{ session('success') }}</div>
                @endif

                @if ($transactionCount > 0 && $subscriptions->isEmpty())
                    <div class="alert-strip" style="cursor:default;">
                        <div class="alert-dot" style="background:#1b1b1b"></div>
                        <div class="alert-text"><strong>{{ number_format($transactionCount) }} transactions imported.</strong> Click <strong>Re-scan</strong> above to map subscriptions.</div>
                    </div>
                @endif

                @if ($unreadAlertsCount > 0)
                    <div class="alert-strip" onclick="setPage('alerts')">
                        <div class="alert-dot"></div>
                        <div class="alert-text"><strong>{{ $unreadAlertsCount }} alert{{ $unreadAlertsCount > 1 ? 's' : '' }} need your attention</strong></div>
                        <div class="alert-count">View all</div>
                    </div>
                @endif

                <!-- ── My Cards (PDF-powered) ── -->
                    <div class="cards-widget">
                        <div class="cards-widget-head">
                            <div class="cards-widget-title">My cards</div>
                        </div>

                        <div class="cards-scroll" id="cards-scroll-container">
                            @php $cards = session('cards', []); @endphp

                            @forelse ($cards as $card)
                                <div class="bank-card dark">
                                    <div>
                                        <div class="card-number">•••• •••• •••• {{ $card['last4'] }}</div>
                                        <div class="card-name">{{ $card['name'] }}</div>

                                        <div style="font-size:10px;color:rgba(255,255,255,0.65);margin-top:4px;">
                                            {{ $card['owner'] ?? 'Unknown Owner' }}
                                        </div>
                                    </div>

                                    <div class="card-bottom">
                                        <div>
                                            <div class="card-balance-label">Balance</div>
                                            <div class="card-balance">{{ $card['balance'] }} {{ $card['currency'] }}</div>
                                        </div>

                                        <div class="card-network">KASPI</div>
                                    </div>
                                </div>
                            @empty
                                <div id="cards-empty-state" class="add-card-placeholder">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                        <rect x="2" y="5" width="16" height="12" rx="2"/>
                                        <path d="M2 9h16M6 13h2"/>
                                    </svg>
                                    <span>No card imported yet</span>
                                </div>
                            @endforelse
                        </div>

                        <div id="card-parse-status" style="display:none;font-size:11px;color:var(--text-muted);margin-top:10px;padding:0 4px;"></div>
                    </div>

                <div class="metrics-row">
                    <div class="metric-card" onclick="setPage('subscriptions')">
                        <div class="metric-icon icon-blue"><svg width="14" height="14" viewBox="0 0 16 16"><path d="M2 12l4-4 3 3 5-6" stroke="#1a56db" fill="none" stroke-width="1.8" stroke-linecap="round"/></svg></div>
                        <div class="metric-label">Monthly burn rate</div>
                        <div class="metric-value">{{ number_format($monthlyBurn, 2) }} ₸</div>
                        <div class="metric-sub">Across all tracked items</div>
                    </div>
                    <div class="metric-card" onclick="setPage('subscriptions')">
                        <div class="metric-icon icon-green"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="8" cy="8" r="5"/><path d="m5.5 8 2 2 3-3" stroke-linecap="round"/></svg></div>
                        <div class="metric-label">Active subscriptions</div>
                        <div class="metric-value">{{ $subscriptions->count() }}</div>
                        <div class="metric-sub">Currently monitored</div>
                    </div>
                    <div class="metric-card" onclick="setPage('transactions')">
                        <div class="metric-icon icon-amber"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#d97706" stroke-width="1.2"><path d="M2 5h12M2 8h8M2 11h6"/></svg></div>
                        <div class="metric-label">Transactions</div>
                        <div class="metric-value">{{ number_format($transactionCount) }}</div>
                        <div class="metric-sub">Total records parsed</div>
                    </div>
                    <div class="metric-card" onclick="setPage('detected')">
                        <div class="metric-icon icon-red"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#dc2626" stroke-width="1.6"><circle cx="8" cy="8" r="5"/><path d="m5 5 6 6M11 5 5 11" stroke-linecap="round"/></svg></div>
                        <div class="metric-label">Unconfirmed logs</div>
                        <div class="metric-value">{{ $detected->count() }}</div>
                        <div class="metric-sub">Pending validation</div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="panel">
                        <div class="panel-head">
                            <div class="panel-title">Active subscriptions</div>
                            <div class="panel-action" onclick="setPage('subscriptions')">View all</div>
                        </div>
                        @forelse ($subscriptions->take(6) as $sub)
                            @php
                                $initials = strtoupper(substr($sub->name, 0, 2));
                                $confClass = $sub->confidence_score >= 80 ? 'conf-high' : 'conf-med';
                                $colors = ['#f0f4ff|#1a56db','#e6f7ed|#059669','#fff5f5|#dc2626','#faf5ff|#7c3aed','#fff9eb|#d97706','#fff0f6|#be185d'];
                                $colorPair = explode('|', $colors[$loop->index % count($colors)]);
                            @endphp
                            <div class="sub-row">
                                <div class="sub-logo" style="background:{{ $colorPair[0] }}; color:{{ $colorPair[1] }}">{{ $initials }}</div>
                                <div class="sub-info">
                                    <div class="sub-name">{{ $sub->name }} <span class="conf-pill {{ $confClass }}">{{ number_format($sub->confidence_score) }}%</span></div>
                                    <div class="sub-meta">{{ ucfirst($sub->billing_cycle) }}</div>
                                </div>
                                <div class="sub-right">
                                    <div class="sub-amount">{{ number_format($sub->amount, 2) }} ₸</div>
                                    <div class="sub-next">{{ $sub->next_billing_date ? \Carbon\Carbon::parse($sub->next_billing_date)->format('M j') : '—' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">No subscriptions yet.<br>Import a CSV to detect them.</div>
                        @endforelse
                    </div>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div class="panel">
                            <div class="panel-head"><div class="panel-title">Upcoming charges</div></div>
                            @forelse ($upcomingCharges->take(4) as $sub)
                                @php $daysUntil = \Carbon\Carbon::parse($sub->next_billing_date)->startOfDay()->diffInDays(now()->startOfDay(), false); @endphp
                                <div class="upcoming-row">
                                    <div class="upcoming-date {{ $daysUntil <= 3 ? 'due-soon' : '' }}">{{ \Carbon\Carbon::parse($sub->next_billing_date)->format('M j') }}</div>
                                    <div class="upcoming-name">{{ $sub->name }}</div>
                                    <div class="upcoming-amt {{ $daysUntil <= 3 ? 'due-soon' : '' }}">{{ number_format($sub->amount, 2) }} ₸</div>
                                </div>
                            @empty
                                <div class="empty-state">No charges in the next 7 days.</div>
                            @endforelse
                        </div>
                        <div class="panel">
                            <div class="panel-head">
                                <div class="panel-title">Recent alerts</div>
                                <div class="panel-action" onclick="setPage('alerts')">View all</div>
                            </div>
                            @forelse ($alerts->take(3) as $alert)
                                <div class="alert-row">
                                    <div class="alert-icon" style="background:#fef3c7"><svg width="14" height="14" viewBox="0 0 16 16" fill="#d97706"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div>
                                    <div class="alert-body">
                                        <div class="alert-title">{{ $alert->message }}</div>
                                        @if ($alert->subscription)
                                            <div class="alert-desc">{{ $alert->subscription->name }}</div>
                                        @endif
                                    </div>
                                    <div class="alert-time">{{ $alert->created_at->diffForHumans(null, true) }}</div>
                                </div>
                            @empty
                                <div class="empty-state">No alerts yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ DETECTED ══ -->
        <div id="page-detected" style="display:none;">
            <div class="topbar">
                <div class="page-title">Review detections</div>
                <div class="topbar-actions">
                    <form method="POST" action="{{ route('detect') }}" style="display:inline" id="detect-form-det">
                        @csrf
                        <button type="submit" class="btn primary" id="detect-btn-det">
                            <svg viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.5"><path d="M13 8A5 5 0 1 1 8 3" stroke-linecap="round"/><path d="M13 3v3h-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Re-scan
                        </button>
                    </form>
                </div>
            </div>
            <div class="content">
                <p style="font-size:13px;color:var(--text-muted);">The detection engine found these recurring patterns. Confirm the ones that are real subscriptions, edit if the details are wrong, or remove false positives.</p>
                <div class="panel">
                    @forelse ($detected as $sub)
                        @php
                            $initials = strtoupper(substr($sub->name, 0, 2));
                            $conf = (int) $sub->confidence_score;
                            $confColor = $conf >= 75 ? '#16a34a' : ($conf >= 55 ? '#d97706' : '#dc2626');
                        @endphp
                        <div class="sub-row" style="flex-direction:column;align-items:stretch;gap:12px;padding:16px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div class="sub-logo" style="background:#e6f7ed;color:#16a34a;flex-shrink:0">{{ $initials }}</div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:13px;font-weight:600;color:var(--text-primary)">{{ $sub->name }}</div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
                                        {{ ucfirst($sub->billing_cycle) }} &middot;
                                        {{ number_format(abs($sub->amount), 2) }} {{ $sub->currency }}
                                        {{ $sub->next_billing_date ? ' &middot; Next ' . \Carbon\Carbon::parse($sub->next_billing_date)->format('M j, Y') : '' }}
                                    </div>
                                </div>
                                <div style="flex-shrink:0;text-align:right">
                                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:4px">Confidence</div>
                                    <div style="font-size:15px;font-weight:700;color:{{ $confColor }}">{{ $conf }}%</div>
                                </div>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <form method="POST" action="{{ route('subscriptions.confirm', $sub) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn primary" style="font-size:12px;padding:6px 14px">
                                        <svg viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round"><path d="M3 8l4 4 6-6"/></svg>
                                        Confirm
                                    </button>
                                </form>
                                <button type="button" class="btn" style="font-size:12px;padding:6px 14px"
                                        onclick="openEditModal({{ $sub->id }}, {{ json_encode($sub->name) }}, {{ json_encode(number_format(abs($sub->amount), 2, '.', '')) }}, {{ json_encode($sub->billing_cycle) }}, {{ json_encode($sub->currency) }})">
                                    <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M11 2l3 3-8 8H3v-3L11 2z"/></svg>
                                    Edit &amp; Confirm
                                </button>
                                <form method="POST" action="{{ route('subscriptions.destroy', $sub) }}" style="display:inline"
                                      onsubmit="return confirm('Remove &quot;{{ addslashes($sub->name) }}&quot;? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" style="font-size:12px;padding:6px 14px;color:#dc2626;border-color:#fecaca">
                                        <svg viewBox="0 0 16 16" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"><path d="M3 5h10M8 5V3M6 5v7M10 5v7M4 5l1 8h6l1-8"/></svg>
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            No unconfirmed detections.<br>
                            <span style="font-size:12px;color:#b5b4b0">Run <strong>Re-scan</strong> after importing transactions to detect recurring charges.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ══ ALERTS ══ -->
        <div id="page-alerts" style="display:none;">
            <div class="topbar"><div class="page-title">Alerts</div></div>
            <div class="content">
                <div class="panel">
                    @forelse ($alerts as $alert)
                        <div class="alert-row">
                            <div class="alert-icon" style="background:#fef3c7"><svg width="14" height="14" viewBox="0 0 16 16" fill="#d97706"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div>
                            <div class="alert-body">
                                <div class="alert-title">{{ $alert->message }}</div>
                                @if ($alert->subscription)
                                    <div class="alert-desc">{{ $alert->subscription->name }}</div>
                                @endif
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                                <span class="tag tag-amber">{{ ucfirst($alert->type) }}</span>
                                <span class="alert-time">{{ $alert->created_at->format('M j') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No alerts yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ══ REPORTS ══ -->
        <div id="page-reports" style="display:none;">
            <div class="topbar"><div class="page-title">Monthly reports</div></div>
            <div class="content">
                <div class="metrics-row" style="grid-template-columns:repeat(3,minmax(0,1fr))">
                    <div class="metric-card">
                        <div class="metric-label">Monthly burn rate</div>
                        <div class="metric-value">₸{{ number_format($monthlyBurn, 2) }}</div>
                        <div class="metric-sub">{{ $subscriptions->count() }} active subscriptions</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Annual projection</div>
                        <div class="metric-value">${{ number_format($monthlyBurn * 12, 2) }}</div>
                        <div class="metric-sub">at current rate</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-label">Avg per subscription</div>
                        <div class="metric-value">${{ $subscriptions->count() > 0 ? number_format($monthlyBurn / $subscriptions->count(), 2) : '0.00' }}</div>
                        <div class="metric-sub">monthly</div>
                    </div>
                </div>
                <div class="panel">
                    <div class="panel-head"><div class="panel-title">Recent imports</div></div>
                    @forelse ($recentImports as $import)
                        <div class="upcoming-row">
                            <div style="font-size:12px;color:var(--text-primary);flex:1">{{ \Carbon\Carbon::parse($import->import_date)->format('M j, Y') }}</div>
                            <div style="font-size:12px;font-weight:600;color:#1a56db">{{ number_format($import->total) }} transactions</div>
                        </div>
                    @empty
                        <div class="empty-state">No imports yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ══ IMPORT ══ -->
        <div id="page-import" style="display:none;">
            <div class="topbar">
                <div class="page-title">Import Bank Statement</div>
            </div>
            <div class="content">

                @if (session('import_result') && session('open_page') === 'import')
                    @php $ir = session('import_result'); @endphp
                    <div style="border-radius:16px;background:{{ $ir['imported'] > 0 ? '#e6f7ed' : '#fffbeb' }};padding:16px 20px;display:flex;align-items:center;gap:18px;flex-wrap:wrap">
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
                            @if($ir['imported'] > 0)
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" fill="#16a34a"/><path d="M6 10l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                <span style="font-size:13px;font-weight:600;color:#166534">Import complete</span>
                            @else
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9" fill="#f59e0b"/><path d="M10 6v4M10 13h.01" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>
                                <span style="font-size:13px;font-weight:600;color:#92400e">Import complete</span>
                            @endif
                        </div>
                        <div style="display:flex;gap:20px;flex-wrap:wrap;flex:1">
                            <div style="display:flex;align-items:center;gap:6px">
                                <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;flex-shrink:0"></span>
                                <span style="font-size:12px;color:var(--text-primary)"><strong>{{ number_format($ir['imported']) }}</strong> imported</span>
                            </div>
                            @if($ir['duplicates'] > 0)
                                <div style="display:flex;align-items:center;gap:6px">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;flex-shrink:0"></span>
                                    <span style="font-size:12px;color:var(--text-primary)"><strong>{{ number_format($ir['duplicates']) }}</strong> duplicates skipped</span>
                                </div>
                            @endif
                            @if($ir['skipped'] > 0)
                                <div style="display:flex;align-items:center;gap:6px">
                                    <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;flex-shrink:0"></span>
                                    <span style="font-size:12px;color:var(--text-primary)"><strong>{{ number_format($ir['skipped']) }}</strong> invalid rows skipped</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($errors->has('csv_file'))
                    <div class="flash-error" style="display:flex;align-items:flex-start;gap:8px">
                        <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" style="flex-shrink:0;margin-top:1px"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 10.5a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5zM7.25 5h1.5v4.5h-1.5V5z"/></svg>
                        <span>{{ $errors->first('csv_file') }}</span>
                    </div>
                @endif

                <div style="display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:16px;align-items:start">
                    <div class="panel">
                        <div class="panel-head">
                            <div class="panel-title">Upload file</div>
                            <span style="font-size:11px;color:var(--text-muted)">PDF, CSV or TXT · max 10 MB</span>
                        </div>
                        <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" id="import-form">
                            @csrf
                            <div style="padding:4px 0 16px">
                                <label for="csv_file" id="drop-zone"
                                       style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;
                                           height:164px;border:2px dashed #e2e1dd;border-radius:16px;cursor:pointer;
                                           background:#faf9f5;transition:background 0.12s,border-color 0.12s">
                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#b5b4b0" stroke-width="1.4" stroke-linecap="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="17 8 12 3 7 8"/>
                                        <line x1="12" y1="3" x2="12" y2="15"/>
                                    </svg>
                                    <div style="text-align:center">
                                        <div style="font-size:13px;font-weight:500;color:var(--text-primary)" id="drop-label">Click to browse or drag &amp; drop</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:3px">Kaspi Bank PDF, CSV or TXT</div>
                                    </div>
                                    <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt,.pdf" style="display:none" onchange="updateImportLabel(this)">
                                </label>

                                <div id="file-preview" style="display:none;margin-top:12px;padding:10px 12px;background:#f0f4ff;border-radius:12px;align-items:center;gap:10px">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span id="file-name" style="font-size:12px;font-weight:500;color:#1d4ed8;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></span>
                                    <span id="file-size" style="font-size:11px;color:#3b82f6;flex-shrink:0"></span>
                                </div>

                                <button type="submit" class="btn primary" style="width:100%;justify-content:center;margin-top:14px;padding:10px 0;font-size:13px;border-radius:16px" id="import-submit-btn">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Upload &amp; Import
                                </button>
                            </div>
                        </form>

                        <div style="border-top:1px solid #f5f4f0;padding-top:16px">
                            <div style="font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px">Validation rules</div>
                            <div style="display:flex;flex-direction:column;gap:7px">
                                <div style="display:flex;align-items:flex-start;gap:8px;font-size:11px;color:var(--text-primary)">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="6" cy="6" r="5.5" stroke="#e2e1dd"/><path d="M3.5 6l2 2 3-3" stroke="#10b981" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    Date must be between 2000-01-01 and one year from today
                                </div>
                                <div style="display:flex;align-items:flex-start;gap:8px;font-size:11px;color:var(--text-primary)">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="6" cy="6" r="5.5" stroke="#e2e1dd"/><path d="M3.5 6l2 2 3-3" stroke="#10b981" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    Amount must be non-zero and ≤ 10,000,000
                                </div>
                                <div style="display:flex;align-items:flex-start;gap:8px;font-size:11px;color:var(--text-primary)">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="6" cy="6" r="5.5" stroke="#e2e1dd"/><path d="M3.5 6l2 2 3-3" stroke="#10b981" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    Merchant name must not be empty
                                </div>
                                <div style="display:flex;align-items:flex-start;gap:8px;font-size:11px;color:var(--text-primary)">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="6" cy="6" r="5.5" stroke="#e2e1dd"/><path d="M3.5 6l2 2 3-3" stroke="#10b981" stroke-width="1.3" stroke-linecap="round"/></svg>
                                    Duplicate rows are detected and skipped automatically
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div class="panel">
                            <div class="panel-head"><div class="panel-title">Supported formats</div></div>
                            <div style="display:flex;flex-direction:column;gap:12px">
                                <div style="display:flex;gap:10px;align-items:flex-start">
                                    <div style="width:28px;height:28px;border-radius:8px;background:#fff5f5;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size:12px;font-weight:600;color:var(--text-primary)">Kaspi Bank PDF</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px;line-height:1.5">Gold / Red statements.<br><code style="background:#f0eee8;padding:1px 4px;border-radius:3px;font-size:10px">DD.MM.YY ± amount ₸ Type Merchant</code></div>
                                    </div>
                                </div>
                                <div style="display:flex;gap:10px;align-items:flex-start">
                                    <div style="width:28px;height:28px;border-radius:8px;background:#e6f7ed;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-size:12px;font-weight:600;color:var(--text-primary)">CSV / TXT</div>
                                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px"><code style="background:#f0eee8;padding:1px 4px;border-radius:3px;font-size:10px">date, amount, merchant, description</code></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel">
                            <div class="panel-head"><div class="panel-title">Data schema</div></div>
                            @php
                                $fields = [
                                    ['date','transaction_date','Date of transaction','#f0f4ff','#1a56db'],
                                    ['#','amount','Amount (negative = expense)','#e6f7ed','#059669'],
                                    ['T','merchant_name','Merchant / payee name','#faf5ff','#7c3aed'],
                                    ['i','description','Transaction type or note','#fff9eb','#d97706'],
                                    ['$','currency','Currency code (KZT, USD…)','#e0f7fe','#0369a1'],
                                ];
                            @endphp
                            @foreach($fields as $f)
                                <div style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid #f5f4f0;{{ $loop->last ? 'border-bottom:none' : '' }}">
                                    <div style="width:22px;height:22px;border-radius:6px;background:{{ $f[3] }};color:{{ $f[4] }};font-size:9px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ $f[0] }}</div>
                                    <div style="flex:1;min-width:0">
                                        <div style="font-size:11px;font-weight:600;color:var(--text-primary);font-family:monospace">{{ $f[1] }}</div>
                                        <div style="font-size:10px;color:var(--text-muted)">{{ $f[2] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="panel">
                            <div class="panel-head"><div class="panel-title">Recent imports</div></div>
                            @forelse ($recentImports as $import)
                                <div class="upcoming-row">
                                    <div style="flex:1;min-width:0">
                                        <div style="font-size:12px;font-weight:500;color:var(--text-primary)">{{ \Carbon\Carbon::parse($import->import_date)->format('M j, Y') }}</div>
                                    </div>
                                    <span style="background:#f0eee8;color:#1b1b1b;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px">
                                        {{ number_format($import->total) }} tx
                                    </span>
                                </div>
                            @empty
                                <div class="empty-state">No imports yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="page-transactions" style="display:none;">
            <div class="topbar"><div class="page-title">Transactions</div></div>
            <div class="content">
                <div class="filter-bar">
                    <div style="flex:1;min-width:180px">
                        <label>Search merchant / description</label>
                        <input class="filter-input" style="width:100%" type="text" id="tx-search" placeholder="e.g. Netflix, Spotify…" oninput="filterTransactions()">
                    </div>
                    <div>
                        <label>Date from</label>
                        <input class="filter-input" type="date" id="tx-date-from" onchange="filterTransactions()">
                    </div>
                    <div>
                        <label>Date to</label>
                        <input class="filter-input" type="date" id="tx-date-to" onchange="filterTransactions()">
                    </div>
                    <button class="btn" onclick="clearTxFilters()">Clear</button>
                </div>
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-title">All transactions</div>
                        <div class="panel-action" id="tx-count">
                            @if ($transactionCount > $transactions->count())
                                showing {{ number_format($transactions->count()) }} of {{ number_format($transactionCount) }} records
                            @else
                                {{ number_format($transactionCount) }} records
                            @endif
                        </div>
                    </div>
                    <div id="tx-list">
                        @forelse ($transactions as $tx)
                            @php $initials = strtoupper(substr($tx->merchant_name ?? 'UN', 0, 2)); @endphp
                            <div class="sub-row tx-row"
                                 data-merchant="{{ strtolower($tx->merchant_name ?? '') }}"
                                 data-desc="{{ strtolower($tx->description ?? '') }}"
                                 data-date="{{ $tx->transaction_date }}">
                                <div class="sub-logo" style="background:#f0eee8;color:#4a4947">{{ $initials }}</div>
                                <div class="sub-info">
                                    <div class="sub-name">{{ $tx->merchant_name ?? 'Unknown' }}</div>
                                    <div class="sub-meta">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M j, Y') }}{{ $tx->description ? ' · ' . Str::limit($tx->description, 50) : '' }}</div>
                                </div>
                                <div class="sub-right">
                                    <div class="sub-amount" style="color:{{ $tx->amount < 0 ? '#dc2626' : '#059669' }}">
                                        {{ $tx->amount < 0 ? '-' : '+' }}{{ number_format(abs($tx->amount), 2) }} {{ $tx->currency ?? 'USD' }}
                                    </div>
                                    <div class="sub-next">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M j') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">No transactions yet.<br>Import a CSV to get started.</div>
                        @endforelse
                    </div>
                    <div id="tx-empty" style="display:none" class="empty-state">No transactions match your filters.</div>
                </div>
            </div>
        </div>

        <!-- ══ MERCHANTS ══ -->
        <div id="page-merchants" style="display:none;">
            <div class="topbar"><div class="page-title">Merchants</div></div>
            <div class="content">
                <div class="filter-bar">
                    <div style="flex:1;min-width:200px">
                        <label>Search merchants</label>
                        <input class="filter-input" style="width:100%" type="text" id="merchant-search" placeholder="e.g. Netflix…" oninput="filterMerchants()">
                    </div>
                    <button class="btn" onclick="clearMerchantFilters()">Clear</button>
                </div>
                <div class="panel">
                    <table class="merchant-table">
                        <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Canonical Name</th>
                            <th style="text-align:right">Transactions</th>
                        </tr>
                        </thead>
                        <tbody id="merchant-list">
                        @forelse ($merchants as $merchant)
                            <tr class="merchant-row" data-name="{{ strtolower($merchant->name) }}" data-canonical="{{ strtolower($merchant->canonical_name ?? '') }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="sub-logo" style="background:#f0eee8;color:#1b1b1b;width:30px;height:30px;font-size:10px">
                                            {{ strtoupper(substr($merchant->name, 0, 2)) }}
                                        </div>
                                        <span style="font-weight:500;color:var(--text-primary)">{{ $merchant->name }}</span>
                                    </div>
                                </td>
                                <td style="color:var(--text-muted)">{{ $merchant->canonical_name ?? '—' }}</td>
                                <td style="text-align:right">
                                        <span style="background:#f0eee8;color:#1b1b1b;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px">
                                            {{ number_format($merchant->transactions_count) }}
                                        </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="empty-state">No merchants yet. Import a CSV to populate merchants.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    <div id="merchant-empty" style="display:none" class="empty-state">No merchants match your search.</div>
                </div>
            </div>
        </div>

        <div id="page-profile" style="display:none;">
            <div class="topbar"><div class="page-title">Profile</div></div>
            <div class="content" style="max-width:560px">

                @if (session('status') === 'profile-updated')
                    <div class="profile-success">Profile updated successfully.</div>
                @endif

                <div class="profile-card">
                    <h3>Profile Information</h3>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')
                        <div class="profile-field">
                            <label class="profile-label">Full name</label>
                            <input class="profile-input" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="profile-field">
                            <label class="profile-label">Email address</label>
                            <input class="profile-input" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn primary" style="margin-top:4px">Save changes</button>
                    </form>
                </div>

                <div class="profile-card">
                    <h3>Update Password</h3>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="profile-field">
                            <label class="profile-label">Current password</label>
                            <input class="profile-input" type="password" name="current_password" autocomplete="current-password">
                            @error('current_password', 'updatePassword') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="profile-field">
                            <label class="profile-label">New password</label>
                            <input class="profile-input" type="password" name="password" autocomplete="new-password">
                            @error('password', 'updatePassword') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="profile-field">
                            <label class="profile-label">Confirm new password</label>
                            <input class="profile-input" type="password" name="password_confirmation" autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn primary" style="margin-top:4px">Update password</button>
                    </form>
                </div>

                <div class="profile-danger">
                    <h3>Delete Account</h3>
                    <p>Once your account is deleted, all data will be permanently removed. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <div class="profile-field">
                            <label class="profile-label">Enter your password to confirm</label>
                            <input class="profile-input" type="password" name="password" placeholder="••••••••">
                            @error('password', 'userDeletion') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn" style="border-color:#ef4444;color:#ef4444;margin-top:4px" onclick="return confirm('Delete your account permanently?')">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="page-subscriptions" style="display:none;">
            <div class="topbar">
                <div class="page-title">All subscriptions</div>
                <div class="topbar-actions">
                    <button class="btn" onclick="setPage('detected')" style="position:relative">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M8 3v2M8 11v2M3 8H1M15 8h-2M5.05 5.05 3.64 3.64M12.36 12.36l-1.41-1.41M10.95 5.05l1.41-1.41M3.64 12.36l1.41-1.41" stroke-linecap="round"/></svg>
                        Review Detections
                        @if ($detected->count() > 0)
                            <span style="position:absolute;top:-6px;right:-6px;background:#1b1b1b;color:#fff;border-radius:9999px;font-size:10px;font-weight:700;padding:1px 5px;line-height:16px">{{ $detected->count() }}</span>
                        @endif
                    </button>
                    <form method="POST" action="{{ route('detect') }}" style="display:inline" id="detect-form-subs">
                        @csrf
                        <button type="submit" class="btn primary" id="detect-btn-subs">
                            <svg viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.5"><path d="M13 8A5 5 0 1 1 8 3" stroke-linecap="round"/><path d="M13 3v3h-3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Re-scan
                        </button>
                    </form>
                </div>
            </div>
            <div class="content">
                <div class="panel">
                    @forelse ($subscriptions as $sub)
                        @php
                            $initials = strtoupper(substr($sub->name, 0, 2));
                            $confClass = $sub->confidence_score >= 80 ? 'conf-high' : 'conf-med';
                            $colors = ['#f0f4ff|#1a56db','#e6f7ed|#059669','#fff5f5|#dc2626','#faf5ff|#7c3aed','#fff9eb|#d97706','#fff0f6|#be185d'];
                            $colorPair = explode('|', $colors[$loop->index % count($colors)]);
                        @endphp
                        <div class="sub-row">
                            <div class="sub-logo" style="background:{{ $colorPair[0] }};color:{{ $colorPair[1] }}">{{ $initials }}</div>
                            <div class="sub-info">
                                <div class="sub-name">{{ $sub->name }} <span class="conf-pill {{ $confClass }}">{{ number_format($sub->confidence_score) }}%</span></div>
                                <div class="sub-meta">{{ ucfirst($sub->billing_cycle) }} · ${{ number_format($sub->amount, 2) }}</div>
                            </div>
                            <div class="sub-right">
                                <div class="sub-amount">${{ number_format($sub->amount, 2) }}</div>
                                <div class="sub-next">{{ $sub->next_billing_date ? \Carbon\Carbon::parse($sub->next_billing_date)->format('M j') : '—' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No subscriptions yet.<br>Import a CSV to detect recurring charges.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ══ EDIT SUBSCRIPTION MODAL ══ -->
<div id="edit-modal-backdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:1000;align-items:center;justify-content:center;backdrop-filter:blur(4px)" onclick="closeEditModal(event)">
    <div style="background:#fff;border-radius:24px;width:100%;max-width:440px;margin:0 16px;box-shadow:0 24px 64px rgba(0,0,0,.15);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f5f4f0;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:var(--text-primary)">Edit subscription</div>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:#b5b4b0;padding:4px;line-height:1">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 3l10 10M13 3 3 13"/></svg>
            </button>
        </div>
        <form id="edit-modal-form" method="POST" style="padding:20px 24px 24px">
            @csrf
            @method('PATCH')
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em">Service name</label>
                <input id="edit-name" name="name" type="text" required maxlength="255"
                       style="width:100%;padding:10px 13px;border:1px solid #e2e1dd;border-radius:12px;font-size:13px;color:var(--text-primary);box-sizing:border-box;outline:none;background:#faf9f5"
                       onfocus="this.style.borderColor='#b5b4b0';this.style.background='#fff'" onblur="this.style.borderColor='#e2e1dd';this.style.background='#faf9f5'">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em">Amount</label>
                    <input id="edit-amount" name="amount" type="number" step="0.01" min="0" required
                           style="width:100%;padding:10px 13px;border:1px solid #e2e1dd;border-radius:12px;font-size:13px;color:var(--text-primary);box-sizing:border-box;outline:none;background:#faf9f5"
                           onfocus="this.style.borderColor='#b5b4b0';this.style.background='#fff'" onblur="this.style.borderColor='#e2e1dd';this.style.background='#faf9f5'">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em">Currency</label>
                    <input id="edit-currency" name="currency" type="text" maxlength="3" required
                           style="width:100%;padding:10px 13px;border:1px solid #e2e1dd;border-radius:12px;font-size:13px;color:var(--text-primary);box-sizing:border-box;outline:none;background:#faf9f5;text-transform:uppercase"
                           onfocus="this.style.borderColor='#b5b4b0';this.style.background='#fff'" onblur="this.style.borderColor='#e2e1dd';this.style.background='#faf9f5'">
                </div>
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em">Billing cycle</label>
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px">
                    @foreach(['weekly','monthly','quarterly','yearly'] as $cycle)
                        <label style="cursor:pointer">
                            <input type="radio" name="billing_cycle" value="{{ $cycle }}" style="display:none" class="cycle-radio">
                            <div class="cycle-option" data-val="{{ $cycle }}"
                                 style="text-align:center;padding:8px 4px;border:1px solid #e2e1dd;border-radius:12px;font-size:11px;font-weight:600;color:#4a4947;transition:all .15s;user-select:none">
                                {{ ucfirst($cycle) }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div style="display:flex;gap:10px">
                <button type="button" onclick="closeEditModal()"
                        style="flex:1;padding:10px;border:1px solid #e2e1dd;border-radius:12px;background:#faf9f5;font-size:13px;font-weight:600;color:#4a4947;cursor:pointer">
                    Cancel
                </button>
                <button type="submit"
                        style="flex:2;padding:10px;border:none;border-radius:12px;background:#1b1b1b;color:#fff;font-size:13px;font-weight:600;cursor:pointer">
                    Save &amp; Confirm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    var pages = ['dashboard','detected','alerts','reports','import','transactions','subscriptions','merchants','profile'];

    function setPage(name) {
        for (var i = 0; i < pages.length; i++) {
            var el = document.getElementById('page-' + pages[i]);
            if (el) el.style.display = pages[i] === name ? 'block' : 'none';
        }
        var items = document.querySelectorAll('.nav-item');
        for (var j = 0; j < items.length; j++) {
            items[j].classList.remove('active');
            var oc = items[j].getAttribute('onclick');
            if (oc && oc.indexOf("'" + name + "'") !== -1) items[j].classList.add('active');
        }
    }

    function updateImportLabel(input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];
        var label = document.getElementById('drop-label');
        var preview = document.getElementById('file-preview');
        var fileName = document.getElementById('file-name');
        var fileSize = document.getElementById('file-size');
        label.textContent = file.name;
        if (preview) {
            preview.style.display = 'flex';
            fileName.textContent = file.name;
            fileSize.textContent = (file.size / 1024).toFixed(0) + ' KB';
        }
    }

    var dropZone = document.getElementById('drop-zone');
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.style.borderColor = '#b5b4b0';
            dropZone.style.background = '#f0eee8';
        });
        dropZone.addEventListener('dragleave', function() {
            dropZone.style.borderColor = '#e2e1dd';
            dropZone.style.background = '#faf9f5';
        });
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.style.borderColor = '#e2e1dd';
            dropZone.style.background = '#faf9f5';
            var file = e.dataTransfer.files[0];
            if (file) {
                var input = document.getElementById('csv_file');
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                updateImportLabel(input);
            }
        });
    }

    var importForm = document.getElementById('import-form');
    if (importForm) {
        importForm.addEventListener('submit', function() {
            var btn = document.getElementById('import-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="upload-spinner"></span> Importing…';
        });
    }

    // Transaction filters
    function filterTransactions() {
        var search = document.getElementById('tx-search').value.toLowerCase();
        var dateFrom = document.getElementById('tx-date-from').value;
        var dateTo = document.getElementById('tx-date-to').value;
        var rows = document.querySelectorAll('.tx-row');
        var visible = 0;
        rows.forEach(function(row) {
            var merchant = row.dataset.merchant || '';
            var desc = row.dataset.desc || '';
            var date = row.dataset.date || '';
            var matchSearch = !search || merchant.includes(search) || desc.includes(search);
            var matchFrom = !dateFrom || date >= dateFrom;
            var matchTo = !dateTo || date <= dateTo;
            var show = matchSearch && matchFrom && matchTo;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        var countEl = document.getElementById('tx-count');
        if (countEl) countEl.textContent = visible + ' records';
        var emptyEl = document.getElementById('tx-empty');
        if (emptyEl) emptyEl.style.display = visible === 0 ? 'block' : 'none';
    }

    function clearTxFilters() {
        document.getElementById('tx-search').value = '';
        document.getElementById('tx-date-from').value = '';
        document.getElementById('tx-date-to').value = '';
        filterTransactions();
    }

    function filterMerchants() {
        var search = document.getElementById('merchant-search').value.toLowerCase();
        var rows = document.querySelectorAll('.merchant-row');
        var visible = 0;
        rows.forEach(function(row) {
            var name = row.dataset.name || '';
            var canonical = row.dataset.canonical || '';
            var show = !search || name.includes(search) || canonical.includes(search);
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        var emptyEl = document.getElementById('merchant-empty');
        if (emptyEl) emptyEl.style.display = visible === 0 ? 'block' : 'none';
    }

    function clearMerchantFilters() {
        document.getElementById('merchant-search').value = '';
        filterMerchants();
    }

    ['detect-form','detect-form-subs','detect-form-det'].forEach(function(id) {
        var form = document.getElementById(id);
        if (!form) return;
        form.addEventListener('submit', function() {
            form.querySelectorAll('button[type=submit]').forEach(function(btn) {
                btn.disabled = true;
                btn.innerHTML = '⏳ Scanning…';
            });
        });
    });

    var baseUpdateUrl = '{{ url('/subscriptions') }}/';

    function openEditModal(id, name, amount, cycle, currency) {
        var form = document.getElementById('edit-modal-form');
        form.action = baseUpdateUrl + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-amount').value = amount;
        document.getElementById('edit-currency').value = currency;

        document.querySelectorAll('.cycle-radio').forEach(function(radio) {
            radio.checked = radio.value === cycle;
        });
        document.querySelectorAll('.cycle-option').forEach(function(opt) {
            var active = opt.dataset.val === cycle;
            opt.style.background = active ? '#f0eee8' : '#faf9f5';
            opt.style.borderColor = active ? '#b5b4b0' : '#e2e1dd';
            opt.style.color = active ? '#1b1b1b' : '#4a4947';
        });

        var backdrop = document.getElementById('edit-modal-backdrop');
        backdrop.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(function() { document.getElementById('edit-name').focus(); }, 50);
    }

    function closeEditModal(e) {
        if (e && e.target !== document.getElementById('edit-modal-backdrop')) return;
        document.getElementById('edit-modal-backdrop').style.display = 'none';
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.cycle-option').forEach(function(opt) {
        opt.addEventListener('click', function() {
            var val = opt.dataset.val;
            document.querySelectorAll('.cycle-option').forEach(function(o) {
                var active = o.dataset.val === val;
                o.style.background = active ? '#f0eee8' : '#faf9f5';
                o.style.borderColor = active ? '#b5b4b0' : '#e2e1dd';
                o.style.color = active ? '#1b1b1b' : '#4a4947';
            });
            document.querySelectorAll('.cycle-radio').forEach(function(r) {
                r.checked = r.value === val;
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('edit-modal-backdrop').style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    var flashEl = document.getElementById('flash-success');
    if (flashEl) {
        setTimeout(function() {
            flashEl.style.transition = 'opacity 0.4s ease';
            flashEl.style.opacity = '0';
            setTimeout(function() { flashEl.style.display = 'none'; }, 400);
        }, 7000);
    }

    @if (session('status') === 'profile-updated')
    setPage('profile');
    @elseif ($errors->has('csv_file') || session('open_page') === 'import')
    setPage('import');
    @elseif (session('open_page'))
    setPage('{{ session('open_page') }}');
    @elseif ($errors->any())
    setPage('profile');
    @endif
</script>
</body>
</html>
