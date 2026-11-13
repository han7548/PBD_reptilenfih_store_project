<?php


require_once 'classes/Pengadaan.php';

$db = new Database();
$conn = $db->getConnection();
$pengadaanModel = new Pengadaan($conn);

$action = $_GET['action'] ?? '';

// Initialize Session Carts
if (!isset($_SESSION['po_cart'])) $_SESSION['po_cart'] = [];
if (!isset($_SESSION['rcv_cart'])) $_SESSION['rcv_cart'] = [];
if (!isset($_SESSION['retur_cart'])) $_SESSION['retur_cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $action) {

   
    if ($action === 'add_po_cart') {
        $idbarang = (int)$_POST['idbarang'];
        $newVendorId = (int)$_POST['vendor_id'];

        // SECURITY CHECK: If user changes vendor mid-way, clear the previous cart items!
        if (isset($_SESSION['active_vendor_id']) && $_SESSION['active_vendor_id'] !== $newVendorId) {
            $_SESSION['po_cart'] = [];
        }

        if ($idbarang > 0) {
            // Add or update quantity
            if (isset($_SESSION['po_cart'][$idbarang])) {
                $_SESSION['po_cart'][$idbarang]['qty'] += (int)$_POST['qty'];
            } else {
                $_SESSION['po_cart'][$idbarang] = [
                    'nama' => $_POST['nama_barang'],
                    'harga' => (int)$_POST['harga'],
                    'qty' => (int)$_POST['qty']
                ];
            }
            $_SESSION['active_vendor_id'] = $newVendorId; 
        }
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'remove_po_cart') {
        unset($_SESSION['po_cart'][(int)$_GET['id']]);
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'checkout_po') {
        $iduser = (int)$_SESSION['user_id'];
        $idvendor = (int)$_POST['vendor_id'];
        if (!empty($_SESSION['po_cart']) && $idvendor > 0) {
            try {
                $pengadaanModel->checkoutPengadaan($iduser, $idvendor, $_SESSION['po_cart']);
                $_SESSION['po_cart'] = [];
                unset($_SESSION['active_vendor_id']);
                header("Location: index.php?page=pengadaan&msg=Pengadaan Order successfully created!");
            } catch (Exception $e) {
                header("Location: index.php?page=pengadaan&err=DB Error: " . urlencode($e->getMessage()));
            }
        } else {
            header("Location: index.php?page=pengadaan&err=Vendor and Cart cannot be empty.");
        }
        exit;
    }

   
    if ($action === 'load_rcv_cart') {
        $idpengadaan = (int)$_POST['idpengadaan'];
        $_SESSION['active_pengadaan_id'] = $idpengadaan;
        $_SESSION['rcv_cart'] = []; // Reset cart
        
        $items = $pengadaanModel->getPengadaanItemsForPenerimaan($idpengadaan);
        foreach ($items as $item) {
            $_SESSION['rcv_cart'][$item['idbarang']] = [
                'nama' => $item['nama_barang'],
                'harga' => $item['harga_satuan'],
                'qty' => $item['sisa_belum_terima'], // Auto-fill with remaining qty!
                'max_qty' => $item['sisa_belum_terima'] // Lock the max to the remaining qty!
            ];
        }
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'update_rcv_qty') {
        $idbarang = (int)$_POST['idbarang'];
        $newQty = (int)$_POST['qty'];
        if (isset($_SESSION['rcv_cart'][$idbarang])) {
            $max = $_SESSION['rcv_cart'][$idbarang]['max_qty'];
            // Validation: Cannot receive more than ordered
            $_SESSION['rcv_cart'][$idbarang]['qty'] = ($newQty > $max) ? $max : $newQty;
        }
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'remove_rcv_cart') {
        unset($_SESSION['rcv_cart'][(int)$_GET['id']]);
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'checkout_rcv') {
        $iduser = (int)$_SESSION['user_id'];
        $idpengadaan = (int)$_SESSION['active_pengadaan_id'];
        if (!empty($_SESSION['rcv_cart']) && $idpengadaan > 0) {
            try {
                $pengadaanModel->checkoutPenerimaan($iduser, $idpengadaan, $_SESSION['rcv_cart']);
                $_SESSION['rcv_cart'] = [];
                unset($_SESSION['active_pengadaan_id']);
                header("Location: index.php?page=pengadaan&msg=Penerimaan processed! Stock updated.");
            } catch (Exception $e) {
                header("Location: index.php?page=pengadaan&err=DB Error: " . urlencode($e->getMessage()));
            }
        }
        exit;
    }

    // ==========================================
    // MODULE 3: RETUR
    // ==========================================
    if ($action === 'add_retur_cart') {
        $iddetail = (int)$_POST['iddetail_penerimaan'];
        if ($iddetail > 0) {
            $_SESSION['retur_cart'][$iddetail] = [
                'idpenerimaan' => (int)$_POST['idpenerimaan'],
                'nama' => $_POST['nama_barang'],
                'qty' => (int)$_POST['qty'],
                'alasan' => $_POST['alasan']
            ];
        }
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'remove_retur_cart') {
        unset($_SESSION['retur_cart'][(int)$_GET['id']]);
        header("Location: index.php?page=pengadaan");
        exit;
    }

    if ($action === 'checkout_retur') {
        $iduser = (int)$_SESSION['user_id'];
        if (!empty($_SESSION['retur_cart'])) {
            try {
                // All items in cart must belong to the same penerimaan for simplicity
                $firstItem = reset($_SESSION['retur_cart']);
                $idpenerimaan = $firstItem['idpenerimaan'];
                
                $pengadaanModel->checkoutRetur($iduser, $idpenerimaan, $_SESSION['retur_cart']);
                $_SESSION['retur_cart'] = [];
                header("Location: index.php?page=pengadaan&msg=Retur processed! Broken stock deducted.");
            } catch (Exception $e) {
                header("Location: index.php?page=pengadaan&err=DB Error: " . urlencode($e->getMessage()));
            }
        }
        exit;
    }
}
?>