<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';

$db = new Database();

// Get all pages
$pages = $db->query(
    "SELECT page_id as id, page_name as title, page_slug as slug, 
            CASE WHEN page_status = 1 THEN 'published' ELSE 'draft' END as status 
     FROM page 
     ORDER BY page_date DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main>
        <div class="header-actions">
            <h1>Pages</h1>
            <a href="edit-pages.php" class="btn-primary">
                <i class="fas fa-plus"></i> Create a new page
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($page['title']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $page['status']; ?>">
                                    <?php echo ucfirst($page['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button onclick="editPage(<?php echo $page['id']; ?>)" class="btn-edit">
                                    Edit
                                </button>
                                <button onclick="deletePage(<?php echo $page['id']; ?>)" class="btn-delete">
                                    Delete
                                </button>
                                <a href="../page.php?slug=<?php echo $page['slug']; ?>" target="_blank" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="pageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="pageModalTitle">Edit Page</h2>
            <form id="pageForm" action="includes/page_actions.php" method="POST">
                <input type="hidden" id="action" name="action" value="edit">
                <input type="hidden" id="page_id" name="page_id">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                    </select>
                </div>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        function editPage(pageId) {
            window.location.href = `edit-pages.php?id=${pageId}`;
        }

        function deletePage(pageId) {
            if (confirm('Are you sure you want to delete this page?')) {
                window.location.href = `includes/page_actions.php?action=delete&id=${pageId}`;
            }
        }
    </script>
</body>
</html>