<?php
// Prevent any output before JSON
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'config.php';
include_once 'database.php';

$db = new Database();

// Get all media items
function getMedia() {
    global $db;
    try {
        $media = $db->query("SELECT * FROM media ORDER BY medi_uploaded DESC")->fetchAll(PDO::FETCH_ASSOC);
        
        // Transform the data to match the expected format
        return array_map(function($item) {
            return [
                'filename' => $item['medi_name'],
                'filepath' => $item['medi_url'],
                'filetype' => $item['medi_type']
            ];
        }, $media);
    } catch (Exception $e) {
        error_log("Error fetching media: " . $e->getMessage());
        return [];
    }
}

// If this file is called directly, return JSON response
if (basename($_SERVER['PHP_SELF']) == 'functions.php') {
    header('Content-Type: application/json');
    $media = getMedia();
    echo json_encode($media);
    exit;
}
?>