<?php


class Penjualan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch active items alongside real-time stock from fn_get_current_stock
    public function getAvailableBarang() {
        $query = "SELECT 
                    `no` AS idbarang, 
                    nama, 
                    jenis, 
                    nama_satuan, 
                    harga AS harga_beli,
                    (harga + (harga * COALESCE((
                        SELECT persen FROM margin_penjualan WHERE STATUS = 1 ORDER BY idmargin_penjualan DESC LIMIT 1
                    ), 0) / 100)) AS harga_jual,
                    status, 
                    fn_get_current_stock(`no`) AS stock 
                  FROM view_barang 
                  WHERE status = 'Active' 
                  ORDER BY nama ASC";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch current active margin settings
    public function getActiveMargin() {
        $query = "SELECT idmargin_penjualan, persen 
                  FROM margin_penjualan 
                  WHERE STATUS = 1 
                  ORDER BY idmargin_penjualan DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Execute Multi-Item Checkout via Stored Procedure (sp_checkout_penjualan_multi)
    public function checkoutMulti($iduser, $idmargin, array $items) {
        $jsonItems = json_encode($items);
        $query = "CALL sp_checkout_penjualan_multi(:iduser, :idmargin, :items)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
        $stmt->bindParam(':idmargin', $idmargin, PDO::PARAM_INT);
        $stmt->bindParam(':items', $jsonItems);

        return $stmt->execute();
    }
}
?>