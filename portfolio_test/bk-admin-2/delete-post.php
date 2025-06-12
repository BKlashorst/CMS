<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';

$db = new Database();
// Check if the ID is set in the URL
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Fetch the post content
    $postContent = $db->getPostContent($postId);

    // Check if the post exists
    if ($postContent) {
        // Delete the post content
        $db->query("DELETE FROM postcontent WHERE post_id = ?", [$postId]);
        $db->query("DELETE FROM post WHERE post_id = ?", [$postId]);

        // Redirect to the posts page
        header("Location: posts.php");
        exit();
    } else {
        echo "Post not found.";
    }
} else {
    echo "No ID provided.";
}