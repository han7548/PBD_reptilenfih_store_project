<?php


class Pengadaan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getActiveVendors() {
        $query = "SELECT `no` AS idvendor, nama_vendor FROM view_vendor WHERE status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActiveBarang() {
        $query = "SELECT `no` AS idbarang, nama, jenis, harga FROM view_barang WHERE status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch Pengadaan that are 'Pending' or 'Sebagian' (Partial)
    public function getPendingPengadaan() {
        $query = "SELECT `no` AS idpengadaan, nama_vendor, total_nilai, status 
                  FROM view_pengadaan WHERE status IN ('Pending', 'Sebagian')";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch items for a specific Pengadaan to Auto-Fill the Penerimaan Cart
    // Fetch items for a specific Pengadaan, calculating what is LEFT to receive
    public function getPengadaanItemsForPenerimaan($idpengadaan) {
        $query = "SELECT 
                    dp.idbarang, 
                    dp.nama_barang, 
                    dp.jumlah_order, 
                    dp.harga_satuan,
                    (dp.jumlah_order - COALESCE((
                        SELECT SUM(dpen.jumlah_terima) 
                        FROM detail_penerimaan dpen 
                        JOIN penerimaan p ON dpen.idpenerimaan = p.idpenerimaan 
                        WHERE p.idpengadaan = dp.no_pengadaan AND dpen.barang_idbarang = dp.idbarang
                    ), 0)) AS sisa_belum_terima
                  FROM view_detail_pengadaan dp
                  WHERE dp.no_pengadaan = :idpengadaan
                  HAVING sisa_belum_terima > 0";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idpengadaan', $idpengadaan, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch received items for Retur validation
    public function getPenerimaanDetailsForRetur() {
        $query = "SELECT no_detail_terima, no_penerimaan, idbarang, nama_barang, jumlah_terima 
                  FROM view_detail_penerimaan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    public function checkoutPengadaan($iduser, $idvendor, $cart) {
        $this->conn->query("SET @idpengadaan = 0");
        $stmt = $this->conn->prepare("CALL sp_simpan_pengadaan(:iduser, :idvendor, :idbarang, :qty, :harga, @idpengadaan)");

        foreach ($cart as $idbarang => $item) {
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->bindParam(':idvendor', $idvendor, PDO::PARAM_INT);
            $stmt->bindParam(':idbarang', $idbarang, PDO::PARAM_INT);
            $stmt->bindValue(':qty', $item['qty'], PDO::PARAM_INT);
            $stmt->bindValue(':harga', $item['harga'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor(); 
        }
        return true;
    }

    // --- 2. EXECUTE PENERIMAAN ---
    public function checkoutPenerimaan($iduser, $idpengadaan, $cart) {
        $this->conn->query("SET @idpenerimaan = 0");
        $stmt = $this->conn->prepare("CALL sp_simpan_penerimaan(:idpengadaan, :iduser, :idbarang, :qty, :harga, @idpenerimaan)");

        foreach ($cart as $idbarang => $item) {
            $stmt->bindParam(':idpengadaan', $idpengadaan, PDO::PARAM_INT);
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->bindParam(':idbarang', $idbarang, PDO::PARAM_INT);
            $stmt->bindValue(':qty', $item['qty'], PDO::PARAM_INT);
            $stmt->bindValue(':harga', $item['harga'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        }
        return true;
    }

    // --- 3. EXECUTE RETUR ---
    public function checkoutRetur($iduser, $idpenerimaan, $cart) {
        $this->conn->query("SET @idretur = 0");
        $stmt = $this->conn->prepare("CALL sp_simpan_retur(:idpenerimaan, :iduser, :iddetail, :qty, :alasan, @idretur)");

        foreach ($cart as $iddetail => $item) {
            $stmt->bindParam(':idpenerimaan', $idpenerimaan, PDO::PARAM_INT);
            $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
            $stmt->bindParam(':iddetail', $iddetail, PDO::PARAM_INT);
            $stmt->bindValue(':qty', $item['qty'], PDO::PARAM_INT);
            $stmt->bindValue(':alasan', $item['alasan']);
            $stmt->execute();
            $stmt->closeCursor();
        }
        return true;
    }
}
?>