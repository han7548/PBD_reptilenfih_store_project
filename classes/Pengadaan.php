<?php


class Pengadaan {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllPengadaan() {
        $query = "SELECT * FROM view_pengadaan ORDER BY `no` DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch detailed items inside each Pengadaan header
    public function getPengadaanDetails() {
        $query = "SELECT * FROM view_detail_pengadaan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllPenerimaan() {
        $query = "SELECT * FROM view_penerimaan ORDER BY `no` DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPenerimaanDetails() {
        $query = "SELECT * FROM view_detail_penerimaan";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch Pending Pengadaan orders WITH full detail mapping for JS auto-fill
    public function getPendingPengadaanWithDetails() {
        $query = "SELECT p.`no` AS idpengadaan, p.nama_vendor, dp.idbarang, dp.nama_barang, dp.jumlah_order, dp.harga_satuan 
                  FROM view_pengadaan p
                  JOIN view_detail_pengadaan dp ON p.`no` = dp.no_pengadaan
                  WHERE p.status = 'Pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch active vendors
    public function getActiveVendors() {
        $query = "SELECT `no` AS idvendor, nama_vendor, badan_hukum FROM view_vendor WHERE status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fetch active items (with 'jenis' classification for JS vendor filtering)
    public function getActiveBarang() {
        $query = "SELECT `no` AS idbarang, nama, jenis, harga FROM view_barang WHERE status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createPengadaanMulti($userId, $vendorId, array $items) {
        $jsonItems = json_encode($items);
        $query = "CALL sp_create_pengadaan_multi(:iduser, :idvendor, :items)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':iduser', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':idvendor', $vendorId, PDO::PARAM_INT);
        $stmt->bindParam(':items', $jsonItems);
        return $stmt->execute();
    }

    public function processPenerimaanMulti($idPengadaan, $userId, array $items) {
        $jsonItems = json_encode($items);
        $query = "CALL sp_proses_penerimaan_multi(:idpengadaan, :iduser, :items)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idpengadaan', $idPengadaan, PDO::PARAM_INT);
        $stmt->bindParam(':iduser', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':items', $jsonItems);
        return $stmt->execute();
    }

    public function processRetur($idPenerimaan, $idDetailPenerimaan, $userId, $jumlahRetur, $alasan) {
        $query = "CALL sp_ajukan_retur(:idpenerimaan, :iddetail, :iduser, :jumlah, :alasan)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idpenerimaan', $idPenerimaan, PDO::PARAM_INT);
        $stmt->bindParam(':iddetail', $idDetailPenerimaan, PDO::PARAM_INT);
        $stmt->bindParam(':iduser', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':jumlah', $jumlahRetur, PDO::PARAM_INT);
        $stmt->bindParam(':alasan', $alasan);
        return $stmt->execute();
    }
}
?>