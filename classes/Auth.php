<?php

class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        // Direct JOIN between USER and role tables instead of using the view
        $query = "SELECT u.iduser, u.username, u.password, r.nama_role 
                  FROM USER u 
                  JOIN role r ON u.idrole = r.idrole 
                  WHERE u.username = :username LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            if ($password === $row['password']) {
                session_start();
                $_SESSION['user_id'] = $row['iduser'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['nama_role'];
                return true;
            }
        }
        return false;
    }

    public static function checkAccess($allowed_roles) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
            header("Location: unauthorized.php");
            exit();
        }
    }
}
?>