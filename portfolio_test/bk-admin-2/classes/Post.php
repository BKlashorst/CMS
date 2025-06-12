<?php
class Post {
    private $conn;
    private $table_name = "post";

    public $post_id;
    public $post_name;
    public $post_slug;
    public $post_status;
    public $post_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new post
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET post_name=:name, post_slug=:slug, 
                    post_status=:status, post_date=:date";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->post_name = htmlspecialchars(strip_tags($this->post_name));
        $this->post_slug = htmlspecialchars(strip_tags($this->post_slug));
        $this->post_status = (int)$this->post_status;
        $this->post_date = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":name", $this->post_name);
        $stmt->bindParam(":slug", $this->post_slug);
        $stmt->bindParam(":status", $this->post_status);
        $stmt->bindParam(":date", $this->post_date);

        if($stmt->execute()) {
            $this->post_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read single post
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE post_id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->post_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->post_name = $row['post_name'];
        $this->post_slug = $row['post_slug'];
        $this->post_status = $row['post_status'];
        $this->post_date = $row['post_date'];
    }

    // Update post
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET post_name = :name,
                    post_slug = :slug,
                    post_status = :status
                WHERE post_id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->post_name = htmlspecialchars(strip_tags($this->post_name));
        $this->post_slug = htmlspecialchars(strip_tags($this->post_slug));
        $this->post_status = (int)$this->post_status;
        $this->post_id = htmlspecialchars(strip_tags($this->post_id));

        // Bind values
        $stmt->bindParam(":name", $this->post_name);
        $stmt->bindParam(":slug", $this->post_slug);
        $stmt->bindParam(":status", $this->post_status);
        $stmt->bindParam(":id", $this->post_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete post
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE post_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->post_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all published posts
    public function getPublished() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE post_status = 1
                ORDER BY post_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add content to post
    public function addContent($content_id) {
        $query = "INSERT INTO postcontent (cont_id, post_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $content_id);
        $stmt->bindParam(2, $this->post_id);
        return $stmt->execute();
    }

    // Remove content from post
    public function removeContent($content_id) {
        $query = "DELETE FROM postcontent WHERE cont_id = ? AND post_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $content_id);
        $stmt->bindParam(2, $this->post_id);
        return $stmt->execute();
    }

    // Get all content associated with this post
    public function getContent() {
        $query = "SELECT c.* FROM content c
                JOIN postcontent pc ON c.cont_id = pc.cont_id
                WHERE pc.post_id = ?
                ORDER BY c.cont_order";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->post_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 