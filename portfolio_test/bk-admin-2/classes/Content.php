<?php
class Content {
    private $conn;
    private $table_name = "content";

    public $cont_id;
    public $cont_name;
    public $cont_content;
    public $cont_order;
    public $cont_block;
    public $cont_settings;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create new content
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET cont_name=:name, cont_content=:content, 
                    cont_order=:order, cont_block=:block, 
                    cont_settings=:settings";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->cont_name = htmlspecialchars(strip_tags($this->cont_name));
        $this->cont_block = htmlspecialchars(strip_tags($this->cont_block));
        // Don't strip tags from content and settings as they may contain HTML
        $this->cont_content = $this->cont_content;
        $this->cont_settings = $this->cont_settings;
        $this->cont_order = (int)$this->cont_order;

        // Bind values
        $stmt->bindParam(":name", $this->cont_name);
        $stmt->bindParam(":content", $this->cont_content);
        $stmt->bindParam(":order", $this->cont_order);
        $stmt->bindParam(":block", $this->cont_block);
        $stmt->bindParam(":settings", $this->cont_settings);

        if($stmt->execute()) {
            $this->cont_id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Read single content
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE cont_id = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->cont_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->cont_name = $row['cont_name'];
        $this->cont_content = $row['cont_content'];
        $this->cont_order = $row['cont_order'];
        $this->cont_block = $row['cont_block'];
        $this->cont_settings = $row['cont_settings'];
    }

    // Update content
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET cont_name = :name,
                    cont_content = :content,
                    cont_order = :order,
                    cont_block = :block,
                    cont_settings = :settings
                WHERE cont_id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->cont_name = htmlspecialchars(strip_tags($this->cont_name));
        $this->cont_block = htmlspecialchars(strip_tags($this->cont_block));
        // Don't strip tags from content and settings
        $this->cont_content = $this->cont_content;
        $this->cont_settings = $this->cont_settings;
        $this->cont_order = (int)$this->cont_order;
        $this->cont_id = htmlspecialchars(strip_tags($this->cont_id));

        // Bind values
        $stmt->bindParam(":name", $this->cont_name);
        $stmt->bindParam(":content", $this->cont_content);
        $stmt->bindParam(":order", $this->cont_order);
        $stmt->bindParam(":block", $this->cont_block);
        $stmt->bindParam(":settings", $this->cont_settings);
        $stmt->bindParam(":id", $this->cont_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete content
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE cont_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->cont_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Add media to content
    public function addMedia($media_id) {
        $query = "INSERT INTO mediacontent (medi_id, cont_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $media_id);
        $stmt->bindParam(2, $this->cont_id);
        return $stmt->execute();
    }

    // Remove media from content
    public function removeMedia($media_id) {
        $query = "DELETE FROM mediacontent WHERE medi_id = ? AND cont_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $media_id);
        $stmt->bindParam(2, $this->cont_id);
        return $stmt->execute();
    }

    // Get all media associated with this content
    public function getMedia() {
        $query = "SELECT m.* FROM media m
                JOIN mediacontent mc ON m.medi_id = mc.medi_id
                WHERE mc.cont_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->cont_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 