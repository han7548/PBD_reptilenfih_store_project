<?php
$db = new Database();
$conn = $db->getConnection();
$action = $_GET['action'] ?? '';

if ($action === 'toggle_satuan_status') {
    $id = (int)($_GET['id'] ?? 0);
    $status = ((int)($_GET['status'] ?? 0) === 1) ? 1 : 0;

    try {
        $stmt = $conn->prepare("UPDATE satuan SET STATUS = :status WHERE idsatuan = :id");
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $msg = $status === 1 ? "Satuan activated." : "Satuan deactivated.";
        header("Location: index.php?page=master_satuan&msg=" . urlencode($msg));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_satuan&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'create_satuan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_satuan'] ?? '');
    try {
        $stmt = $conn->prepare("INSERT INTO satuan (nama_satuan, STATUS) VALUES (:nama, 1)");
        $stmt->bindValue(':nama', $nama);
        $stmt->execute();
        header("Location: index.php?page=master_satuan&msg=" . urlencode("Satuan added."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_satuan&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'update_satuan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['idsatuan'] ?? 0);
    $nama = trim($_POST['nama_satuan'] ?? '');
    try {
        $stmt = $conn->prepare("UPDATE satuan SET nama_satuan = :nama WHERE idsatuan = :id");
        $stmt->bindValue(':nama', $nama);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: index.php?page=master_satuan&msg=" . urlencode("Satuan updated."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_satuan&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}
?>