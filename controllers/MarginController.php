<?php
require_once 'classes/Margin.php';

$db = new Database();
$conn = $db->getConnection();
$marginModel = new Margin($conn);

$action = $_GET['action'] ?? '';

if ($action === 'create_margin' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $persen = (float)($_POST['persen'] ?? 0);
    $setActive = isset($_POST['set_active']) && $_POST['set_active'] === '1';

    if ($persen < 0) {
        header("Location: index.php?page=margin_settings&err=" . urlencode("Margin percentage cannot be negative."));
        exit;
    }

    try {
        $marginModel->createMargin($persen, $setActive);
        header("Location: index.php?page=margin_settings&msg=" . urlencode("Margin saved."));
    } catch (PDOException $e) {
        header("Location: index.php?page=margin_settings&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'activate_margin') {
    $idmargin = (int)($_GET['id'] ?? $_POST['idmargin'] ?? 0);
    if ($idmargin <= 0) {
        header("Location: index.php?page=margin_settings&err=" . urlencode("Invalid margin ID."));
        exit;
    }

    try {
        $marginModel->setActiveMargin($idmargin);
        header("Location: index.php?page=margin_settings&msg=" . urlencode("Active margin updated. Only one margin is active."));
    } catch (PDOException $e) {
        header("Location: index.php?page=margin_settings&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}
?>