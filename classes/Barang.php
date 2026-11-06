<?php


class Barang {
    private $conn;

    public function __construct($db) {
        $this->conn = $db; // INI KONEKSI DBNYA
    }

    // Read all items using the v_master_barang View
    

public function getAll() {
    
    $query = "SELECT * FROM view_barang ORDER BY `no` DESC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}

    // Read a single item for pre-filling edit forms
    public function getById($id) {
        $query = "SELECT * FROM barang WHERE idbarrang = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Helper: Fetch units (satuan) for drop-down selection
    public function getSatuans() {
        $query = "SELECT idsatuan, nama_satuan FROM satuan WHERE STATUS = 1 ORDER BY nama_satuan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Create new item
    public function create($jenis, $nama, $idsatuan, $status, $harga) {
        $query = "INSERT INTO barang (jenis, nama, idsatuan, STATUS, harga) 
                  VALUES (:jenis, :nama, :idsatuan, :status, :harga)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':jenis', $jenis);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':idsatuan', $idsatuan, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':harga', $harga, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Update existing item
    public function update($id, $jenis, $nama, $idsatuan, $status, $harga) {
        $query = "UPDATE barang 
                  SET jenis = :jenis, nama = :nama, idsatuan = :idsatuan, STATUS = :status, harga = :harga 
                  WHERE idbarrang = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':jenis', $jenis);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':idsatuan', $idsatuan, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':harga', $harga, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete item
    public function delete($id) {
        $query = "DELETE FROM barang WHERE idbarrang = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>