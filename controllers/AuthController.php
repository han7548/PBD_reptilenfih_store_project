<?php

// Note: Database.php and Auth.php are already included by index.php before reaching here

$db = new Database();
$conn = $db->getConnection();
$auth = new Auth($conn);

if ($_GET['action'] === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        
        if ($auth->login($username, $password)) {
            $role = $_SESSION['role'];
            
            // Route 
            if ($role === 'Admin') {
                header("Location: index.php?page=admin_dashboard");
            } elseif ($role === 'Manager') {
                header("Location: index.php?page=manager_dashboard");
            } elseif ($role === 'Kasir') {
                header("Location: index.php?page=kasir_dashboard");
            }
            exit;
        } else {
            //  error flag
            header("Location: index.php?page=login&error=1");
            exit;
        }
    }
} elseif ($_GET['action'] === 'logout') {
    // Clear session n return to login
    session_unset();
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}
?>