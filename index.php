<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'config/Database.php';
require_once 'classes/Auth.php';
require_once 'controllers/Barangcontroller.php';

// Route Definitions
$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? 'login';

// 1. Form Submissions / Actions (POST/GET Requests)
if ($action) {
    switch ($action) {
        case 'login':
        case 'logout':
            require_once 'controllers/AuthController.php';
            break;

        case 'create_barang':
        case 'update_barang':
        case 'delete_barang':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/BarangController.php';
            break;
            
        case 'create_user':
        case 'update_user':
        case 'delete_user':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/UserController.php';
            break;

        // --- NEW KASIR POS SESSION CART ROUTES ---
        case 'add_to_cart':
        case 'remove_from_cart':
        case 'checkout_pos':
            Auth::checkAccess(['Kasir', 'Manager', 'Admin']);
            require_once 'controllers/TransaksiController.php';
            break;

        // --- NEW MANAGER PENGADAAN & PENERIMAAN CART ROUTES ---
        case 'add_po_cart':
        case 'remove_po_cart':
        case 'checkout_po':
        case 'load_rcv_cart':
        case 'update_rcv_qty':
        case 'remove_rcv_cart':
        case 'checkout_rcv':
        case 'add_retur_cart':
        case 'remove_retur_cart':
        case 'checkout_retur':
            
            Auth::checkAccess(['Manager', 'Admin']);
            require_once 'controllers/PengadaanController.php';
            break;

        

        default:
            die("Action not found.");
    }
    exit; 
}

// 2. Page Routing (Views)
switch ($page) {
    case 'login':
        if (isset($_SESSION['role'])) {
            header("Location: index.php?page=" . strtolower($_SESSION['role']) . "_dashboard");
            exit;
        }
        require_once 'views/auth/login.php';
        break;
        
    case 'admin_dashboard':
        Auth::checkAccess(['Admin']); 
        require_once 'views/admin/dashboard.php';
        break;

    // --- TAMBAHAN HALAMAN MASTER BARANG ---
    case 'master_barang':
        Auth::checkAccess(['Manager','Admin']);
        require_once 'views/admin/master_barang.php';
        break;
        
    case 'pengadaan':
case 'manager_dashboard':
    Auth::checkAccess(['Manager', 'Admin']);
    require_once 'views/manager/pengadaan.php';
    break;
        
    case 'kasir_dashboard':
        Auth::checkAccess(['Kasir', 'Manager', 'Admin']); 
        require_once 'views/kasir/pos.php';
        break;
        
    case 'unauthorized':
        require_once 'views/error/403.php'; 
        break;

    case 'all_views':
        Auth::checkAccess(['Admin']); 
        require_once 'views/admin/all_views.php';
        break;

    case 'master_user':
    Auth::checkAccess(['Admin']);
    require_once 'views/admin/master_user.php';
    break;

    case 'kartu_stok':
    Auth::checkAccess(['Admin', 'Manager']);
    require_once 'views/admin/kartu_stok.php';
    break;
    
    case 'penerimaan':
    case 'pengadaan':
    case 'manager_dashboard':
        Auth::checkAccess(['Manager', 'Admin']);
        require_once 'views/manager/pengadaan.php';
        break;

    
        
    default:
        echo "<h2>404 - Page Not Found</h2>";
        break;
}
?>