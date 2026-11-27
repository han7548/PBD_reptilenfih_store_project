<?php
$db = new Database();
$conn = $db->getConnection();
$action = $_GET['action'] ?? '';

if ($action === 'create_role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_role'] ?? '');
    try {
        $stmt = $conn->prepare("INSERT INTO role (nama_role) VALUES (:nama)");
        $stmt->bindValue(':nama', $nama);
        $stmt->execute();
        header("Location: index.php?page=master_role&msg=" . urlencode("Role added."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_role&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}

if ($action === 'update_role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['idrole'] ?? 0);
    $nama = trim($_POST['nama_role'] ?? '');
    try {
        $stmt = $conn->prepare("UPDATE role SET nama_role = :nama WHERE idrole = :id");
        $stmt->bindValue(':nama', $nama);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: index.php?page=master_role&msg=" . urlencode("Role updated."));
    } catch (PDOException $e) {
        header("Location: index.php?page=master_role&err=" . urlencode("DB Error: " . $e->getMessage()));
    }
    exit;
}
?>