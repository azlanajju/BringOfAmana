<?php
/**
 * Bright of Amana – Admin layout header + sidebar
 * Theme: Green accent, Donezo-style dashboard.
 */
$page = $page ?? 'dashboard';
$title = $title ?? 'Admin';
$adminName = htmlspecialchars($_SESSION['user_name'] ?? 'Admin');
$adminEmail = htmlspecialchars($_SESSION['user_email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> – Bright of Amana</title>
  <style>
    :root {
      --green: #268e45;
      --green-dark: #1e6f38;
      --green-light: #d4edda;
      --orange: #ffb300;
      --orange-bg: #fff8e6;
      --red: #ff4444;
      --red-bg: #fff5f5;
      --sidebar: #fff;
      --text: #1a202c;
      --text-muted: #718096;
      --bg: #edf2f7;
      --radius: 12px;
      --radius-lg: 16px;
      --shadow: 0 2px 8px rgba(0,0,0,.06);
      --shadow-lg: 0 4px 16px rgba(0,0,0,.08);
    }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; color: var(--text); background: var(--bg); }
    .layout { display: flex; min-height: 100vh; }
    .sidebar {
      width: 260px;
      height: 100vh;
      max-height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background: var(--sidebar);
      color: var(--text);
      display: flex;
      flex-direction: column;
      box-shadow: 1px 0 0 0 #e2e8f0;
      overflow-y: auto;
    }
    .sidebar-brand {
      padding: 1.5rem;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .sidebar-logo {
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .sidebar-logo img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
    .sidebar-brand-text h1 { margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text); }
    .sidebar-brand-text p { margin: 0.15rem 0 0; font-size: 0.75rem; color: var(--text-muted); }
    .sidebar-section {
      padding: 1rem 0 0.5rem;
      padding-left: 1.25rem;
      padding-right: 1.25rem;
    }
    .sidebar-section-label {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-muted);
      margin-bottom: 0.5rem;
    }
    .sidebar-nav { padding: 0; }
    .sidebar-nav a {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      padding: 0.7rem 1.25rem;
      margin: 0 0.5rem;
      color: var(--text);
      text-decoration: none;
      border-radius: var(--radius);
      transition: all .2s;
      position: relative;
      border-left: 3px solid transparent;
    }
    .sidebar-nav a:hover { background: #f7fafc; color: var(--text); }
    .sidebar-nav a { touch-action: manipulation; }
    .sidebar-nav a.active {
      font-weight: 600;
      color: var(--green);
    }
    .sidebar-nav .nav-icon {
      width: 20px;
      height: 20px;
      opacity: .85;
    }
    .sidebar-nav a.active .nav-icon { opacity: 1; }
    .sidebar-general { margin-top: auto; border-top: 1px solid #e2e8f0; padding-top: 1rem; }
    .sidebar-user {
      padding: 1rem 1.25rem;
      font-size: 0.875rem;
      color: var(--text);
    }
    .sidebar-user span { opacity: .9; }
    .sidebar-user a { color: var(--green); text-decoration: none; }
    .sidebar-user a:hover { text-decoration: underline; }
    .main { flex: 1; display: flex; flex-direction: column; overflow-x: auto; margin-left: 260px; min-width: 0; }
    .main-top {
      background: #fff;
      padding: 1rem 1.5rem 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
      box-shadow: var(--shadow);
    }
    .main-top-search {
      flex: 1;
      min-width: 200px;
      max-width: 320px;
      padding: 0.5rem 1rem 0.5rem 2.5rem;
      font-size: 0.9rem;
      border: 1px solid #e2e8f0;
      border-radius: var(--radius);
      background: #f7fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23718096' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 0.75rem center;
    }
    .main-top-search:focus { outline: none; border-color: var(--green); box-shadow: 0 0 0 2px rgba(38,142,69,.15); }
    .main-top-user {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    .main-top-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--green);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1rem;
    }
    .main-top-user-info { text-align: right; }
    .main-top-user-name { font-weight: 600; color: var(--text); font-size: 0.9rem; }
    .main-top-user-email { font-size: 0.75rem; color: var(--text-muted); }
    .main-content { padding: 1.5rem 2rem; flex: 1; }
    .page-header { 
      margin-bottom: 1.5rem; 
      display: flex; 
      justify-content: space-between; 
      align-items: flex-start; 
      gap: 1rem; 
      flex-wrap: wrap; 
    }
    .page-title { margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text); }
    .page-subtitle { margin: 0.35rem 0 0; font-size: 0.9rem; color: var(--text-muted); }
    .card {
      background: #fff;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }
    .card h2 { margin: 0 0 1rem; font-size: 1.1rem; font-weight: 600; color: var(--text); }
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1.25rem;
      margin-bottom: 1.5rem;
    }
    .stat-card {
      background: #fff;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 1.25rem;
      position: relative;
      overflow: hidden;
    }
    .stat-card.highlight {
      background: var(--green);
      color: #fff;
      box-shadow: var(--shadow-lg);
    }
    .stat-card.highlight .label { color: rgba(255,255,255,.85); }
    .stat-card.highlight .value { color: #fff; }
    .stat-card.highlight .stat-trend { color: rgba(255,255,255,.9); }
    .stat-card .label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 0.35rem; }
    .stat-card .value { font-size: 1.6rem; font-weight: 700; color: var(--text); }
    .stat-card .stat-trend { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.35rem; }
    .stat-card .stat-icon {
      position: absolute;
      top: 1rem;
      right: 1rem;
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--green-light);
      color: var(--green);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }
    .stat-card.highlight .stat-icon { background: rgba(255,255,255,.2); color: #fff; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 0.85rem 1rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
    th { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em; font-weight: 600; }
    tr:hover { background: #f7fafc; }
    .btn {
      display: inline-block;
      padding: 0.55rem 1.1rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #fff;
      background: var(--green);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      text-decoration: none;
      transition: all .2s;
    }
    .btn:hover { background: var(--green-dark); }
    .btn { touch-action: manipulation; }
    .btn-sm { padding: 0.4rem 0.85rem; font-size: 0.8rem; }
    .btn-success { background: var(--green); }
    .btn-success:hover { background: var(--green-dark); }
    .btn-danger { background: var(--red); }
    .btn-danger:hover { background: #e03a3a; }
    .btn-outline {
      background: transparent;
      color: var(--green);
      border: 1px solid var(--green);
    }
    .btn-outline:hover { background: var(--green-light); }
    .badge { display: inline-block; padding: 0.25rem 0.55rem; font-size: 0.7rem; border-radius: 6px; font-weight: 600; text-transform: capitalize; }
    .badge-pending { background: var(--orange-bg); color: #b77900; }
    .badge-approved { background: var(--green-light); color: var(--green); }
    .badge-rejected { background: var(--red-bg); color: var(--red); }
    .badge-active { background: var(--green-light); color: var(--green); }
    .badge-inactive { background: #e2e8f0; color: #4a5568; }
    form .form-group { margin-bottom: 1rem; }
    form label { display: block; font-size: 0.875rem; font-weight: 600; color: var(--text); margin-bottom: 0.35rem; }
    form input, form select, form textarea {
      width: 100%;
      max-width: 400px;
      padding: 0.55rem 0.85rem;
      font-size: 1rem;
      border: 1px solid #e2e8f0;
      border-radius: var(--radius);
    }
    form input:focus, form select:focus, form textarea:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 2px rgba(38,142,69,.15);
    }
    .error { padding: 0.85rem; font-size: 0.875rem; color: #c53030; background: #fff5f5; border-radius: var(--radius); margin-bottom: 1rem; }
    .success { padding: 0.85rem; font-size: 0.875rem; color: var(--green); background: var(--green-light); border-radius: var(--radius); margin-bottom: 1rem; }
    .toolbar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }
    .filters { display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; }
    .filters input, .filters select { 
      padding: 0.55rem 0.85rem; 
      font-size: 0.875rem; 
      border: 1px solid #e2e8f0; 
      border-radius: var(--radius); 
      background: #fff;
      color: var(--text);
      min-width: 140px;
    }
    .filters input:focus, .filters select:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 2px rgba(38,142,69,.1);
    }
    code { font-family: 'Courier New', monospace; }
    .form-actions { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
    .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem; }
    .chart-container { position: relative; height: 300px; }
    .chart-container--tall { height: 340px; }
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,.75); overflow: auto; }
    .modal.active { display: flex; align-items: center; justify-content: center; }
    .modal-content { background: #fff; border-radius: var(--radius-lg); box-shadow: 0 8px 32px rgba(0,0,0,.2); max-width: 90vw; max-height: 90vh; position: relative; display: flex; flex-direction: column; }
    .modal-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .modal-title { margin: 0; font-size: 1.1rem; font-weight: 600; color: var(--text); }
    .modal-close { background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: var(--radius); transition: all .2s; }
    .modal-close:hover { background: #f7fafc; color: var(--text); }
    .modal-body { padding: 1.5rem; overflow: auto; flex: 1; }
    .modal-image { max-width: 100%; max-height: 70vh; display: block; margin: 0 auto; border-radius: var(--radius); }
    .modal-pdf { width: 100%; height: 70vh; border: none; border-radius: var(--radius); }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -0.25rem; }
    .table-responsive table { min-width: 640px; }
    .sidebar-overlay { display: none; }

    .hamburger {
      display: none;
      width: 44px;
      height: 44px;
      padding: 0;
      border: none;
      background: none;
      cursor: pointer;
      color: var(--text);
      border-radius: var(--radius);
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      touch-action: manipulation;
    }
    .hamburger:hover { background: #f7fafc; color: var(--green); }
    .hamburger svg { width: 24px; height: 24px; }

    @media (max-width: 768px) {
      .main { margin-left: 0; }
      .main-top { padding: 0.75rem 1rem; }
      .hamburger { display: flex; }
      .main-top-search { display: none; }
      .sidebar-overlay {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.4);
        z-index: 998;
        opacity: 0;
        visibility: hidden;
        transition: opacity .2s, visibility .2s;
        pointer-events: none;
      }
      body.mobile-nav-open .sidebar-overlay {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
      }
      .sidebar {
        width: 280px;
        max-width: 85vw;
        z-index: 999;
        transform: translateX(-100%);
        transition: transform .25s ease;
        box-shadow: none;
      }
      body.mobile-nav-open .sidebar { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.15); }
      body.mobile-nav-open { overflow: hidden; }
      .main-content { padding: 1rem; }
      .stats { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
      .page-header { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
      .page-title { font-size: 1.35rem; }
      .card { padding: 1.25rem; }
      .card h2 { font-size: 1rem; }
      form input, form select, form textarea { max-width: 100%; }
      .filters { flex-direction: column; align-items: stretch; }
      .filters input, .filters select { min-width: 0; width: 100%; }
      th, td { padding: 0.65rem 0.75rem; font-size: 0.85rem; }
      .toolbar { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
      .btn, .btn-sm { min-height: 44px; padding: 0.6rem 1rem; touch-action: manipulation; }
      .charts-grid { grid-template-columns: 1fr; gap: 1rem; }
      .chart-container { height: 260px; }
      .chart-container--tall { height: 280px; }
      .modal-content { max-width: 95vw; max-height: 85vh; margin: 1rem; }
      .modal-body { padding: 1rem; }
      .modal-image { max-height: 60vh; }
      .modal-pdf { height: 60vh; }
    }
    @media (max-width: 480px) {
      .stats { grid-template-columns: 1fr; }
      .main-top-user-info { display: none; }
      .main-top-avatar { width: 36px; height: 36px; font-size: 0.9rem; }
      .sidebar { width: 100%; max-width: 100%; }
      .table-responsive table { min-width: 560px; }
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
  <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>
  <div class="layout">
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo"><img src="../../assets/BABG_Logo.png" alt="Bright of Amana"></div>
        <div class="sidebar-brand-text">
          <h1>Bright of Amana</h1>
          <p>Admin Panel</p>
        </div>
      </div>
      <div class="sidebar-section">
        <div class="sidebar-section-label">Menu</div>
        <nav class="sidebar-nav">
          <a href="index.php" class="<?= $page === 'dashboard' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Dashboard
          </a>
          <a href="users.php" class="<?= $page === 'users' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Admins & Users
          </a>
          <a href="investors.php" class="<?= $page === 'investors' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Investors
          </a>
          <a href="investments.php" class="<?= $page === 'investments' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Investments
          </a>
          <a href="all-investments.php" class="<?= $page === 'all-investments' ? 'active' : '' ?>">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
            All Investments
          </a>
        </nav>
      </div>
      <div class="sidebar-section sidebar-general">
        <div class="sidebar-section-label">General</div>
        <nav class="sidebar-nav">
          <a href="../../login/logout.php">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Log out
          </a>
        </nav>
      </div>
      <div class="sidebar-user">
        <span><?= $adminName ?></span>
      </div>
    </aside>
    <main class="main">
      <div class="main-top">
        <button type="button" class="hamburger" id="hamburgerBtn" aria-label="Toggle menu" aria-expanded="false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <input type="search" class="main-top-search" placeholder="Search..." aria-label="Search">
        <div class="main-top-user">
          <div class="main-top-avatar"><?= strtoupper(mb_substr($adminName, 0, 1)) ?></div>
          <div class="main-top-user-info">
            <div class="main-top-user-name"><?= $adminName ?></div>
            <div class="main-top-user-email"><?= $adminEmail ?: 'Admin' ?></div>
          </div>
        </div>
      </div>
      <div class="main-content">
