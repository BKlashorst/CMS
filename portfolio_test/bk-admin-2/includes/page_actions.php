<?php
include 'config.php';
include 'database.php';

// Create database instance
$database = new Database();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'edit':
            editPage($database);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'delete') {
        deletePage($database);
    }
}

function editPage($database) {
    $page_id = $_POST['page_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $slug = $_POST['slug'] ?? '';
    $status = $_POST['status'] ?? '';
    
    // Validate input
    if (empty($page_id) || empty($title) || empty($slug)) {
        header('Location: ../pages.php?error=missing_fields');
        exit;
    }
    
    try {
        $database->query(
            "UPDATE Page SET page_name = ?, page_slug = ?, page_status = ? WHERE page_id = ?",
            [$title, $slug, $status, $page_id]
        );
        header('Location: ../pages.php?success=page_updated');
    } catch (PDOException $e) {
        header('Location: ../pages.php?error=update_failed');
    }
}

function deletePage($database) {
    $page_id = $_GET['id'] ?? '';
    
    if (empty($page_id)) {
        header('Location: ../pages.php?error=missing_id');
        exit;
    }
    
    try {
        $database->query(
            "DELETE FROM page WHERE page_id = ?",
            [$page_id]
        );
        header('Location: ../pages.php?success=page_deleted');
    } catch (PDOException $e) {
        header('Location: ../pages.php?error=delete_failed');
    }
} 