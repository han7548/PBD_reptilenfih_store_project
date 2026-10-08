<?php
// gak full blown class sttructure dulu, cuma pengen test koneksi aja dulu
$host = '127.0.0.1';
$dbname = 'Tugas_Proyek_PBD_Toko';
$username = 'root'; 
$password = 'AkuAdminKamuMember'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    if (isset($_POST['add_vendor'])) {
        
        $stmt = $pdo->prepare("CALL sp_insert_vendor(:nama, :badan, :status)");
        $stmt->execute([
            ':nama' => $_POST['nama_vendor'],
            ':badan' => $_POST['badan_hukum'],
            ':status' => $_POST['status']
        ]);
        echo "<script>alert('Vendor Added!'); window.location.href='test.php';</script>";
    }
    
    
    if (isset($_POST['edit_vendor'])) {
        $stmt = $pdo->prepare("CALL sp_update_vendor(:id, :nama, :badan, :status)");
        $stmt->execute([
            ':id' => $_POST['idvendor'],
            ':nama' => $_POST['nama_vendor'],
            ':badan' => $_POST['badan_hukum'],
            ':status' => $_POST['status']
        ]);
        echo "<script>alert('Vendor Updated!'); window.location.href='test.php';</script>";
    }

    
    if (isset($_POST['delete_vendor'])) {
        $stmt = $pdo->prepare("CALL sp_delete_vendor(:id)");
        $stmt->execute([':id' => $_POST['idvendor']]);
        echo "<script>alert('Vendor Deleted!'); window.location.href='test.php';</script>";
    }
}


$vendors = $pdo->query("SELECT * FROM vendor")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vendor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #b5eee4; padding: 8px; text-align: left; }
        th { background-color: #b7edf8; }
        .form-container { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; width: 300px; }
    </style>
</head>
<body>

    <h2>Vendor Management (C U D Frontend)</h2>

    <div class="form-container">
        <h3>Add New Vendor</h3>
        <form method="POST">
            <label>Nama Vendor:</label><br>
            <input type="text" name="nama_vendor" required><br><br>
            
            <label>Badan Hukum (e.g., P, C):</label><br>
            <input type="text" name="badan_hukum" maxlength="1" required><br><br>
            
            <label>Status (A/I):</label><br>
            <select name="status">
                <option value="A">Active (A)</option>
                <option value="I">Inactive (I)</option>
            </select><br><br>
            
            <button type="submit" name="add_vendor">Add Vendor</button>
        </form>
    </div>

    <h3>Vendor List</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama Vendor</th>
            <th>Badan Hukum</th>
            <th>Status</th>
            <th>Actions (Update / Delete)</th>
        </tr>
        <?php foreach ($vendors as $v): ?>
        <tr>
            <form method="POST">
                <td><?= $v['idvendor'] ?> <input type="hidden" name="idvendor" value="<?= $v['idvendor'] ?>"></td>
                <td><input type="text" name="nama_vendor" value="<?= htmlspecialchars($v['nama_vendor']) ?>"></td>
                <td><input type="text" name="badan_hukum" value="<?= htmlspecialchars($v['badan_hukum']) ?>" maxlength="1" style="width: 30px;"></td>
                <td>
                    <select name="status">
                        <option value="A" <?= $v['status'] == 'A' ? 'selected' : '' ?>>A</option>
                        <option value="I" <?= $v['status'] == 'I' ? 'selected' : '' ?>>I</option>
                    </select>
                </td>
                <td>
                    <button type="submit" name="edit_vendor">Update</button>
                    <button type="submit" name="delete_vendor" onclick="return confirm('Are you sure?')">Delete</button>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>