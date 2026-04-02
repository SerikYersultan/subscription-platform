<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
.user-row { display: flex; align-items: center; gap: 8px; padding: 6px 8px; border-radius: 6px; cursor: pointer; }
.user-row:hover { background: #f3f4f6; }
.avatar { width: 30px; height: 30px; border-radius: 50%; background: #dbeafe; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: var(--brand); flex-shrink: 0; }
.user-name { font-size: 12px; font-weight: 600; color: #111827; }
.user-plan { font-size: 10px; color: #6b7280; }
 
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
 
#modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 200; align-items: center; justify-content: center; }
    </style>
</head>
<body>
 
<div class="app">
  <div class="sidebar">
    <div class="sidebar-logo">
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
        <span class="nav-badge blue">24</span>
      </div>
      <div class="nav-section">Detection</div>
      <div class="nav-item" onclick="setPage('detected')">
        <svg viewBox="0 0 16 16" fill="currentColor"><circle cx="7" cy="7" r="5" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="m11 11 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        Detected
        <span class="nav-badge">3</span>
      </div>
      <div class="nav-item" onclick="setPage('transactions')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M2 5h12M2 8h8M2 11h6" stroke="currentColor" stroke-width="1.2" fill="none"/></svg>
        Transactions
      </div>
      <div class="nav-section">Intelligence</div>
      <div class="nav-item" onclick="setPage('alerts')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M8 1a5 5 0 0 1 5 5c0 2.5 1 3.5 1 5H2c0-1.5 1-2.5 1-5a5 5 0 0 1 5-5zm-1 13h2a1 1 0 0 1-2 0z"/></svg>
        Alerts
        <span class="nav-badge">5</span>
      </div>
      <div class="nav-item" onclick="setPage('reports')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M3 2h10a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm2 3v6M8 7v4M11 5v6" stroke="currentColor" stroke-width="1.2" fill="none"/></svg>
        Reports
      </div>
      <div class="nav-item" onclick="setPage('import')">
        <svg viewBox="0 0 16 16" fill="currentColor"><path d="M8 10V3M5 7l3 3 3-3M3 13h10" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
        Import CSV
      </div>
    </div>
    <div class="sidebar-footer">
      <div class="user-row">
        <div class="avatar">JD</div>
        <div>
          <div class="user-name">James Doe</div>
          <div class="user-plan">Pro plan</div>
        </div>
      </div>
    </div>
  </div>
 
  <div class="main">
 
    <!-- DASHBOARD -->
    <div id="page-dashboard">
      <div class="topbar">
        <div class="page-title">Dashboard</div>
        <div class="topbar-actions">
          <div class="search-bar">
            <svg viewBox="0 0 16 16" fill="none" stroke="#9ca3af" stroke-width="1.5"><circle cx="7" cy="7" r="4.5"/><path d="m11 11 3 3" stroke-linecap="round"/></svg>
            <input type="text" placeholder="Search subscriptions..." />
          </div>
          <button class="btn" onclick="showModal('import-modal')">
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 10V3M5 7l3 3 3-3M3 13h10"/></svg>
            Import CSV
          </button>
          <button class="btn primary" onclick="showModal('add-modal')">
            <svg viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"><path d="M8 3v10M3 8h10"/></svg>
            Add subscription
          </button>
        </div>
      </div>
      <div class="content">
        <div class="alert-strip" onclick="setPage('alerts')">
          <div class="alert-dot"></div>
          <div class="alert-text"><strong>5 alerts need your attention</strong> - Netflix price increase detected, Duplicate Spotify charge, Upcoming charges in 3 days</div>
          <div class="alert-count">View all</div>
        </div>
        <div class="metrics-row">
          <div class="metric-card" onclick="setPage('subscriptions')">
            <div class="metric-icon icon-blue"><svg width="14" height="14" viewBox="0 0 16 16"><path d="M2 12l4-4 3 3 5-6" stroke="#1a56db" fill="none" stroke-width="1.8" stroke-linecap="round"/></svg></div>
            <div class="metric-label">Monthly burn rate</div>
            <div class="metric-value">$284.50</div>
            <div class="metric-sub"><span class="badge-up">+$18.50</span> vs last month</div>
          </div>
          <div class="metric-card" onclick="setPage('subscriptions')">
            <div class="metric-icon icon-green"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#059669" stroke-width="1.5"><circle cx="8" cy="8" r="5"/><path d="m5.5 8 2 2 3-3" stroke-linecap="round"/></svg></div>
            <div class="metric-label">Active subscriptions</div>
            <div class="metric-value">24</div>
            <div class="metric-sub"><span class="badge-up">+2</span> new this month</div>
          </div>
          <div class="metric-card" onclick="setPage('alerts')">
            <div class="metric-icon icon-amber"><svg width="14" height="14" viewBox="0 0 16 16" fill="#d97706"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div>
            <div class="metric-label">Price increases</div>
            <div class="metric-value">3</div>
            <div class="metric-sub"><span class="badge-up">+$9.97</span> total increase</div>
          </div>
          <div class="metric-card" onclick="setPage('detected')">
            <div class="metric-icon icon-red"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#dc2626" stroke-width="1.6"><circle cx="8" cy="8" r="5"/><path d="m5 5 6 6M11 5 5 11" stroke-linecap="round"/></svg></div>
            <div class="metric-label">Unconfirmed detections</div>
            <div class="metric-value">3</div>
            <div class="metric-sub">Need your review</div>
          </div>
        </div>
 
        <div class="grid-2">
          <div class="panel">
            <div class="panel-head">
              <div class="panel-title">Active subscriptions</div>
              <div class="panel-action" onclick="setPage('subscriptions')">View all</div>
            </div>
            <div class="tab-row">
              <div class="tab active" onclick="filterSubs(this,'all')">All</div>
              <div class="tab" onclick="filterSubs(this,'streaming')">Streaming</div>
              <div class="tab" onclick="filterSubs(this,'saas')">SaaS</div>
              <div class="tab" onclick="filterSubs(this,'other')">Other</div>
            </div>
            <div id="sub-list">
              <div class="sub-row" data-cat="streaming" onclick="showModal('sub-detail')">
                <div class="sub-logo" style="background:#fee2e2;color:#dc2626">NF</div>
                <div class="sub-info"><div class="sub-name">Netflix <span class="conf-pill conf-high">98%</span></div><div class="sub-meta">Monthly <span class="tag tag-amber">Price increase</span></div></div>
                <div class="sub-right"><div class="sub-amount">$15.49</div><div class="sub-next">Apr 8</div></div>
              </div>
              <div class="sub-row" data-cat="streaming" onclick="showModal('sub-detail')">
                <div class="sub-logo" style="background:#d1fae5;color:#059669">SP</div>
                <div class="sub-info"><div class="sub-name">Spotify <span class="conf-pill conf-high">97%</span></div><div class="sub-meta">Monthly <span class="tag tag-red">Duplicate</span></div></div>
                <div class="sub-right"><div class="sub-amount">$9.99</div><div class="sub-next">Apr 5</div></div>
              </div>
              <div class="sub-row" data-cat="saas" onclick="showModal('sub-detail')">
                <div class="sub-logo" style="background:#dbeafe;color:#1d4ed8">GH</div>
                <div class="sub-info"><div class="sub-name">GitHub Pro <span class="conf-pill conf-high">99%</span></div><div class="sub-meta">Monthly</div></div>
                <div class="sub-right"><div class="sub-amount">$4.00</div><div class="sub-next">Apr 12</div></div>
              </div>
              <div class="sub-row" data-cat="saas" onclick="showModal('sub-detail')">
                <div class="sub-logo" style="background:#f3e8ff;color:#7c3aed">NT</div>
                <div class="sub-info"><div class="sub-name">Notion <span class="conf-pill conf-med">84%</span></div><div class="sub-meta">Monthly</div></div>
                <div class="sub-right"><div class="sub-amount">$16.00</div><div class="sub-next">Apr 18</div></div>
              </div>
              <div class="sub-row" data-cat="other" onclick="showModal('sub-detail')">
                <div class="sub-logo" style="background:#fef3c7;color:#d97706">AZ</div>
                <div class="sub-info"><div class="sub-name">Amazon Prime <span class="conf-pill conf-high">96%</span></div><div class="sub-meta">Yearly</div></div>
                <div class="sub-right"><div class="sub-amount">$139.00</div><div class="sub-next">Oct 3</div></div>
              </div>
            </div>
          </div>
 
          <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="panel">
              <div class="panel-head"><div class="panel-title">Spend by category</div></div>
              <div class="chart-area"><div style="position:relative;height:170px;"><canvas id="catChart"></canvas></div></div>
            </div>
            <div class="panel">
              <div class="panel-head">
                <div class="panel-title">Upcoming charges</div>
                <div class="panel-action" onclick="setPage('subscriptions')">Calendar</div>
              </div>
              <div class="upcoming-row" onclick="showModal('sub-detail')"><div class="upcoming-date due-soon">Apr 5</div><div class="upcoming-name">Spotify (x2 duplicate)</div><div class="upcoming-amt due-soon">$19.98</div></div>
              <div class="upcoming-row" onclick="showModal('sub-detail')"><div class="upcoming-date due-soon">Apr 8</div><div class="upcoming-name">Netflix</div><div class="upcoming-amt">$15.49</div></div>
              <div class="upcoming-row" onclick="showModal('sub-detail')"><div class="upcoming-date">Apr 12</div><div class="upcoming-name">GitHub Pro</div><div class="upcoming-amt">$4.00</div></div>
              <div class="upcoming-row" onclick="showModal('sub-detail')"><div class="upcoming-date">Apr 15</div><div class="upcoming-name">Adobe CC</div><div class="upcoming-amt">$54.99</div></div>
            </div>
          </div>
        </div>
 
        <div class="panel">
          <div class="panel-head"><div class="panel-title">Recent alerts</div><div class="panel-action" onclick="setPage('alerts')">View all</div></div>
          <div class="alert-list">
            <div class="alert-row" onclick="showModal('alert-detail')">
              <div class="alert-icon" style="background:#fee2e2"><svg width="14" height="14" viewBox="0 0 16 16" fill="#dc2626"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div>
              <div class="alert-body"><div class="alert-title">Netflix price increased: $12.99 to $15.49</div><div class="alert-desc">Charge on Mar 8 was $2.50 higher than previous month</div></div>
              <div class="alert-time">2d ago</div>
            </div>
            <div class="alert-row" onclick="showModal('alert-detail')">
              <div class="alert-icon" style="background:#fee2e2"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#dc2626" stroke-width="1.6"><circle cx="8" cy="8" r="5"/><path d="m5 5 6 6M11 5 5 11" stroke-linecap="round"/></svg></div>
              <div class="alert-body"><div class="alert-title">Duplicate Spotify charge detected</div><div class="alert-desc">2 charges of $9.99 found in same billing cycle (Apr 1 and Apr 2)</div></div>
              <div class="alert-time">1d ago</div>
            </div>
            <div class="alert-row" onclick="showModal('alert-detail')">
              <div class="alert-icon" style="background:#fef3c7"><svg width="14" height="14" viewBox="0 0 16 16" fill="#d97706"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div>
              <div class="alert-body"><div class="alert-title">Upcoming charge in 3 days: Spotify $9.99</div><div class="alert-desc">Expected on Apr 5 based on recurring pattern</div></div>
              <div class="alert-time">Today</div>
            </div>
          </div>
        </div>
      </div>
    </div>
 
    <!-- DETECTED -->
    <div id="page-detected" style="display:none;">
      <div class="topbar"><div class="page-title">Unconfirmed detections</div></div>
      <div class="content">
        <p style="font-size:13px;color:#6b7280;margin-bottom:16px;">The detection engine found these recurring patterns. Confirm or dismiss each one to improve accuracy.</p>
        <div class="panel">
          <div class="sub-row" style="flex-direction:column;align-items:stretch;gap:10px;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div class="sub-logo" style="background:#f0fdf4;color:#16a34a">CV</div>
              <div style="flex:1"><div style="font-size:13px;font-weight:500;color:#111827">Canva Pro</div><div style="font-size:11px;color:#6b7280;margin-top:2px">Monthly $12.99 - Next Apr 10 - Matched: CANVA PTY LTD, CANVA.COM</div></div>
              <div style="display:flex;gap:6px;">
                <button class="btn" style="font-size:11px" onclick="dismissDetection(this)">Dismiss</button>
                <button class="btn primary" style="font-size:11px" onclick="confirmDetection(this)">Confirm</button>
              </div>
            </div>
            <div style="background:#f9fafb;border-radius:6px;padding:8px 12px;font-size:11px;color:#6b7280">Confidence: <strong style="color:#16a34a">91%</strong> - 4 matching transactions - Monthly cadence confirmed</div>
          </div>
          <div class="sub-row" style="flex-direction:column;align-items:stretch;gap:10px;border-top:1px solid #f3f4f6;">
            <div style="display:flex;align-items:center;gap:12px;">
              <div class="sub-logo" style="background:#eff6ff;color:#2563eb">ZM</div>
              <div style="flex:1"><div style="font-size:13px;font-weight:500;color:#111827">Zoom Pro</div><div style="font-size:11px;color:#6b7280;margin-top:2px">Monthly $14.99 - Next Apr 22 - Matched: ZOOM.US, ZOOM VIDEO COM</div></div>
              <div style="display:flex;gap:6px;">
                <button class="btn" style="font-size:11px" onclick="dismissDetection(this)">Dismiss</button>
                <button class="btn primary" style="font-size:11px" onclick="confirmDetection(this)">Confirm</button>
              </div>
            </div>
            <div style="background:#f9fafb;border-radius:6px;padding:8px 12px;font-size:11px;color:#6b7280">Confidence: <strong style="color:#d97706">74%</strong> - 3 matching transactions - Monthly cadence likely</div>
          </div>
        </div>
      </div>
    </div>
 
    <!-- ALERTS -->
    <div id="page-alerts" style="display:none;">
      <div class="topbar"><div class="page-title">Alerts</div><button class="btn" onclick="markAllRead()">Mark all read</button></div>
      <div class="content">
        <div class="panel">
          <div class="alert-row"><div class="alert-icon" style="background:#fee2e2"><svg width="14" height="14" viewBox="0 0 16 16" fill="#dc2626"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div><div class="alert-body"><div class="alert-title">Netflix price increased: $12.99 to $15.49</div><div class="alert-desc">+$2.50/mo on Mar 8</div></div><div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px"><span class="tag tag-red">Price increase</span><span class="alert-time">Mar 8</span></div></div>
          <div class="alert-row"><div class="alert-icon" style="background:#fee2e2"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#dc2626" stroke-width="1.6"><circle cx="8" cy="8" r="5"/><path d="m5 5 6 6M11 5 5 11" stroke-linecap="round"/></svg></div><div class="alert-body"><div class="alert-title">Duplicate Spotify charge</div><div class="alert-desc">2 x $9.99 on Apr 1 and Apr 2 - same billing cycle</div></div><div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px"><span class="tag tag-red">Duplicate</span><span class="alert-time">Apr 2</span></div></div>
          <div class="alert-row"><div class="alert-icon" style="background:#fef3c7"><svg width="14" height="14" viewBox="0 0 16 16" fill="#d97706"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div><div class="alert-body"><div class="alert-title">Upcoming charge in 3 days: Spotify</div><div class="alert-desc">Expected $9.99 on Apr 5</div></div><div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px"><span class="tag tag-amber">Upcoming</span><span class="alert-time">Today</span></div></div>
          <div class="alert-row"><div class="alert-icon" style="background:#fef3c7"><svg width="14" height="14" viewBox="0 0 16 16" fill="#d97706"><path d="M8 2l6 12H2L8 2zm0 4v3M8 10v1.5"/></svg></div><div class="alert-body"><div class="alert-title">Adobe CC price increased: $49.99 to $54.99</div><div class="alert-desc">+$5.00/mo detected on Mar 15</div></div><div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px"><span class="tag tag-amber">Price increase</span><span class="alert-time">Mar 15</span></div></div>
          <div class="alert-row"><div class="alert-icon" style="background:#d1fae5"><svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="#059669" stroke-width="1.6"><circle cx="8" cy="8" r="5"/><path d="m5.5 8 2 2 3-3" stroke-linecap="round"/></svg></div><div class="alert-body"><div class="alert-title">New subscription detected: Canva Pro</div><div class="alert-desc">$12.99/mo - Confirm to start tracking</div></div><div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px"><span class="tag tag-green">New</span><span class="alert-time">Apr 1</span></div></div>
        </div>
      </div>
    </div>
 
    <!-- REPORTS -->
    <div id="page-reports" style="display:none;">
      <div class="topbar"><div class="page-title">Monthly reports</div><button class="btn primary"><svg width="13" height="13" viewBox="0 0 16 16" fill="none" stroke="white" stroke-width="1.5" stroke-linecap="round"><path d="M8 10V3M5 7l3 3 3-3M3 13h10"/></svg> Export PDF</button></div>
      <div class="content">
        <div class="metrics-row" style="grid-template-columns:repeat(3,minmax(0,1fr))">
          <div class="metric-card"><div class="metric-label">March total spend</div><div class="metric-value">$284.50</div><div class="metric-sub"><span class="badge-up">+6.9%</span> vs Feb</div></div>
          <div class="metric-card"><div class="metric-label">Avg per subscription</div><div class="metric-value">$11.85</div><div class="metric-sub">24 active</div></div>
          <div class="metric-card"><div class="metric-label">Annual projection</div><div class="metric-value">$3,414</div><div class="metric-sub">at current rate</div></div>
        </div>
        <div class="panel" style="margin-bottom:16px;">
          <div class="panel-head"><div class="panel-title">Spend trend - last 6 months</div></div>
          <div class="chart-area"><div style="position:relative;height:200px;"><canvas id="trendChart"></canvas></div></div>
        </div>
        <div class="panel">
          <div class="panel-head"><div class="panel-title">Previous reports</div></div>
          <div style="padding:0 16px">
            <div class="upcoming-row"><div style="font-size:12px;color:#111827;flex:1">March 2025 report</div><div style="font-size:11px;color:#1a56db;cursor:pointer">Download PDF</div></div>
            <div class="upcoming-row"><div style="font-size:12px;color:#111827;flex:1">February 2025 report</div><div style="font-size:11px;color:#1a56db;cursor:pointer">Download PDF</div></div>
            <div class="upcoming-row"><div style="font-size:12px;color:#111827;flex:1">January 2025 report</div><div style="font-size:11px;color:#1a56db;cursor:pointer">Download PDF</div></div>
          </div>
        </div>
      </div>
    </div>
 
    <!-- IMPORT -->
    <div id="page-import" style="display:none;">
      <div class="topbar"><div class="page-title">Import CSV</div></div>
      <div class="content">
        <div class="panel" style="max-width:480px;">
          <div class="panel-head"><div class="panel-title">Upload bank export</div></div>
          <div style="padding:24px;text-align:center;border:2px dashed #d1d5db;margin:16px;border-radius:8px;cursor:pointer;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
            <div style="font-size:32px;margin-bottom:8px">&#128193;</div>
            <div style="font-size:13px;font-weight:500;color:#111827">Drop your CSV file here</div>
            <div style="font-size:11px;color:#6b7280;margin-top:4px">or click to browse - Chase, BofA, Wells Fargo</div>
          </div>
          <div style="padding:0 16px 16px"><button class="btn primary" style="width:100%;justify-content:center">Analyze transactions</button></div>
        </div>
      </div>
    </div>
 
    <!-- TRANSACTIONS -->
    <div id="page-transactions" style="display:none;">
      <div class="topbar"><div class="page-title">Transactions</div></div>
      <div class="content">
        <div class="panel">
          <div class="panel-head"><div class="panel-title">All transactions</div><select style="font-size:12px;padding:5px 8px;border-radius:6px;border:1px solid #d1d5db;background:white;color:#374151"><option>All categories</option><option>Streaming</option><option>SaaS</option><option>Other</option></select></div>
          <div class="sub-row"><div class="sub-logo" style="background:#fee2e2;color:#dc2626">NF</div><div class="sub-info"><div class="sub-name">Netflix</div><div class="sub-meta">Mar 8 - NETFLIX.COM <span class="tag tag-amber">Higher than usual</span></div></div><div class="sub-right"><div class="sub-amount" style="color:#dc2626">-$15.49</div></div></div>
          <div class="sub-row"><div class="sub-logo" style="background:#d1fae5;color:#059669">SP</div><div class="sub-info"><div class="sub-name">Spotify</div><div class="sub-meta">Apr 2 - SPOTIFY AB <span class="tag tag-red">Duplicate</span></div></div><div class="sub-right"><div class="sub-amount" style="color:#dc2626">-$9.99</div></div></div>
          <div class="sub-row"><div class="sub-logo" style="background:#d1fae5;color:#059669">SP</div><div class="sub-info"><div class="sub-name">Spotify</div><div class="sub-meta">Apr 1 - Spotify 1234</div></div><div class="sub-right"><div class="sub-amount" style="color:#dc2626">-$9.99</div></div></div>
          <div class="sub-row"><div class="sub-logo" style="background:#dbeafe;color:#1d4ed8">GH</div><div class="sub-info"><div class="sub-name">GitHub Pro</div><div class="sub-meta">Mar 12 - GITHUB.COM</div></div><div class="sub-right"><div class="sub-amount" style="color:#dc2626">-$4.00</div></div></div>
        </div>
      </div>
    </div>
 
    <!-- SUBSCRIPTIONS -->
    <div id="page-subscriptions" style="display:none;">
      <div class="topbar"><div class="page-title">All subscriptions</div><button class="btn primary" onclick="showModal('add-modal')">+ Add</button></div>
      <div class="content">
        <div class="panel">
          <div style="padding:12px 16px;display:flex;gap:8px;flex-wrap:wrap">
            <span class="tag tag-blue" style="cursor:pointer">All 24</span>
            <span class="tag tag-gray" style="cursor:pointer">Streaming 5</span>
            <span class="tag tag-gray" style="cursor:pointer">SaaS 8</span>
            <span class="tag tag-gray" style="cursor:pointer">Utilities 3</span>
            <span class="tag tag-gray" style="cursor:pointer">Other 8</span>
          </div>
          <div class="sub-row" onclick="showModal('sub-detail')"><div class="sub-logo" style="background:#fee2e2;color:#dc2626">NF</div><div class="sub-info"><div class="sub-name">Netflix</div><div class="sub-meta">Monthly Streaming <span class="tag tag-amber">Price up</span></div></div><div class="sub-right"><div class="sub-amount">$15.49</div><div class="sub-next">Apr 8</div></div></div>
          <div class="sub-row" onclick="showModal('sub-detail')"><div class="sub-logo" style="background:#d1fae5;color:#059669">SP</div><div class="sub-info"><div class="sub-name">Spotify</div><div class="sub-meta">Monthly Streaming <span class="tag tag-red">Duplicate</span></div></div><div class="sub-right"><div class="sub-amount">$9.99</div><div class="sub-next">Apr 5</div></div></div>
          <div class="sub-row" onclick="showModal('sub-detail')"><div class="sub-logo" style="background:#f3e8ff;color:#7c3aed">AD</div><div class="sub-info"><div class="sub-name">Adobe CC</div><div class="sub-meta">Monthly SaaS <span class="tag tag-amber">Price up</span></div></div><div class="sub-right"><div class="sub-amount">$54.99</div><div class="sub-next">Apr 15</div></div></div>
          <div class="sub-row" onclick="showModal('sub-detail')"><div class="sub-logo" style="background:#dbeafe;color:#1d4ed8">GH</div><div class="sub-info"><div class="sub-name">GitHub Pro</div><div class="sub-meta">Monthly SaaS</div></div><div class="sub-right"><div class="sub-amount">$4.00</div><div class="sub-next">Apr 12</div></div></div>
          <div class="sub-row" onclick="showModal('sub-detail')"><div class="sub-logo" style="background:#f3e8ff;color:#7c3aed">NT</div><div class="sub-info"><div class="sub-name">Notion</div><div class="sub-meta">Monthly SaaS</div></div><div class="sub-right"><div class="sub-amount">$16.00</div><div class="sub-next">Apr 18</div></div></div>
          <div class="sub-row" onclick="showModal('sub-detail')"><div class="sub-logo" style="background:#fef3c7;color:#d97706">AZ</div><div class="sub-info"><div class="sub-name">Amazon Prime</div><div class="sub-meta">Yearly Other</div></div><div class="sub-right"><div class="sub-amount">$139.00</div><div class="sub-next">Oct 3</div></div></div>
        </div>
      </div>
    </div>
 
  </div>
</div>
 
<!-- MODALS -->
<div id="modal-overlay" onclick="if(event.target===this)closeModal()">
  <div id="sub-detail" class="modal-box" style="display:none;background:white;border-radius:12px;width:360px;max-width:95vw;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <div style="background:#1a56db;padding:20px">
      <div style="display:flex;align-items:center;gap:12px">
        <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white">NF</div>
        <div><div style="font-size:15px;font-weight:600;color:white">Netflix</div><div style="font-size:11px;color:rgba(255,255,255,0.75)">Streaming - Monthly</div></div>
        <div style="margin-left:auto;font-size:22px;font-weight:600;color:white">$15.49</div>
      </div>
    </div>
    <div style="padding:16px">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
        <div style="background:#f9fafb;border-radius:6px;padding:10px"><div style="font-size:10px;color:#6b7280;margin-bottom:2px">Next charge</div><div style="font-size:13px;font-weight:600;color:#111827">Apr 8, 2025</div></div>
        <div style="background:#f9fafb;border-radius:6px;padding:10px"><div style="font-size:10px;color:#6b7280;margin-bottom:2px">Confidence</div><div style="font-size:13px;font-weight:600;color:#059669">98% high</div></div>
      </div>
      <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:10px;margin-bottom:12px">
        <div style="font-size:11px;font-weight:600;color:#dc2626">Price increase detected</div>
        <div style="font-size:11px;color:#991b1b;margin-top:2px">$12.99 to $15.49 (+$2.50/mo = +$30/year)</div>
      </div>
      <div style="display:flex;gap:8px">
        <button class="btn" style="flex:1;justify-content:center" onclick="closeModal()">Cancel sub</button>
        <button class="btn primary" style="flex:1;justify-content:center" onclick="closeModal()">Done</button>
      </div>
    </div>
  </div>
 
  <div id="alert-detail" class="modal-box" style="display:none;background:white;border-radius:12px;padding:20px;width:360px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <div style="font-size:15px;font-weight:600;color:#111827;margin-bottom:8px">Netflix price increase</div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:16px">Charge on Mar 8 was $15.49, up from $12.99 the previous month.</div>
    <div style="background:#f9fafb;border-radius:6px;padding:12px;margin-bottom:16px;font-size:12px">
      <div style="display:flex;justify-content:space-between;margin-bottom:4px"><span style="color:#6b7280">Previous price</span><span>$12.99/mo</span></div>
      <div style="display:flex;justify-content:space-between;margin-bottom:4px"><span style="color:#6b7280">New price</span><span style="color:#dc2626;font-weight:600">$15.49/mo</span></div>
      <div style="display:flex;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:4px;margin-top:4px"><span style="color:#6b7280">Annual impact</span><span style="font-weight:600">+$30.00/yr</span></div>
    </div>
    <div style="display:flex;gap:8px">
      <button class="btn" style="flex:1;justify-content:center" onclick="closeModal()">Dismiss</button>
      <button class="btn primary" style="flex:1;justify-content:center" onclick="closeModal()">Mark resolved</button>
    </div>
  </div>
 
  <div id="add-modal" class="modal-box" style="display:none;background:white;border-radius:12px;padding:20px;width:360px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <div style="font-size:15px;font-weight:600;margin-bottom:16px">Add subscription</div>
    <div style="margin-bottom:12px"><label style="font-size:11px;color:#6b7280;display:block;margin-bottom:4px">Service name</label><input type="text" placeholder="e.g. Netflix" style="width:100%;padding:8px 10px;border-radius:6px;border:1px solid #d1d5db;font-size:13px;outline:none"/></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:16px">
      <div><label style="font-size:11px;color:#6b7280;display:block;margin-bottom:4px">Amount</label><input type="text" placeholder="$0.00" style="width:100%;padding:8px 10px;border-radius:6px;border:1px solid #d1d5db;font-size:13px;outline:none"/></div>
      <div><label style="font-size:11px;color:#6b7280;display:block;margin-bottom:4px">Cadence</label><select style="width:100%;padding:8px 10px;border-radius:6px;border:1px solid #d1d5db;font-size:13px"><option>Monthly</option><option>Yearly</option><option>Weekly</option></select></div>
    </div>
    <div style="display:flex;gap:8px">
      <button class="btn" style="flex:1;justify-content:center" onclick="closeModal()">Cancel</button>
      <button class="btn primary" style="flex:1;justify-content:center" onclick="closeModal()">Add</button>
    </div>
  </div>
 
  <div id="import-modal" class="modal-box" style="display:none;background:white;border-radius:12px;padding:20px;width:380px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
    <div style="font-size:15px;font-weight:600;margin-bottom:8px">Import bank CSV</div>
    <div style="font-size:12px;color:#6b7280;margin-bottom:16px">Upload your bank transaction export. We will detect recurring patterns automatically.</div>
    <div style="border:2px dashed #d1d5db;border-radius:8px;padding:24px;text-align:center;margin-bottom:16px;cursor:pointer" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
      <div style="font-size:28px;margin-bottom:6px">&#128193;</div>
      <div style="font-size:13px;font-weight:500;color:#111827">Drop CSV here or click to browse</div>
      <div style="font-size:11px;color:#6b7280;margin-top:4px">Chase - BofA - Wells Fargo - HSBC</div>
    </div>
    <div style="display:flex;gap:8px">
      <button class="btn" style="flex:1;justify-content:center" onclick="closeModal()">Cancel</button>
      <button class="btn primary" style="flex:1;justify-content:center">Analyze</button>
    </div>
  </div>
</div>
 
<script>
var pages = ['dashboard','detected','alerts','reports','import','transactions','subscriptions'];
 
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
  if (name === 'reports') setTimeout(initTrend, 50);
}
 
function showModal(id) {
  var overlay = document.getElementById('modal-overlay');
  overlay.style.display = 'flex';
  var boxes = document.querySelectorAll('.modal-box');
  for (var i = 0; i < boxes.length; i++) boxes[i].style.display = 'none';
  var box = document.getElementById(id);
  if (box) box.style.display = 'block';
}
 
function closeModal() {
  document.getElementById('modal-overlay').style.display = 'none';
}
 
function filterSubs(tab, cat) {
  var tabs = document.querySelectorAll('.tab');
  for (var i = 0; i < tabs.length; i++) tabs[i].classList.remove('active');
  tab.classList.add('active');
  var rows = document.querySelectorAll('#sub-list .sub-row[data-cat]');
  for (var j = 0; j < rows.length; j++) {
    rows[j].style.display = (cat === 'all' || rows[j].dataset.cat === cat) ? '' : 'none';
  }
}
 
function confirmDetection(btn) {
  btn.closest('.sub-row').style.opacity = '0.4';
  btn.textContent = 'Confirmed';
  btn.disabled = true;
}
 
function dismissDetection(btn) {
  btn.closest('.sub-row').style.opacity = '0.4';
  btn.textContent = 'Dismissed';
  btn.disabled = true;
}
 
function markAllRead() {
  var rows = document.querySelectorAll('.alert-row');
  for (var i = 0; i < rows.length; i++) rows[i].style.opacity = '0.5';
}
 
new Chart(document.getElementById('catChart'), {
  type: 'doughnut',
  data: {
    labels: ['Streaming','SaaS','Utilities','Other'],
    datasets: [{ data: [45,98,32,109], backgroundColor: ['#1a56db','#059669','#d97706','#7c3aed'], borderWidth: 2, borderColor: '#fff' }]
  },
  options: { responsive: true, maintainAspectRatio: false, cutout: '68%', plugins: { legend: { display: false } } }
});
 
function initTrend() {
  var canvas = document.getElementById('trendChart');
  if (!canvas || canvas._done) return;
  canvas._done = true;
  new Chart(canvas, {
    type: 'line',
    data: {
      labels: ['Oct','Nov','Dec','Jan','Feb','Mar'],
      datasets: [{ data: [241,248,261,255,266,284], borderColor: '#1a56db', backgroundColor: 'rgba(26,86,219,0.07)', tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: '#1a56db' }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: function(v) { return '$'+v; }, font: { size: 11 } }, grid: { color: '#f3f4f6' } }, x: { ticks: { font: { size: 11 } }, grid: { display: false } } } }
  });
}
</script>
</body>
</html>
 