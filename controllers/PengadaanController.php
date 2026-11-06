<?php
// controllers/PengadaanController.php

require_once 'classes/Pengadaan.php';

$db = new Database();
$conn = $db->getConnection();
$pengadaanModel = new Pengadaan($conn);

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create_pengadaan') {
        $userId   = (int)($_SESSION['user_id'] ?? 0);
        $vendorId = (int)($_POST['vendor_id'] ?? 0);
        $itemsRaw = $_POST['items_json'] ?? '[]';
        $items    = json_decode($itemsRaw, true);

        if ($userId > 0 && $vendorId > 0 && !empty($items)) {
            try {
                $pengadaanModel->createPengadaanMulti($userId, $vendorId, $items);
                header("Location: index.php?page=pengadaan&msg=pengadaan_created");
            } catch (Exception $e) {
                header("Location: index.php?page=pengadaan&err=" . urlencode($e->getMessage()));
            }
        } else {
            header("Location: index.php?page=pengadaan&err=Cart+cannot+be+empty");
        }
        exit;
    }

    if ($action === 'proses_penerimaan') {
        $userId      = (int)($_SESSION['user_id'] ?? 0);
        $idPengadaan = (int)($_POST['idpengadaan'] ?? 0);
        $itemsRaw    = $_POST['items_json'] ?? '[]';
        $items       = json_decode($itemsRaw, true);

        if ($userId > 0 && $idPengadaan > 0 && !empty($items)) {
            try {
                $pengadaanModel->processPenerimaanMulti($idPengadaan, $userId, $items);
                header("Location: index.php?page=pengadaan&msg=penerimaan_processed");
            } catch (Exception $e) {
                header("Location: index.php?page=pengadaan&err=" . urlencode($e->getMessage()));
            }
        } else {
            header("Location: index.php?page=pengadaan&err=Invalid+reception+items");
        }
        exit;
    }

    if ($action === 'proses_retur') {
        $userId             = (int)($_SESSION['user_id'] ?? 0);
        $idPenerimaan       = (int)($_POST['idpenerimaan'] ?? 0);
        $idDetailPenerimaan = (int)($_POST['iddetail_penerimaan'] ?? 0);
        $jumlahRetur        = (int)($_POST['jumlah_retur'] ?? 0);
        $alasan             = trim($_POST['alasan'] ?? '');

        if ($userId > 0 && $idPenerimaan > 0 && $idDetailPenerimaan > 0 && $jumlahRetur > 0 && !empty($alasan)) {
            try {
                $pengadaanModel->processRetur($idPenerimaan, $idDetailPenerimaan, $userId, $jumlahRetur, $alasan);
                header("Location: index.php?page=pengadaan&msg=retur_processed");
            } catch (PDOException $e) {
                header("Location: index.php?page=pengadaan&err=" . urlencode($e->getMessage()));
            }
        } else {
            header("Location: index.php?page=pengadaan&err=Invalid+retur+input");
        }
        exit;
    }
}
?>