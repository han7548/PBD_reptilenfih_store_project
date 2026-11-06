<?php
// controllers/UserController.php

require_once 'classes/User.php';

$db = new Database();
$conn = $db->getConnection();
$userModel = new User($conn);

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create_user') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $idrole   = (int)($_POST['idrole'] ?? 0);

        if (!empty($username) && !empty($password) && $idrole > 0) {
            $userModel->create($username, $password, $idrole);
            header("Location: index.php?page=master_user&msg=added");
        } else {
            header("Location: index.php?page=master_user&err=invalid_input");
        }
        exit;
    }

    if ($action === 'update_user') {
        $iduser   = (int)($_POST['iduser'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $idrole   = (int)($_POST['idrole'] ?? 0);

        if ($iduser > 0 && !empty($username) && $idrole > 0) {
            $userModel->update($iduser, $username, $idrole, $password);
            header("Location: index.php?page=master_user&msg=updated");
        } else {
            header("Location: index.php?page=master_user&err=invalid_input");
        }
        exit;
    }
}

if ($action === 'delete_user') {
    $id = (int)($_GET['id'] ?? 0);

    // Prevent active user from deleting their own logged-in account
    if ($id === $_SESSION['user_id']) {
        header("Location: index.php?page=master_user&err=self_delete");
        exit;
    }

    if ($id > 0) {
        try {
            $userModel->delete($id);
            header("Location: index.php?page=master_user&msg=deleted");
        } catch (PDOException $e) {
            header("Location: index.php?page=master_user&err=fk_constraint");
        }
    }
    exit;
}
?>