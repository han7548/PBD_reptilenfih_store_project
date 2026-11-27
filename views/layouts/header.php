<?php
$role = $_SESSION['role'] ?? '';
$currentPage = $_GET['page'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS  - Reptile &amp; Fish Store</title>
    <style>
        :root {
            --olive-leaf: #606C38;
            --black-forest: #283618;
            --cornsilk: #FEFAE0;
            --earth: #DDA15E;
            --bg-light: #f4f6f3;
            --white: #ffffff;
            --border: #e2e6df;
            --text-main: #22281c;
            --text-muted: #5f6659;
            --danger: #b83227;
            --success: #2e6f40;
            --warning: #9a6700;
        }

        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-light);
            color: var(--text-main);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            flex-shrink: 0;
            background-color: var(--black-forest);
            color: var(--cornsilk);
            display: flex;
            flex-direction: column;
            transition: transform 0.25s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            font-size: 1.35rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(254, 250, 224, 0.12);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-menu {
            padding: 12px 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-menu a {
            padding: 12px 22px;
            color: var(--cornsilk);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: background-color 0.2s, padding-left 0.2s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: var(--olive-leaf);
            padding-left: 26px;
        }

        .sidebar-footer {
            padding: 18px 20px;
            border-top: 1px solid rgba(254, 250, 224, 0.12);
            font-size: 0.88rem;
            color: rgba(254, 250, 224, 0.85);
        }

        /* Main Area */
        .main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--white);
            padding: 14px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .menu-toggle {
            display: none;
            background: var(--olive-leaf);
            color: var(--white);
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
        }

        .sidebar-close {
            display: none;
            background: transparent;
            color: var(--cornsilk);
            border: 1px solid rgba(254, 250, 224, 0.25);
            border-radius: 5px;
            padding: 4px 9px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 950;
        }

        .user-info {
            font-weight: 600;
            color: var(--black-forest);
            font-size: 0.92rem;
        }

        .btn-logout {
            background: var(--danger);
            color: var(--white);
            text-decoration: none;
            padding: 7px 14px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.88rem;
            white-space: nowrap;
        }

        .content-wrapper {
            padding: 24px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Cards, Grids, Tables, Forms */
        .card {
            background: var(--white);
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            padding: 20px;
            margin-bottom: 22px;
        }

        .card-header {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--black-forest);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        input, select {
            width: 100%;
            padding: 9px 11px;
            border: 1px solid #cbd2c7;
            border-radius: 6px;
            font-size: 0.95rem;
            background: var(--white);
        }

        input[readonly] {
            background: #f0f2ee;
            color: var(--text-muted);
        }

        input:focus, select:focus {
            border-color: var(--olive-leaf);
            outline: none;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 6px;
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            min-width: 620px;
        }

        th {
            background-color: var(--olive-leaf);
            color: var(--white);
            padding: 11px 14px;
            text-align: left;
            font-size: 0.88rem;
            white-space: nowrap;
        }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid #edf0eb;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        tr:hover td {
            background-color: #fafbf8;
        }

        .btn {
            border: none;
            padding: 9px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8rem;
        }

        .btn-primary { background-color: var(--olive-leaf); color: var(--white); }
        .btn-success { background-color: var(--success); color: var(--white); }
        .btn-danger { background-color: var(--danger); color: var(--white); }
        .btn-warning { background-color: var(--earth); color: var(--black-forest); }

        .status-text { font-weight: 600; }
        .status-active { color: var(--success); }
        .status-inactive { color: var(--danger); }
        .status-pending { color: var(--warning); }
        .status-partial { color: #1f5f8b; }
        .status-finished { color: var(--success); }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 0.92rem;
        }

        .alert-success { background: #e6f4ea; color: #1e4620; border: 1px solid #b7dfc2; }
        .alert-danger { background: #fce8e6; color: #7c1d15; border: 1px solid #f4c7c3; }

        /* Mobile / Tablet Responsiveness */
        @media (max-width: 900px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle,
            .sidebar-close {
                display: inline-block;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-backdrop.open {
                display: block;
            }

            .topbar {
                padding: 12px 16px;
            }

            .content-wrapper {
                padding: 14px;
            }

            .card {
                padding: 15px;
            }

            table {
                min-width: 520px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar(false)"></div>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span> POS</span>
        <button type="button" class="sidebar-close" onclick="toggleSidebar(false)">&times;</button>
    </div>
    <nav class="sidebar-menu">
        <?php if ($role === 'Admin'): ?>
            <a href="index.php?page=admin_dashboard" class="<?= $currentPage === 'admin_dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="index.php?page=master_barang" class="<?= $currentPage === 'master_barang' ? 'active' : '' ?>">Master Barang</a>
            <a href="index.php?page=master_vendor" class="<?= $currentPage === 'master_vendor' ? 'active' : '' ?>">Master Vendor</a>
            <a href="index.php?page=master_satuan" class="<?= $currentPage === 'master_satuan' ? 'active' : '' ?>">Master Satuan</a>
            <a href="index.php?page=master_role" class="<?= $currentPage === 'master_role' ? 'active' : '' ?>">Master Role</a>
            <a href="index.php?page=master_user" class="<?= $currentPage === 'master_user' ? 'active' : '' ?>">Master User</a>
            <a href="index.php?page=margin_settings" class="<?= $currentPage === 'margin_settings' ? 'active' : '' ?>">Margin Settings</a>
        <?php endif; ?>

        <?php if (in_array($role, ['Admin', 'Manager'])): ?>
            <a href="index.php?page=pengadaan" class="<?= in_array($currentPage, ['pengadaan', 'penerimaan', 'manager_dashboard']) ? 'active' : '' ?>">Pengadaan &amp; Penerimaan</a>
        <?php endif; ?>

        <?php if (in_array($role, ['Admin', 'Manager', 'Kasir'])): ?>
            <a href="index.php?page=kasir_dashboard" class="<?= $currentPage === 'kasir_dashboard' ? 'active' : '' ?>">POS Penjualan</a>
            <a href="index.php?page=riwayat_penjualan" class="<?= $currentPage === 'riwayat_penjualan' ? 'active' : '' ?>">Daftar Penjualan</a>
            <a href="index.php?page=kartu_stok" class="<?= $currentPage === 'kartu_stok' ? 'active' : '' ?>">Kartu Stok Log</a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        User: <strong><?= htmlspecialchars($_SESSION['username'] ?? '-') ?></strong><br>
        Role: <strong><?= htmlspecialchars($role ?: '-') ?></strong>
    </div>
</aside>

<div class="main-content">
    <header class="topbar">
        <div class="topbar-left">
            <button type="button" class="menu-toggle" onclick="toggleSidebar(true)">&#9776;</button>
            <span class="user-info">Reptile &amp; Fish Store</span>
        </div>
        <a href="index.php?action=logout" class="btn-logout">Logout</a>
    </header>
    <main class="content-wrapper">

<script>
function toggleSidebar(open) {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (open) {
        sidebar.classList.add('open');
        backdrop.classList.add('open');
    } else {
        sidebar.classList.remove('open');
        backdrop.classList.remove('open');
    }
}
</script>