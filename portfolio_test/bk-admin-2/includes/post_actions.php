<?php
include '../includes/config.php';
include '../includes/database.php';

header('Content-Type: application/json');

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'get' && isset($_GET['id'])) {
        // Get post data
        $post = $db->query(
            "SELECT post_id as id, post_name as title, post_slug as slug, post_status as status 
             FROM post WHERE post_id = ?",
            [$_GET['id']]
        )->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            echo json_encode(['success' => true, 'post' => $post]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Post not found']);
        }
    } elseif ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        try {
            // First delete related content
            $db->query("DELETE FROM postcontent WHERE post_id = ?", [$_GET['id']]);

            // Then delete the post
            $db->query("DELETE FROM post WHERE post_id = ?", [$_GET['id']]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'edit' && isset($_POST['post_id'])) {
        try {
            $db->query(
                "UPDATE post SET post_name = ?, post_slug = ?, post_status = ? WHERE post_id = ?",
                [$_POST['title'], $_POST['slug'], $_POST['status'], $_POST['post_id']]
            );
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    }
} 