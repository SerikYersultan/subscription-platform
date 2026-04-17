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
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; }
:root { --brand: #1a56db; --brand-dark: #1040b0; --sidebar-w: 210px; }

.app { display: flex; height: 100vh; overflow: hidden; }

.sidebar {
  width: var(--sidebar-w); background: #ffffff;
  border-right: 1px solid #e5e7eb;
  display: flex; flex-direction: column; flex-shrink: 0; overflow-y: auto;
}
.sidebar-logo { padding: 16px; border-bottom: 1px solid #e5e7eb; }
.logo-mark { display: flex; align-items: center; gap: 8px; }
.logo-icon { width: 30px; height: 30px; background: var(--brand); border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.logo-icon svg { width: 16px; height: 16px; fill: white; }
.logo-text { font-size: 14px; font-weight: 600; color: #111827; }
.logo-sub { font-size: 10px; color: #6b7280; }

.nav { padding: 8px; flex: 1; }
.nav-section { font-size: 10px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.06em; padding: 10px 8px 4px; }
.nav-item {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 10px; border-radius: 6px;
  font-size: 13px; color: #374151; cursor: pointer;
  transition: background 0.12s; margin-bottom: 1px;
}
.nav-item:hover { background: #f3f4f6; color: #111827; }
.nav-item.active { background: #eff6ff; color: var(--brand); font-weight: 500; }
.nav-item svg { width: 15px; height: 15px; flex-shrink: 0; }
.nav-badge { margin-left: auto; background: #ef4444; color: white; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 10px; }
.nav-badge.blue { background: var(--brand); }

.sidebar-footer { padding: 12px 8px; border-top: 1px solid #e5e7eb; }
.user-row { display: flex; align-items: center; gap: 8px; padding: 6px 8px; border-radius: 6px; }
.avatar { width: 30px; height: 30px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: var(--brand); flex-shrink: 0; }
.user-name { font-size: 12px; font-weight: 600; color: #111827; }
.user-plan { font-size: 10px; color: #6b7280; }

.logout-btn { display: flex; align-items: center; gap: 6px; padding: 6px 8px; border-radius: 6px; font-size: 11px; color: #6b7280; cursor: pointer; width: 100%; background: none; border: none; margin-top: 4px; }
.logout-btn:hover { background: #f3f4f6; color: #374151; }

.main { flex: 1; overflow-y: auto; display: flex; flex-direction: column; background: #f9fafb; }

.topbar { background: #ffffff; border-bottom: 1px solid #e5e7eb; padding: 12px 20px; display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
.page-title { font-size: 15px; font-weight: 600; color: #111827; flex: 1; }
.topbar-actions { display: flex; gap: 8px; align-items: center; }

.btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 13px; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; border: 1px solid #d1d5db; background: #ffffff; color: #374151; transition: background 0.12s; }
.btn:hover { background: #f3f4f6; }
.btn.primary { background: var(--brand); color: white; border-color: var(--brand); }
.btn.primary:hover { background: var(--brand-dark); }
.btn svg { width: 13px; height: 13px; }

.search-bar { display: flex; align-items: center; gap: 6px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 10px; }
.search-bar input { background: none; border: none; outline: none; font-size: 12px; color: #111827; width: 160px; }
.search-bar svg { width: 12px; height: 12px; color: #9ca3af; flex-shrink: 0; }

.content { padding: 20px; flex: 1; }

.flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; font-size: 12px; color: #166534; }
.flash-error { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 12px; color: #991b1b; }

.alert-strip { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; cursor: pointer; }
.alert-strip:hover { background: #fef3c7; }
.alert-dot { width: 8px; height: 8px; border-radius: 50%; background: #f59e0b; flex-shrink: 0; }
.alert-text { font-size: 12px; color: #78350f; flex: 1; }
.alert-count { font-size: 11px; font-weight: 600; color: #92400e; background: #fde68a; padding: 2px 8px; border-radius: 10px; white-space: nowrap; }

.metrics-row { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 20px; }
.metric-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px 16px; cursor: pointer; transition: box-shadow 0.12s; position: relative; }
.metric-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.metric-label { font-size: 11px; color: #6b7280; margin-bottom: 6px; }
.metric-value { font-size: 22px; font-weight: 600; color: #111827; margin-bottom: 3px; }
.metric-sub { font-size: 11px; color: #6b7280; }
.badge-up { color: #ef4444; font-weight: 600; }
.badge-dn { color: #10b981; font-weight: 600; }
.metric-icon { position: absolute; top: 14px; right: 14px; width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; }
.icon-blue { background: #dbeafe; }
.icon-green { background: #d1fae5; }
.icon-amber { background: #fef3c7; }
.icon-red { background: #fee2e2; }

.grid-2 { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 16px; margin-bottom: 16px; }

.panel { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 0; }
.panel-head { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; }
.panel-title { font-size: 13px; font-weight: 600; color: #111827; flex: 1; }
.panel-action { font-size: 11px; color: var(--brand); cursor: pointer; font-weight: 500; }
.panel-action:hover { text-decoration: underline; }

.tab-row { display: flex; border-bottom: 1px solid #e5e7eb; padding: 0 16px; }
.tab { font-size: 12px; font-weight: 500; padding: 9px 12px; cursor: pointer; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -1px; transition: all 0.12s; }
.tab.active { color: var(--brand); border-bottom-color: var(--brand); }
.tab:hover:not(.active) { color: #374151; }

.sub-row { display: flex; align-items: center; gap: 12px; padding: 11px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.1s; }
.sub-row:last-child { border-bottom: none; }
.sub-row:hover { background: #f9fafb; }
.sub-logo { width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
.sub-info { flex: 1; min-width: 0; }
.sub-name { font-size: 13px; font-weight: 500; color: #111827; }
.sub-meta { font-size: 11px; color: #6b7280; margin-top: 1px; }
.sub-right { text-align: right; flex-shrink: 0; }
.sub-amount { font-size: 13px; font-weight: 600; color: #111827; }
.sub-next { font-size: 11px; color: #6b7280; }

.conf-pill { display: inline-block; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 10px; margin-left: 4px; }
.conf-high { background: #d1fae5; color: #065f46; }
.conf-med { background: #fef3c7; color: #92400e; }

.tag { display: inline-flex; align-items: center; font-size: 10px; font-weight: 500; padding: 2px 7px; border-radius: 10px; }
.tag-blue { background: #dbeafe; color: #1d4ed8; }
.tag-amber { background: #fef3c7; color: #92400e; }
.tag-red { background: #fee2e2; color: #991b1b; }
.tag-green { background: #d1fae5; color: #065f46; }
.tag-gray { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

.upcoming-row { display: flex; align-items: center; gap: 10px; padding: 9px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.1s; }
.upcoming-row:last-child { border-bottom: none; }
.upcoming-row:hover { background: #f9fafb; }
.upcoming-date { font-size: 11px; font-weight: 600; color: #6b7280; width: 42px; flex-shrink: 0; }
.upcoming-name { font-size: 12px; color: #374151; flex: 1; }
.upcoming-amt { font-size: 12px; font-weight: 600; color: #111827; }
.due-soon { color: #dc2626; }

.alert-list {}
.alert-row { display: flex; align-items: flex-start; gap: 10px; padding: 11px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.1s; }
.alert-row:last-child { border-bottom: none; }
.alert-row:hover { background: #f9fafb; }
.alert-icon { width: 28px; height: 28px; border-radius: 6px; flex-shrink: 0; margin-top: 1px; display: flex; align-items: center; justify-content: center; }
.alert-body { flex: 1; }
.alert-title { font-size: 12px; font-weight: 600; color: #111827; }
.alert-desc { font-size: 11px; color: #6b7280; margin-top: 2px; }
.alert-time { font-size: 10px; color: #9ca3af; flex-shrink: 0; margin-top: 2px; }

.chart-area { padding: 16px; }
.empty-state { padding: 32px 16px; text-align: center; color: #9ca3af; font-size: 12px; }

.filter-bar { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
.filter-bar label { font-size: 10px; font-weight: 500; color: #6b7280; display: block; margin-bottom: 4px; }
.filter-input { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; color: #111827; outline: none; background: #fff; }
.filter-input:focus { border-color: var(--brand); box-shadow: 0 0 0 2px rgba(26,86,219,0.1); }

.profile-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 24px; margin-bottom: 16px; }
.profile-card h3 { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; }
.profile-field { margin-bottom: 14px; }
.profile-label { font-size: 11px; font-weight: 500; color: #6b7280; margin-bottom: 5px; display: block; }
.profile-input { width: 100%; padding: 8px 11px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 13px; color: #111827; outline: none; }
.profile-input:focus { border-color: var(--brand); box-shadow: 0 0 0 2px rgba(26,86,219,0.1); }
.profile-error { font-size: 11px; color: #ef4444; margin-top: 3px; }
.profile-success { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 7px; padding: 9px 12px; font-size: 12px; color: #166534; margin-bottom: 14px; }
.profile-danger { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 20px 24px; }
.profile-danger h3 { font-size: 13px; font-weight: 600; color: #991b1b; margin-bottom: 8px; }
.profile-danger p { font-size: 12px; color: #6b7280; margin-bottom: 14px; }

.merchant-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.merchant-table th { text-align: left; padding: 10px 16px; font-size: 11px; font-weight: 500; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
.merchant-table td { padding: 10px 16px; border-bottom: 1px solid #f3f4f6; color: #374151; }
.merchant-table tr:last-child td { border-bottom: none; }
.merchant-table tr:hover td { background: #f9fafb; }

#modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 200; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<div class="app">
  <div class="sidebar">
    <div class="sidebar-logo" onclick="setPage('dashboard')" style="cursor:pointer">
      <div class="logo-mark">
        <div class="logo-icon">
          <svg viewBox="0 0 16 16"><path d="M8 2C4.7 2 2 4.7 2 8s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zm0 2c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .4-1 1-1zm0 8c-1.7 0-3.1-.8-4-2 .1-1.3 2.7-2 4-2s3.9.7 4 2c-.9 1.2-2.3 2-4 2z"/></svg>
        </div>
        <div>
          <div class="logo-text">SubTrack</div>
          <div class="logo-sub">Intelligence Platform</div>
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
        Import CSV
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

    <!-- DASHBOARD -->
    <div id="page-dashboard">
      <div class="topbar">
        <div class="page-title">Dashboard</div>
        <div class="topbar-actions">
          <button class="btn" onclick="setPage('import')">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 10V3M5 7l3 3 3-3M3 13h10"/></svg>
            Import CSV
          </button>
          <form method="POST" action="{{ route('detect') }}" style="display:inline" id="detect-form">
            @csrf
            <button type="submit" class="btn primary" id="detect-btn">
              <svg viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.5"><circle cx="7" cy="7" r="4.5"/><path d="m11 11 3 3" stroke-linecap="round"/></svg>
              Run Detector
            </button>
          </form>
        </div>
      </div>
      <div class="content">

        @if (session('success'))
          <div class="flash-success">✅ {{ session('success') }}</div>
        @endif

        @if ($unreadAlertsCount > 0)
        <div class="alert-strip" onclick="setPage('alerts')">
          <div class="alert-dot"></div>
          <div class="alert-text"><strong>{{ $unreadAlertsCount }} alert{{ $unreadAlertsCount > 1 ? 's' : '' }} need your attention</strong></div>
          <div class="alert-count">View all</div>
        </div>
        @endif

        <div class="metrics-row">
          <div class="metric-card" onclick="setPage('subscriptions')">
            <div class="metric-icon icon-blue"><svg width="14" height="14" viewBox="0 0 16 16"><path d="M2 12l4-4 3 3 5-6" stroke="#1a56db" fill="none" stroke-width="1.8" stroke-linecap="round"/></svg></div>
            <div class="metric-label">Monthly burn rate</div>
            <div class="metric-value">${{ number_format($monthlyBurn, 2) }}</div>
            <div class="metric-sub">across all subscriptions</div>
          </div>
          <div class="metric-card" onclick="setPage('subscriptions')">
            <div class="metric-icon icon-green"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="8" cy="8" r="5"/><path d="m5.5 8 2 2 3-3" stroke-linecap="round"/></svg></div>
            <div class="metric-label">Active subscriptions</div>
            <div class="metric-value">{{ $subscriptions->count() }}</div>
            <div class="metric-sub">currently tracked</div>
          </div>
          <div class="metric-card" onclick="setPage('transactions')">
            <div class="metric-icon icon-amber"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#d97706" stroke-width="1.2"><path d="M2 5h12M2 8h8M2 11h6"/></svg></div>
            <div class="metric-label">Transactions</div>
            <div class="metric-value">{{ $transactions->count() }}</div>
            <div class="metric-sub">recent records</div>
          </div>
          <div class="metric-card" onclick="setPage('detected')">
            <div class="metric-icon icon-red"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#dc2626" stroke-width="1.6"><circle cx="8" cy="8" r="5"/><path d="m5 5 6 6M11 5 5 11" stroke-linecap="round"/></svg></div>
            <div class="metric-label">Unconfirmed detections</div>
            <div class="metric-value">{{ $detected->count() }}</div>
            <div class="metric-sub">Need your review</div>
          </div>
        </div>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-head">
              <div class="panel-title">Active subscriptions</div>
              <div class="panel-action" onclick="setPage('subscriptions')">View all</div>
            </div>
            <div id="sub-list">
              @forelse ($subscriptions->take(6) as $sub)
              @php
                $initials = strtoupper(substr($sub->name, 0, 2));
                $confClass = $sub->confidence_score >= 80 ? 'conf-high' : 'conf-med';
                $colors = ['#dbeafe|#1d4ed8','#d1fae5|#059669','#fee2e2|#dc2626','#f3e8ff|#7c3aed','#fef3c7|#d97706','#fce7f3|#be185d'];
                $colorPair = explode('|', $colors[$loop->index % count($colors)]);
              @endphp
              <div class="sub-row">
                <div class="sub-logo" style="background:{{ $colorPair[0] }};color:{{ $colorPair[1] }}">{{ $initials }}</div>
                <div class="sub-info">
                  <div class="sub-name">{{ $sub->name }} <span class="conf-pill {{ $confClass }}">{{ number_format($sub->confidence_score) }}%</span></div>
                  <div class="sub-meta">{{ ucfirst($sub->billing_cycle) }}</div>
                </div>
                <div class="sub-right">
                  <div class="sub-amount">${{ number_format($sub->amount, 2) }}</div>
                  <div class="sub-next">{{ $sub->next_billing_date ? \Carbon\Carbon::parse($sub->next_billing_date)->format('M j') : '—' }}</div>
                </div>
              </div>
              @empty
              <div class="empty-state">No subscriptions yet.<br>Import a CSV to detect them.</div>
              @endforelse
            </div>
          </div>

          <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="panel">
              <div class="panel-head"><div class="panel-title">Upcoming charges</div></div>
              @forelse ($upcomingCharges->take(4) as $sub)
              @php $daysUntil = \Carbon\Carbon::parse($sub->next_billing_date)->startOfDay()->diffInDays(now()->startOfDay(), false); @endphp
              <div class="upcoming-row">
                <div class="upcoming-date {{ $daysUntil <= 3 ? 'due-soon' : '' }}">{{ \Carbon\Carbon::parse($sub->next_billing_date)->format('M j') }}</div>
                <div class="upcoming-name">{{ $sub->name }}</div>
                <div class="upcoming-amt {{ $daysUntil <= 3 ? 'due-soon' : '' }}">${{ number_format($sub->amount, 2) }}</div>
              </div>
              @empty
              <div class="empty-state">No charges in the next 7 days.</div>
              @endforelse
            </div>
            <div class="panel">
              <div class="panel-head"><div class="panel-title">Recent alerts</div><div class="panel-action" onclick="setPage('alerts')">View all</div></div>
              <div class="alert-list">
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
    </div>

    <!-- DETECTED -->
    <div id="page-detected" style="display:none;">
      <div class="topbar"><div class="page-title">Unconfirmed detections</div></div>
      <div class="content">
        <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">The detection engine found these recurring patterns. Confidence score below 80%.</p>
        <div class="panel">
          @forelse ($detected as $sub)
          @php
            $initials = strtoupper(substr($sub->name, 0, 2));
            $confColor = $sub->confidence_score >= 60 ? '#16a34a' : '#d97706';
          @endphp
          <div class="sub-row" style="flex-direction:column;align-items:stretch;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div class="sub-logo" style="background:#f0fdf4;color:#16a34a">{{ $initials }}</div>
              <div style="flex:1">
                <div style="font-size:13px;font-weight:500;color:#111827">{{ $sub->name }}</div>
                <div style="font-size:11px;color:#6b7280;margin-top:2px">{{ ucfirst($sub->billing_cycle) }} ${{ number_format($sub->amount, 2) }}{{ $sub->next_billing_date ? ' - Next ' . \Carbon\Carbon::parse($sub->next_billing_date)->format('M j') : '' }}</div>
              </div>
            </div>
            <div style="background:#f9fafb;border-radius:6px;padding:8px 12px;font-size:11px;color:#6b7280">
              Confidence: <strong style="color:{{ $confColor }}">{{ number_format($sub->confidence_score) }}%</strong> - {{ ucfirst($sub->billing_cycle) }} cadence detected
            </div>
          </div>
          @empty
          <div class="empty-state">No unconfirmed detections. Import a CSV to start detection.</div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- ALERTS -->
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

    <!-- REPORTS -->
    <div id="page-reports" style="display:none;">
      <div class="topbar"><div class="page-title">Monthly reports</div></div>
      <div class="content">
        <div class="metrics-row" style="grid-template-columns:repeat(3,minmax(0,1fr))">
          <div class="metric-card">
            <div class="metric-label">Monthly burn rate</div>
            <div class="metric-value">${{ number_format($monthlyBurn, 2) }}</div>
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
            <div style="font-size:12px;color:#111827;flex:1">{{ \Carbon\Carbon::parse($import->import_date)->format('M j, Y') }}</div>
            <div style="font-size:12px;font-weight:600;color:#1a56db">{{ number_format($import->total) }} transactions</div>
          </div>
          @empty
          <div class="empty-state">No imports yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- IMPORT -->
    <div id="page-import" style="display:none;">
      <div class="topbar"><div class="page-title">Import CSV</div></div>
      <div class="content">
        @if ($errors->any())
          <div class="flash-error">
            @foreach ($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif
        <div class="panel" style="max-width:480px;">
          <div class="panel-head"><div class="panel-title">Upload bank export</div></div>
          <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" id="import-form">
            @csrf
            <div style="padding:24px;text-align:center;border:2px dashed #d1d5db;margin:16px;border-radius:8px;cursor:pointer;transition:background 0.1s" id="drop-zone"
                 onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
              <div style="font-size:32px;margin-bottom:8px">📁</div>
              <div style="font-size:13px;font-weight:500;color:#111827" id="drop-label">Drop your CSV file here or <span style="color:var(--brand);text-decoration:underline">click to browse</span></div>
              <div style="font-size:11px;color:#6b7280;margin-top:4px">CSV only, max 10 MB</div>
              <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt" style="display:none" onchange="updateImportLabel(this)">
            </div>
            <div style="padding:0 16px 16px">
              <button type="submit" class="btn primary" style="width:100%;justify-content:center" id="import-submit-btn">
                📥 Upload &amp; Process
              </button>
            </div>
          </form>
        </div>
        @if ($recentImports->isNotEmpty())
        <div class="panel" style="max-width:480px;margin-top:16px;">
          <div class="panel-head"><div class="panel-title">Recent imports</div></div>
          @foreach ($recentImports as $import)
          <div class="upcoming-row">
            <div style="font-size:12px;color:#111827;flex:1">{{ \Carbon\Carbon::parse($import->import_date)->format('M j, Y') }}</div>
            <div style="font-size:12px;font-weight:600;color:#1a56db">{{ number_format($import->total) }} transactions</div>
          </div>
          @endforeach
        </div>
        @endif
      </div>
    </div>

    <!-- TRANSACTIONS -->
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
            <div class="panel-action" id="tx-count">{{ $transactions->count() }} records</div>
          </div>
          <div id="tx-list">
          @forelse ($transactions as $tx)
          @php $initials = strtoupper(substr($tx->merchant_name ?? 'UN', 0, 2)); @endphp
          <div class="sub-row tx-row"
               data-merchant="{{ strtolower($tx->merchant_name ?? '') }}"
               data-desc="{{ strtolower($tx->description ?? '') }}"
               data-date="{{ $tx->transaction_date }}">
            <div class="sub-logo" style="background:#f3f4f6;color:#374151">{{ $initials }}</div>
            <div class="sub-info">
              <div class="sub-name">{{ $tx->merchant_name ?? 'Unknown' }}</div>
              <div class="sub-meta">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M j, Y') }}{{ $tx->description ? ' · ' . Str::limit($tx->description, 50) : '' }}</div>
            </div>
            <div class="sub-right">
              <div class="sub-amount" style="color:{{ $tx->amount < 0 ? '#dc2626' : '#059669' }}">
                {{ $tx->amount < 0 ? '-' : '+' }}${{ number_format(abs($tx->amount), 2) }}
              </div>
              <div class="sub-next">{{ $tx->currency ?? 'USD' }}</div>
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

    <!-- MERCHANTS -->
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
                    <div class="sub-logo" style="background:#eff6ff;color:#1d4ed8;width:28px;height:28px;font-size:10px">
                      {{ strtoupper(substr($merchant->name, 0, 2)) }}
                    </div>
                    <span style="font-weight:500;color:#111827">{{ $merchant->name }}</span>
                  </div>
                </td>
                <td style="color:#6b7280">{{ $merchant->canonical_name ?? '—' }}</td>
                <td style="text-align:right">
                  <span style="background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px">
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

    <!-- PROFILE -->
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

    <!-- SUBSCRIPTIONS -->
    <div id="page-subscriptions" style="display:none;">
      <div class="topbar"><div class="page-title">All subscriptions</div></div>
      <div class="content">
        <div class="panel">
          @forelse ($subscriptions as $sub)
          @php
            $initials = strtoupper(substr($sub->name, 0, 2));
            $confClass = $sub->confidence_score >= 80 ? 'conf-high' : 'conf-med';
            $colors = ['#dbeafe|#1d4ed8','#d1fae5|#059669','#fee2e2|#dc2626','#f3e8ff|#7c3aed','#fef3c7|#d97706','#fce7f3|#be185d'];
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
  var label = document.getElementById('drop-label');
  if (input.files && input.files[0]) {
    label.textContent = '📄 ' + input.files[0].name;
  }
}

var dropZone = document.getElementById('drop-zone');
if (dropZone) {
  dropZone.addEventListener('click', function() {
    document.getElementById('csv_file').click();
  });
  dropZone.addEventListener('dragover', function(e) { e.preventDefault(); dropZone.style.background = '#eff6ff'; });
  dropZone.addEventListener('dragleave', function() { dropZone.style.background = ''; });
  dropZone.addEventListener('drop', function(e) {
    e.preventDefault();
    dropZone.style.background = '';
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
    btn.textContent = '⏳ Uploading…';
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

// Merchant filters
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

// Detect button loading state
var detectForm = document.getElementById('detect-form');
if (detectForm) {
  detectForm.addEventListener('submit', function() {
    var btn = document.getElementById('detect-btn');
    btn.disabled = true;
    btn.textContent = '⏳ Running…';
  });
}

// Auto-open profile page if profile was updated
@if (session('status') === 'profile-updated')
  setPage('profile');
@endif

// Auto-open import page if there are validation errors
@if ($errors->any())
  setPage('import');
@endif
</script>

</body>
</html>
