<?php
include 'config.php';
include 'database.php';

// Create database instance
$database = new Database();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addUser($database);
            break;
        case 'edit':
            editUser($database);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get':
            getUser($database);
            break;
        case 'delete':
            deleteUser($database);
            break;
    }
}

function addUser($database) {
    $username = $_POST['user_name'] ?? '';
    $email = $_POST['user_mail'] ?? '';
    $password = $_POST['user_password'] ?? '';
    $role_id = $_POST['role_id'] ?? '';
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($role_id)) {
        header('Location: ../users.php?error=missing_fields');
        exit;
    }
    
    try {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $database->query(
            "INSERT INTO user (user_name, user_mail, user_password, role_id) VALUES (?, ?, ?, ?)",
            [$username, $email, $hashed_password, $role_id]
        );
        header('Location: ../users.php?success=user_added');
    } catch (PDOException $e) {
        header('Location: ../users.php?error=add_failed');
    }
}

function editUser($database) {
    $user_id = $_POST['user_id'] ?? '';
    $username = $_POST['user_name'] ?? '';
    $email = $_POST['user_mail'] ?? '';
    $role_id = $_POST['role_id'] ?? '';
    $password = $_POST['user_password'] ?? '';
    
    // Validate input
    if (empty($user_id) || empty($username) || empty($email) || empty($role_id)) {
        header('Location: ../users.php?error=missing_fields');
        exit;
    }
    
    try {
        if (!empty($password)) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $database->query(
                "UPDATE user SET user_name = ?, user_mail = ?, user_password = ?, role_id = ? WHERE user_id = ?",
                [$username, $email, $hashed_password, $role_id, $user_id]
            );
        } else {
            // Update without changing password
            $database->query(
                "UPDATE user SET user_name = ?, user_mail = ?, role_id = ? WHERE user_id = ?",
                [$username, $email, $role_id, $user_id]
            );
        }
        header('Location: ../users.php?success=user_updated');
    } catch (PDOException $e) {
        header('Location: ../users.php?error=update_failed');
    }
}

function getUser($database) {
    $user_id = $_GET['id'] ?? '';
    
    if (empty($user_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing user ID']);
        exit;
    }
    
    try {
        $user = $database->query(
            "SELECT user_id, user_name, user_mail, role_id FROM user WHERE user_id = ?",
            [$user_id]
        )->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

function deleteUser($database) {
    $user_id = $_GET['id'] ?? '';
    
    if (empty($user_id)) {
        header('Location: ../users.php?error=missing_id');
        exit;
    }
    
    try {
        $database->query(
            "DELETE FROM user WHERE user_id = ?",
            [$user_id]
        );
        header('Location: ../users.php?success=user_deleted');
    } catch (PDOException $e) {
        header('Location: ../users.php?error=delete_failed');
    }
} 