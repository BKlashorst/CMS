<?php
include 'config.php';
include 'database.php';

$db = new Database();
$currentSlug = $_GET['slug'] ?? 'home';
$pages = $db->getPublishedPages();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CMS</title>
        <link rel="stylesheet" href="public/css/style-header.css">
        <link rel="stylesheet" href="public/css/style-admin.css">
        <!-- TinyMCE WYSIWYG Editor -->
        <script src="https://cdn.tiny.cloud/1/a7gze85j3ty0nll07fc3t878r13t6sck2p3025emedn3xi0u/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize TinyMCE for all textarea elements with class 'wysiwyg-editor'
                tinymce.init({
                    selector: '.wysiwyg-editor',
                    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                    tinycomments_mode: 'embedded',
                    tinycomments_author: 'Author name',
                    mergetags_list: [
                        { value: 'First.Name', title: 'First Name' },
                        { value: 'Email', title: 'Email' },
                    ],
                    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
                });
            });
        </script>
    </head>
    <body>
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>BK Admin</h2>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="pages.php">
                        <i class="fas fa-file-alt"></i> Pages
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="posts.php">
                        <i class="fas fa-file-alt"></i> Posts
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="media.php">
                        <i class="fas fa-file-alt"></i> Media
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="settings.php">
                        <i class="fas fa-file-alt"></i> Settings
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-link" href="users.php">
                        <i class="fas fa-file-alt"></i> Users
                    </a>
                </li>
            </ul>
        </div>
    </body>
</html>
