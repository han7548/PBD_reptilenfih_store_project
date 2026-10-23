<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// iya yah belom bikin bootstrap, habis ini deh aku benerin
require_once 'config/Database.php';
require_once 'classes/Auth.php';

// Route Definitions nderr
$action = $_GET['action'] ?? '';
$page = $_GET['page'] ?? 'login';

// Form Submissions / POST requests
if ($action) {
    switch ($action) {
        case 'login':
        case 'logout':
            require_once 'controllers/AuthController.php';
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
        
    case 'manager_dashboard':
        Auth::checkAccess(['Manager', 'Admin']); 
        require_once 'views/manager/dashboard.php';
        break;
        
    case 'kasir_dashboard':
        Auth::checkAccess(['Kasir', 'Manager', 'Admin']); 
        require_once 'views/kasir/pos.php';
        break;
        
    case 'unauthorized':
        require_once 'views/error/403.php'; 
        break;

    case 'all_views':
        Auth::checkAccess(['Admin']); // Only Admins should see all raw master data
        require_once 'views/admin/all_views.php';
        break;
        
    default:
        echo "<h2>404 - Page Not Found</h2>";
        break;
}
?>