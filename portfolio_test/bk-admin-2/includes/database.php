<?php
// Prevent multiple inclusion
if (!class_exists('Database')) {
    class Database {
        private $connection;
        
        public function __construct() {
            try {
                $this->connection = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASS
                );
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        
        public function query($sql, $params = []) {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }

        public function getPublishedPages() {
            return $this->query(
                "SELECT page_id as id, page_name as title, page_slug as slug 
                 FROM page 
                 WHERE page_status = 1 
                 ORDER BY page_name"
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getPublishedPosts() {
            return $this->query(
                "SELECT post_id as id, post_name as title, post_slug as slug 
                 FROM post 
                 WHERE post_status = 1 
                 ORDER BY post_date DESC"
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getPageContent($pageId) {
            return $this->query(
                "SELECT c.* 
                 FROM pagecontent pc
                 JOIN content c ON pc.cont_id = c.cont_id
                 WHERE pc.page_id = ?
                 ORDER BY c.cont_order ASC",
                [$pageId]
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getPostContent($postId) {
            return $this->query(
                "SELECT c.* 
                 FROM postcontent pc
                 JOIN content c ON pc.cont_id = c.cont_id
                 WHERE pc.post_id = ?
                 ORDER BY c.cont_order ASC",
                [$postId]
            )->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} 