<?php
session_start();
require_once 'bk-admin-2/includes/config.php';
require_once 'bk-admin-2/includes/database.php';
require_once 'bk-admin-2/includes/Mailer.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?status=error');
        exit;
    }

    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $recipient_email = $_POST['recipient_email'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($message) || empty($recipient_email)) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?status=error');
        exit;
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?status=error');
        exit;
    }

    // Send email using PHPMailer
    $mailer = new Mailer();
    if ($mailer->sendContactForm($recipient_email, $name, $email, $phone, $message)) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?status=success');
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?status=error');
    }
    exit;
}

// If not POST request, redirect to home
header('Location: index.php');
exit; 