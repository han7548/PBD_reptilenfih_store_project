<?php
// classes/User.php

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all users using your aliased view_user
    public function getAll() {
        $query = "SELECT `no`, username, nama_role FROM view_user ORDER BY `no` DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Read a single user record for pre-filling edit forms
    public function getById($id) {
        $query = "SELECT iduser, username, idrole FROM USER WHERE iduser = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Fetch roles list for select dropdown
    public function getRoles() {
        $query = "SELECT `no` AS idrole, nama_role FROM view_role ORDER BY `no` ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Create a new user account
    public function create($username, $password, $idrole) {
        $query = "INSERT INTO USER (username, PASSWORD, idrole) VALUES (:username, :password, :idrole)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':idrole', $idrole, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Update existing user (updates password only if a new one is provided)
    public function update($iduser, $username, $idrole, $password = null) {
        if (!empty($password)) {
            $query = "UPDATE USER SET username = :username, PASSWORD = :password, idrole = :idrole WHERE iduser = :iduser";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $password);
        } else {
            $query = "UPDATE USER SET username = :username, idrole = :idrole WHERE iduser = :iduser";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':idrole', $idrole, PDO::PARAM_INT);
        $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Delete user account
    public function delete($iduser) {
        $query = "DELETE FROM USER WHERE iduser = :iduser";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':iduser', $iduser, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>