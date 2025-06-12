<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';
$db = new Database();
// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $pageId = $_GET['id'];

    // Fetch the page content
    $pageContent = $db->getPageContent($pageId);

    // Check if the page exists
    if ($pageContent) {
        // Delete the page content
        $db->query("DELETE FROM pagecontent WHERE page_id = ?", [$pageId]);
        $db->query("DELETE FROM page WHERE page_id = ?", [$pageId]);

        // Redirect to the pages page
        header("Location: pages.php");
        exit();
    } else {
        echo "Page not found.";
    }
} else {
    echo "No ID provided.";
}