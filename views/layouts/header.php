<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$role = $_SESSION['role'] ?? '';
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reptile & Fish Shop - System</title>
    <style>
        :root {
            --cornsilk: #FEFAE0;     
            --olive-leaf: #606C38;   
            --black-forest: #283618; 
            --white: #FFFFFF;
        }
        
        body {
            background-color: var(--cornsilk);
            color: var(--black-forest);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        
        nav {
            background-color: var(--black-forest);
            color: var(--cornsilk);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--cornsilk);
        }

        .nav-links a {
            color: var(--cornsilk);
            text-decoration: none;
            margin-left: 10px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
            font-size: 0.9rem;
        }

        .nav-links a:hover {
            background-color: var(--olive-leaf);
        }

        .nav-user {
            margin-left: 20px;
            color: var(--olive-leaf);
            font-weight: bold;
        }

        .logout-btn {
            background-color: #d9534f !important; 
        }
        
        .logout-btn:hover {
            background-color: #c9302c !important;
        }

        
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        
        .card-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .card {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-top: 4px solid var(--olive-leaf);
            width: calc(33.333% - 20px);
            min-width: 250px;
            box-sizing: border-box;
        }
        .card h3 {
            margin-top: 0;
            color: var(--black-forest);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .card p {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        .card-link {
            display: inline-block;
            margin-top: 10px;
            color: var(--olive-leaf);
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s;
        }
        .card-link:hover {
            color: var(--black-forest);
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-brand">Reptile & Fish Store</div>
        <div class="nav-links">
            <!-- ADMIN SPECIFIC LINKS -->
            <?php if ($role === 'Admin'): ?>
                <a href="index.php?page=admin_dashboard">Dashboard</a>
                <a href="index.php?page=all_views">Master Views</a> <!-- ADD THIS LINE -->
                <a href="index.php?page=master_barang">Master Barang</a>
                <a href="index.php?page=master_user">Master User</a>
            <?php endif; ?>

            <!-- MANAGER LINKS  -->
            <?php if (in_array($role, ['Admin', 'Manager'])): ?>
                <a href="index.php?page=pengadaan">Pengadaan (Restock)</a>
                <a href="index.php?page=penerimaan">Penerimaan</a>
            <?php endif; ?>

            <!-- KASIR LINKS  -->
            <?php if (in_array($role, ['Admin', 'Manager', 'Kasir'])): ?>
                <a href="index.php?page=kasir_dashboard">POS Penjualan</a>
            <?php endif; ?>
            
            <span class="nav-user">| Halo, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars($role) ?>)</span>
            <a href="index.php?action=logout" class="logout-btn">Logout</a>
        </div>
    </nav>
    <main></main>