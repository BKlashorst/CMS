<?php
// Prevent multiple inclusion
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'bk-admin');
    
    // Base URL of your CMS
    define('BASE_URL', 'http://localhost/Fontys/Personal%20project/CMS/portfolio_test/');

    // reCAPTCHA configuration
    define('RECAPTCHA_SITE_KEY', 'YOUR_RECAPTCHA_SITE_KEY'); // Vervang dit met je reCAPTCHA site key
    define('RECAPTCHA_SECRET_KEY', 'YOUR_RECAPTCHA_SECRET_KEY'); // Vervang dit met je reCAPTCHA secret key

    // Other configuration settings
    define('SITE_URL', 'http://localhost/Fontys/Personal project/CMS/portfolio_test');
    define('UPLOAD_DIR', 'uploads');
}