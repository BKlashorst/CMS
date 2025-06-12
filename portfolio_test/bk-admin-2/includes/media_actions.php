<?php
include 'config.php';
include 'database.php';

// Create database instance
$database = new Database();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'upload':
            uploadMedia($database);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'delete') {
        deleteMedia($database);
    }
}

function uploadMedia($database) {
    if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../media.php?error=upload_failed');
        exit;
    }
    
    $file = $_FILES['media_file'];
    $title = $_POST['title'] ?? '';
    
    // Check file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        header('Location: ../media.php?error=invalid_type');
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;
    $file_url = 'uploads/' . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        header('Location: ../media.php?error=move_failed');
        exit;
    }
    
    try {
        $database->query(
            "INSERT INTO media (medi_name, medi_url, medi_type, medi_uploaded) VALUES (?, ?, ?, NOW())",
            [$title, $filename, $file['type']]
        );
        header('Location: ../media.php?success=media_uploaded');
    } catch (PDOException $e) {
        // Delete the uploaded file if database insert fails
        unlink($filepath);
        header('Location: ../media.php?error=db_failed');
    }
}

function deleteMedia($database) {
    $media_id = $_GET['id'] ?? '';
    
    if (empty($media_id)) {
        header('Location: ../media.php?error=missing_id');
        exit;
    }
    
    try {
        // Get the filename before deleting
        $result = $database->query(
            "SELECT medi_url FROM media WHERE medi_id = ?",
            [$media_id]
        );
        
        if ($result && $result->rowCount() > 0) {
            $media = $result->fetch(PDO::FETCH_ASSOC);
            $filepath = '../uploads/' . $media['medi_url'];
            
            // Delete the file if it exists
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        
        // Delete from database
        $database->query(
            "DELETE FROM media WHERE medi_id = ?",
            [$media_id]
        );
        
        header('Location: ../media.php?success=media_deleted');
    } catch (PDOException $e) {
        header('Location: ../media.php?error=delete_failed');
    }
} 