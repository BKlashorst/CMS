<?php
class User {
    private $conn;
    private $table_name = "user";

    public $user_id;
    public $user_name;
    public $user_mail;
    public $user_password;
    public $role_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET user_name=:name, user_mail=:mail, 
                    user_password=:password, role_id=:role_id";

        $stmt = $this->conn->prepare($query);

        // Sanitize and hash
        $this->user_name = htmlspecialchars(strip_tags($this->user_name));
        $this->user_mail = htmlspecialchars(strip_tags($this->user_mail));
        $this->user_password = password_hash($this->user_password, PASSWORD_BCRYPT);
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));

        // Bind values
        $stmt->bindParam(":name", $this->user_name);
        $stmt->bindParam(":mail", $this->user_mail);
        $stmt->bindParam(":password", $this->user_password);
        $stmt->bindParam(":role_id", $this->role_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read single user
    public function readOne() {
        $query = "SELECT u.*, r.role_name 
                FROM " . $this->table_name . " u
                LEFT JOIN Role r ON u.role_id = r.role_id
                WHERE u.user_id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->user_name = $row['user_name'];
        $this->user_mail = $row['user_mail'];
        $this->role_id = $row['role_id'];
    }

    // Update user
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET user_name = :name,
                    user_mail = :mail,
                    role_id = :role_id
                WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->user_name = htmlspecialchars(strip_tags($this->user_name));
        $this->user_mail = htmlspecialchars(strip_tags($this->user_mail));
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));

        // Bind values
        $stmt->bindParam(":name", $this->user_name);
        $stmt->bindParam(":mail", $this->user_mail);
        $stmt->bindParam(":role_id", $this->role_id);
        $stmt->bindParam(":id", $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Change password
    public function changePassword($new_password) {
        $query = "UPDATE " . $this->table_name . "
                SET user_password = :password
                WHERE user_id = :id";

        $stmt = $this->conn->prepare($query);

        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":id", $this->user_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Check if email exists
    public function emailExists() {
        $query = "SELECT user_id, user_name, user_password, role_id
                FROM " . $this->table_name . "
                WHERE user_mail = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_mail);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->user_id = $row['user_id'];
            $this->user_name = $row['user_name'];
            $this->user_password = $row['user_password'];
            $this->role_id = $row['role_id'];
            return true;
        }
        return false;
    }

    // Verify password
    public function verifyPassword($password) {
        return password_verify($password, $this->user_password);
    }
} 