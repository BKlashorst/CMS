<?php
include 'config.php';
include 'database.php';

header('Content-Type: application/json');

$db = new Database();

try {
    $posts = $db->query(
        "SELECT post_id, post_name FROM post WHERE post_status = 1 ORDER BY post_name"
    )->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($posts);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch posts']);
} 