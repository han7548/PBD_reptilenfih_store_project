<?php
// test views
class MasterData {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getViewData($viewName) {
        
        $allowedViews = [
            'view_role', 'view_satuan', 'view_vendor', 
            'view_user', 'view_barang', 'view_margin'
        ];

        if (in_array($viewName, $allowedViews)) {
            $stmt = $this->conn->query("SELECT * FROM $viewName");
            return $stmt->fetchAll();
        }
        return [];
    }
}
?>