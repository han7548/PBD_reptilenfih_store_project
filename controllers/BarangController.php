<?php
$db = new Database();
$conn = $db->getConnection();

$action = $_GET['action'] ?? '';

if ($action === 'toggle_barang_status' || $action === 'delete_barang') {
    $id = (int)($_GET['id'] ?? $_POST['idbarang'] ?? 0);
    $targetStatus = isset($_GET['status']) ? (int)$_GET['status'] : 0;
    $targetStatus = ($targetStatus === 1) ? 1 : 0;

    if ($id <= 0) {
        header("Location: index.php?page=master_barang&err=" . urlencode("Invalid item ID."));
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE barang SET STATUS = :status WHERE idbarrang = :id");
        $stmt->bindParam(':status', $targetStatus, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $msg = $targetStatus === 1 ? "Item activated." : "Item deactivated and moved to Inactive table.";
        header("Location: index.php?page=master_barang&msg=" . urlencode($msg));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_barang&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'create_barang' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis = $_POST['jenis'] ?? 'P';
    $nama = trim($_POST['nama'] ?? '');
    $idsatuan = (int)($_POST['idsatuan'] ?? 1);
    $harga = (int)($_POST['harga'] ?? 0);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    try {
        $stmt = $conn->prepare("INSERT INTO barang (jenis, nama, idsatuan, harga, STATUS) VALUES (:jenis, :nama, :idsatuan, :harga, :status)");
        $stmt->bindValue(':jenis', $jenis);
        $stmt->bindValue(':nama', $nama);
        $stmt->bindParam(':idsatuan', $idsatuan, PDO::PARAM_INT);
        $stmt->bindParam(':harga', $harga, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: index.php?page=master_barang&msg=" . urlencode("Barang added."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_barang&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'update_barang' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['idbarang'] ?? 0);
    $jenis = $_POST['jenis'] ?? 'P';
    $nama = trim($_POST['nama'] ?? '');
    $idsatuan = (int)($_POST['idsatuan'] ?? 1);
    $harga = (int)($_POST['harga'] ?? 0);

    try {
        $stmt = $conn->prepare("UPDATE barang SET jenis = :jenis, nama = :nama, idsatuan = :idsatuan, harga = :harga WHERE idbarrang = :id");
        $stmt->bindValue(':jenis', $jenis);
        $stmt->bindValue(':nama', $nama);
        $stmt->bindParam(':idsatuan', $idsatuan, PDO::PARAM_INT);
        $stmt->bindParam(':harga', $harga, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: index.php?page=master_barang&msg=" . urlencode("Barang updated."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_barang&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}
?>