<?php
// controllers/TransaksiController.php
require_once 'classes/Penjualan.php';
$db = new Database();
$conn = $db->getConnection();
$penjualanModel = new Penjualan($conn);

$action = $_GET['action'] ?? '';

// Initialize Cart
if (!isset($_SESSION['pos_cart'])) {
    $_SESSION['pos_cart'] = [];
}

// 1. ADD ITEM TO SESSION CART
if ($action === 'add_to_cart') {
    $idbarang = (int)$_POST['idbarang'];
    $nama = $_POST['nama_barang'];
    $harga = (int)$_POST['harga'];
    $qty = (int)$_POST['qty'];
    $maxStock = (int)$_POST['max_stock'];

    if (isset($_SESSION['pos_cart'][$idbarang])) {
        $newQty = $_SESSION['pos_cart'][$idbarang]['qty'] + $qty;
        $_SESSION['pos_cart'][$idbarang]['qty'] = min($newQty, $maxStock); // Prevent exceeding stock
    } else {
        $_SESSION['pos_cart'][$idbarang] = [
            'nama' => $nama,
            'harga' => $harga,
            'qty' => $qty,
            'maxStock' => $maxStock
        ];
    }
    header("Location: index.php?page=kasir_dashboard");
    exit;
}

// 2. REMOVE ITEM FROM SESSION CART
if ($action === 'remove_from_cart') {
    $id = (int)$_GET['id'];
    unset($_SESSION['pos_cart'][$id]);
    header("Location: index.php?page=kasir_dashboard");
    exit;
}

// 3. FINAL CHECKOUT TO DATABASE (Uses the INOUT Stored Procedure!)
if ($action === 'checkout_pos') {
    $iduser = (int)$_SESSION['user_id'];
    $idmargin = (int)$_POST['idmargin'];
    $cart = $_SESSION['pos_cart'];

    if (empty($cart)) {
        header("Location: index.php?page=kasir_dashboard&err=Cart is empty!");
        exit;
    }

    try {
        // We use PDO to track the INOUT variable for the header ID
        $conn->query("SET @idpenjualan = 0"); 
        
        $stmt = $conn->prepare("CALL sp_simpan_penjualan(:iduser, :idmargin, :idbarang, :qty, :harga, @idpenjualan)");

        // Loop the session cart!
        foreach ($cart as $idbarang => $item) {
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->bindParam(':idmargin', $idmargin, PDO::PARAM_INT);
            $stmt->bindParam(':idbarang', $idbarang, PDO::PARAM_INT);
            $stmt->bindValue(':qty', $item['qty'], PDO::PARAM_INT);
            $stmt->bindValue(':harga', $item['harga'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor(); // Must close cursor before next loop in PDO
        }

        // Clear cart on success
        $_SESSION['pos_cart'] = [];
        header("Location: index.php?page=kasir_dashboard&msg=Transaction Successful!");

    } catch (PDOException $e) {
        header("Location: index.php?page=kasir_dashboard&err=DB Error: " . urlencode($e->getMessage()));
    }
    exit;
}
?>