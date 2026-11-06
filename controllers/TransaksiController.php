<?php


require_once 'classes/Penjualan.php';

$db = new Database();
$conn = $db->getConnection();
$penjualanModel = new Penjualan($conn);

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'checkout_pos') {
    $iduser   = (int)($_SESSION['user_id'] ?? 0);
    $idmargin = (int)($_POST['idmargin'] ?? 0);
    $itemsRaw = $_POST['items_json'] ?? '[]';
    $items    = json_decode($itemsRaw, true);

    if ($iduser > 0 && $idmargin > 0 && !empty($items) && is_array($items)) {
        try {
            $penjualanModel->checkoutMulti($iduser, $idmargin, $items);
            header("Location: index.php?page=kasir_dashboard&msg=success");
        } catch (PDOException $e) {
            // Catches stock validation signals thrown by sp_checkout_penjualan_multi
            header("Location: index.php?page=kasir_dashboard&err=" . urlencode($e->getMessage()));
        }
    } else {
        header("Location: index.php?page=kasir_dashboard&err=Cart+cannot+be+empty");
    }
    exit;
}
?>