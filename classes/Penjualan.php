<?php


class Penjualan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch active items alongside real-time stock from fn_get_current_stock
    public function getActiveMargin() {
    return $this->conn->query("SELECT `no` AS idmargin_penjualan, persen FROM view_active_margin")->fetch();
}

public function getAvailableBarang() {
    return $this->conn->query("SELECT * FROM view_pos_barang ORDER BY nama ASC")->fetchAll();
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