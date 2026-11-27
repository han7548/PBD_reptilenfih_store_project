<?php
$db = new Database();
$conn = $db->getConnection();
$action = $_GET['action'] ?? '';

if ($action === 'toggle_vendor_status') {
    $id = (int)($_GET['id'] ?? 0);
    $status = ($_GET['status'] ?? 'I') === 'A' ? 'A' : 'I';

    try {
        $stmt = $conn->prepare("UPDATE vendor SET status = :status WHERE idvendor = :id");
        $stmt->bindValue(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $msg = $status === 'A' ? "Vendor activated." : "Vendor deactivated.";
        header("Location: index.php?page=master_vendor&msg=" . urlencode($msg));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_vendor&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'create_vendor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_vendor'] ?? '');
    $badan = $_POST['badan_hukum'] ?? 'P';

    try {
        $stmt = $conn->prepare("CALL sp_insert_vendor(:nama, :badan, 'A')");
        $stmt->bindValue(':nama', $nama);
        $stmt->bindValue(':badan', $badan);
        $stmt->execute();
        $stmt->closeCursor();
        header("Location: index.php?page=master_vendor&msg=" . urlencode("Vendor added."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_vendor&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'update_vendor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['idvendor'] ?? 0);
    $nama = trim($_POST['nama_vendor'] ?? '');
    $badan = $_POST['badan_hukum'] ?? 'P';
    $status = ($_POST['status'] ?? 'A') === 'A' ? 'A' : 'I';

    try {
        $stmt = $conn->prepare("CALL sp_update_vendor(:id, :nama, :badan, :status)");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nama', $nama);
        $stmt->bindValue(':badan', $badan);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        $stmt->closeCursor();
        header("Location: index.php?page=master_vendor&msg=" . urlencode("Vendor updated."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_vendor&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}
?>