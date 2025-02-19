<?php
include '../includes/config.php';
include '../includes/database.php';
include '../includes/header.php';

$db = new Database();

// Get all pages
$pages = $db->query("SELECT * FROM pages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Page Management</title>
    <link rel="stylesheet" href="../public/css/admin.css">
</head>
<body>
    <main>
        <h1>Pages</h1>
        <a href="edit-page.php" class="button">Create New Page</a>
        
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
                    <td><?php echo $page['status']; ?></td>
                    <td>
                        <a href="edit-page.php?id=<?php echo $page['id']; ?>">Edit</a>
                        <a href="../page.php?id=<?php echo $page['id']; ?>" target="_blank">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>