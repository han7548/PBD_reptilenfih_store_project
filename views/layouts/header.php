<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reptile & Fish Store POS</title>
    <style>
        :root {
            --olive-leaf: #606C38;
            --black-forest: #283618;
            --cornsilk: #FEFAE0;
            --earth: #DDA15E;
            --bg-light: #f4f6f3;
            --white: #ffffff;
            --danger: #d9534f;
            --success: #28a745;
        }
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; display: flex; min-height: 100vh; background-color: var(--bg-light); color: #333; }
        
        /* Modern Sidebar */
        .sidebar { width: 260px; background-color: var(--black-forest); color: var(--cornsilk); display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; font-size: 1.5rem; font-weight: bold; border-bottom: 1px solid rgba(254, 250, 224, 0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { padding: 15px 0; flex: 1; display: flex; flex-direction: column; gap: 5px; }
        .sidebar-menu a { padding: 12px 25px; color: var(--cornsilk); text-decoration: none; display: block; font-weight: 500; transition: all 0.2s; }
        .sidebar-menu a:hover { background-color: var(--olive-leaf); padding-left: 30px; }
        .sidebar-footer { padding: 20px; border-top: 1px solid rgba(254, 250, 224, 0.1); font-size: 0.9rem; }
        
        /* Main Content */
        .main-content { flex: 1; display: flex; flex-direction: column; max-height: 100vh; overflow-y: auto; }
        .topbar { background: var(--white); padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
        .user-info { font-weight: bold; color: var(--olive-leaf); }
        .btn-logout { background: var(--danger); color: white; text-decoration: none; padding: 6px 15px; border-radius: 4px; font-weight: bold; }
        
        .content-wrapper { padding: 30px; }
        
        /* Global Components */
        .card { background: var(--white); border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); padding: 25px; margin-bottom: 25px; }
        .card-header { font-size: 1.25rem; font-weight: bold; color: var(--black-forest); margin-bottom: 15px; border-bottom: 2px solid var(--bg-light); padding-bottom: 10px; }
        
        .table-responsive { overflow-x: auto; border-radius: 8px; border: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; background: var(--white); }
        th { background-color: var(--olive-leaf); color: white; padding: 12px 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        tr:hover td { background-color: #fcfcfc; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #555; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; transition: border 0.2s; }
        input:focus, select:focus { border-color: var(--olive-leaf); outline: none; }
        
        .btn { border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.9; }
        .btn-primary { background-color: var(--olive-leaf); color: white; }
        .btn-success { background-color: var(--success); color: white; }
        .btn-danger { background-color: var(--danger); color: white; }
        
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"> Gwendeng POS</div>
    <div class="sidebar-menu">
        <?php $role = $_SESSION['role'] ?? ''; ?>
        
        <?php if ($role === 'Admin'): ?>
            <a href="index.php?page=admin_dashboard"> Dashboard</a>
            <a href="index.php?page=master_barang"> Master Barang</a>
            <a href="index.php?page=master_user"> Master User</a>
            <a href="index.php?page=kartu_stok"> Kartu Stok Log</a>
        <?php endif; ?>

        <?php if (in_array($role, ['Admin', 'Manager'])): ?>
            <a href="index.php?page=pengadaan"> Pengadaan (Order)</a>
            <a href="index.php?page=penerimaan"> Penerimaan (Receive)</a>
        <?php endif; ?>

        <?php if (in_array($role, ['Admin', 'Manager', 'Kasir'])): ?>
            <a href="index.php?page=kasir_dashboard"> POS Penjualan</a>
            <a href="index.php?page=laporan"> Laporan Penjualan</a>
        <?php endif; ?>
    </div>
    <div class="sidebar-footer">
        Logged in as: <br><strong style="color: var(--earth);"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="user-info">Role: <?= htmlspecialchars($role) ?></div>
        <a href="index.php?action=logout" class="btn-logout">Logout</a>
    </div>
    <div class="content-wrapper">