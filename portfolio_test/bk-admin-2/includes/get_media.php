<?php
include '../includes/config.php';
include '../includes/database.php';

header('Content-Type: application/json');

$db = new Database();

try {
    // Get all media files from the database
    $media = $db->query(
        "SELECT medi_id, medi_name as filename, medi_url as filepath 
         FROM media 
         WHERE medi_type LIKE 'image/%' 
         ORDER BY medi_uploaded DESC"
    )->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($media);
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} 