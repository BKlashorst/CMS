<?php
class Page {
    private $conn;
    private $table_name = "page";

    public $page_id;
    public $page_name;
    public $page_slug;
    public $page_status;
    public $page_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new page
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET page_name=:name, page_slug=:slug, 
                    page_status=:status, page_date=:date";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->page_name = htmlspecialchars(strip_tags($this->page_name));
        $this->page_slug = htmlspecialchars(strip_tags($this->page_slug));
        $this->page_status = (int)$this->page_status;
        $this->page_date = date('Y-m-d H:i:s');

        // Bind values
        $stmt->bindParam(":name", $this->page_name);
        $stmt->bindParam(":slug", $this->page_slug);
        $stmt->bindParam(":status", $this->page_status);
        $stmt->bindParam(":date", $this->page_date);

        if($stmt->execute()) {
            $this->page_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read single page
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE page_id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->page_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->page_name = $row['page_name'];
        $this->page_slug = $row['page_slug'];
        $this->page_status = $row['page_status'];
        $this->page_date = $row['page_date'];
    }

    // Update page
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET page_name = :name,
                    page_slug = :slug,
                    page_status = :status
                WHERE page_id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->page_name = htmlspecialchars(strip_tags($this->page_name));
        $this->page_slug = htmlspecialchars(strip_tags($this->page_slug));
        $this->page_status = (int)$this->page_status;
        $this->page_id = htmlspecialchars(strip_tags($this->page_id));

        // Bind values
        $stmt->bindParam(":name", $this->page_name);
        $stmt->bindParam(":slug", $this->page_slug);
        $stmt->bindParam(":status", $this->page_status);
        $stmt->bindParam(":id", $this->page_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete page
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE page_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->page_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all published pages
    public function getPublished() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE page_status = 1
                ORDER BY page_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add content to page
    public function addContent($content_id) {
        $query = "INSERT INTO pagecontent (cont_id, page_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $content_id);
        $stmt->bindParam(2, $this->page_id);
        return $stmt->execute();
    }

    // Remove content from page
    public function removeContent($content_id) {
        $query = "DELETE FROM pagecontent WHERE cont_id = ? AND page_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $content_id);
        $stmt->bindParam(2, $this->page_id);
        return $stmt->execute();
    }

    // Get all content associated with this page
    public function getContent() {
        $query = "SELECT c.* FROM content c
                JOIN pagecontent pc ON c.cont_id = pc.cont_id
                WHERE pc.page_id = ?
                ORDER BY c.cont_order";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->page_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 