<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'config/Database.php';
require_once 'classes/Auth.php';

$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? 'login';

if ($action) {
    switch ($action) {
        case 'login':
        case 'logout':
            require_once 'controllers/AuthController.php';
            break;

        // Master Barang: Create, Update, Activate/Deactivate (No hard delete)
        case 'create_barang':
        case 'update_barang':
        case 'toggle_barang_status':
        case 'delete_barang':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/BarangController.php';
            break;

        // Master User: Create, Update, Activate/Deactivate (No hard delete)
        case 'create_user':
        case 'update_user':
        case 'toggle_user_status':
        case 'delete_user':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/UserController.php';
            break;

        // Margin Management (Admin only)
        case 'create_margin':
        case 'activate_margin':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/MarginController.php';
            break;

        // Kasir POS Session Cart
        case 'add_to_cart':
        case 'remove_from_cart':
        case 'checkout_pos':
            Auth::checkAccess(['Kasir', 'Manager', 'Admin']);
            require_once 'controllers/TransaksiController.php';
            break;

        // Pengadaan, Penerimaan & Retur Session Carts
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

        // Master Vendor (Admin)
        case 'create_vendor':
        case 'update_vendor':
        case 'toggle_vendor_status':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/VendorController.php';
            break;

        // Master Satuan (Admin)
        case 'create_satuan':
        case 'update_satuan':
        case 'toggle_satuan_status':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/SatuanController.php';
            break;

        // Master Role (Admin)
        case 'create_role':
        case 'update_role':
            Auth::checkAccess(['Admin']);
            require_once 'controllers/RoleController.php';
            break;

        default:
            die("Action not found.");
    }
    exit;
}

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

    case 'master_barang':
        Auth::checkAccess(['Admin']);
        require_once 'views/admin/master_barang.php';
        break;

    case 'master_user':
        Auth::checkAccess(['Admin']);
        require_once 'views/admin/master_user.php';
        break;

    case 'margin_settings':
        Auth::checkAccess(['Admin']);
        require_once 'views/admin/margin_settings.php';
        break;

    case 'penerimaan':
    case 'pengadaan':
    case 'manager_dashboard':
        Auth::checkAccess(['Manager', 'Admin']);
        require_once 'views/manager/pengadaan.php';
        break;

    case 'kasir_dashboard':
        Auth::checkAccess(['Kasir', 'Manager', 'Admin']);
        require_once 'views/kasir/pos.php';
        break;

    case 'riwayat_penjualan':
        Auth::checkAccess(['Kasir', 'Manager', 'Admin']);
        require_once 'views/transaksi/riwayat_penjualan.php';
        break;

    case 'kartu_stok':
        Auth::checkAccess(['Kasir', 'Manager', 'Admin']);
        require_once 'views/admin/kartu_stok.php';
        break;

    case 'master_vendor':
        Auth::checkAccess(['Admin']);
        require_once 'views/admin/master_vendor.php';
        break;

    case 'master_satuan':
        Auth::checkAccess(['Admin']);
        require_once 'views/admin/master_satuan.php';
        break;

    case 'master_role':
        Auth::checkAccess(['Admin']);
        require_once 'views/admin/master_role.php';
        break;

    case 'unauthorized':
        require_once 'views/error/403.php';
        break;

    default:
        echo "<h2>404 - Page Not Found</h2>";
        break;
}
?>