<?php
// Prevent multiple inclusion
if (!defined('HEADER_INCLUDED')) {
    define('HEADER_INCLUDED', true);

    // Include required files if not already included
    if (!defined('DB_HOST')) {
        include __DIR__ . '/config.php';
    }

    if (!class_exists('Database')) {
        include __DIR__ . '/database.php';
    }

    $db = new Database();
    $currentSlug = $_GET['page_slug'] ?? 'home';
    $pages = $db->getPublishedPages();
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Portfolio</title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="bk-admin-2/public/css/style-footer.css">
        <!-- reCAPTCHA JavaScript -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>

    <body>
        <header>
            <nav>
                <div>
                    <div class="header-menu">
                        <a href="page.php?page_slug=portfolio" class="header-item">Portfolio</a>
                        <a href="page.php?page_slug=over-mij" class="header-item">Over mij</a>
                    </div>
                </div>
                <div class="header-logo">
                    <a href="index.php?page_slug=home"><img src="/Fontys/Personal%20project/CMS/portfolio_test/bk-admin-2/uploads/1744200050_bk-logo.png" alt=""></a>
                </div>
                <div>
                    <div class="header-menu">
                        <a href="page.php?page_slug=contact" class="btn pri-cta">Contact</a>
                    </div>
                </div>
            </nav>
        </header>
    <?php
}
    ?>