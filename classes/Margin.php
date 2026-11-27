<?php
class Margin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllMargins() {
    return $this->conn->query("SELECT `no` AS idmargin_penjualan, persen, created_by, created_at, status FROM view_margin ORDER BY `no` DESC")->fetchAll();
}

public function getActiveMargin() {
    return $this->conn->query("SELECT `no` AS idmargin_penjualan, persen FROM view_active_margin")->fetch();
}

    public function createMargin($persen, $setActive = false) {
        $stmt = $this->conn->prepare("INSERT INTO margin_penjualan (persen, STATUS) VALUES (:persen, 0)");
        $stmt->bindValue(':persen', $persen);
        $stmt->execute();
        $newId = (int)$this->conn->lastInsertId();

        if ($setActive && $newId > 0) {
            $this->setActiveMargin($newId);
        }
        return true;
    }

    public function setActiveMargin($idmargin) {
        $stmt = $this->conn->prepare("CALL sp_set_active_margin(:idmargin)");
        $stmt->bindParam(':idmargin', $idmargin, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
        return true;
    }
}
?>