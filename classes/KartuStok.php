<?php
// classes/KartuStok.php

class KartuStok {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch stock movement logs directly from view_kartu_stok
    public function getLogs($filterBarangId = 0) {
        if ($filterBarangId > 0) {
            $query = "SELECT * FROM view_kartu_stok WHERE idbarang = :idbarang ORDER BY `no` DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':idbarang', $filterBarangId, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM view_kartu_stok ORDER BY `no` DESC";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch active items for the filter dropdown
    public function getBarangList() {
        $query = "SELECT `no` AS idbarang, nama FROM view_barang ORDER BY nama ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>