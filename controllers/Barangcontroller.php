<?php
// controllers/BarangController.php

require_once 'classes/Barang.php';

$db = new Database();
$conn = $db->getConnection();
$barangModel = new Barang($conn);

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create_barang') {
        $jenis    = trim($_POST['jenis'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $idsatuan = (int)($_POST['idsatuan'] ?? 0);
        $status   = (int)($_POST['status'] ?? 1);
        $harga    = (int)($_POST['harga'] ?? 0);

        if (!empty($jenis) && !empty($nama) && $idsatuan > 0 && $harga >= 0) {
            $barangModel->create($jenis, $nama, $idsatuan, $status, $harga);
            header("Location: index.php?page=master_barang&msg=added");
        } else {
            header("Location: index.php?page=master_barang&err=invalid_input");
        }
        exit;
    }

    if ($action === 'update_barang') {
        $id       = (int)($_POST['idbarrang'] ?? 0);
        $jenis    = trim($_POST['jenis'] ?? '');
        $nama     = trim($_POST['nama'] ?? '');
        $idsatuan = (int)($_POST['idsatuan'] ?? 0);
        $status   = (int)($_POST['status'] ?? 1);
        $harga    = (int)($_POST['harga'] ?? 0);

        if ($id > 0 && !empty($jenis) && !empty($nama) && $idsatuan > 0) {
            $barangModel->update($id, $jenis, $nama, $idsatuan, $status, $harga);
            header("Location: index.php?page=master_barang&msg=updated");
        } else {
            header("Location: index.php?page=master_barang&err=invalid_input");
        }
        exit;
    }
}

if ($action === 'delete_barang') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0) {
        try {
            $barangModel->delete($id);
            header("Location: index.php?page=master_barang&msg=deleted");
        } catch (PDOException $e) {
            // Catches Foreign Key constraint violations if item is linked to sales/purchases
            header("Location: index.php?page=master_barang&err=fk_constraint");
        }
    }
    exit;
}
?>