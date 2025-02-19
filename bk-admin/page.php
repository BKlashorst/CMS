<?php
include 'includes/config.php';
include 'includes/database.php';

$db = new Database();

// Get the page by slug or ID
$page = null;
$contents = null;

if (isset($_GET['slug'])) {
    $page = $db->query(
        "SELECT * FROM pages WHERE slug = ?", 
        [$_GET['slug']]
    )->fetch(PDO::FETCH_ASSOC);
} elseif (isset($_GET['id'])) {
    $page = $db->query(
        "SELECT * FROM pages WHERE id = ?", 
        [$_GET['id']]
    )->fetch(PDO::FETCH_ASSOC);
}

if ($page) {
    $contents = $db->query(
        "SELECT * FROM page_contents WHERE page_id = ? ORDER BY sort_order",
        [$page['id']]
    )->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page ? htmlspecialchars($page['title']) : 'Page Not Found'; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="main">
        <?php if ($page): ?>
            
            <?php if ($contents): ?>
    <?php foreach ($contents as $content): ?>
        <?php 
        $settings = json_decode($content['settings'], true) ?? [];
        switch($content['block_type']):
            case 'hero1': ?>
                <div class="hero-1" style="background-image: url('<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>')">
                    <h1><?php echo htmlspecialchars($content['content']); ?></h1>
                </div>
                <?php break;
                
            case 'two_columns': ?>
                <div class="two-columns">
                    <div class="column">
                        <?php echo nl2br(htmlspecialchars($settings['left_content'] ?? '')); ?>
                    </div>
                    <div class="column">
                        <?php echo nl2br(htmlspecialchars($settings['right_content'] ?? '')); ?>
                    </div>
                </div>
                <?php break;
                
            default: ?>
                <div class="content-block">
                    <?php echo nl2br(htmlspecialchars($content['content'])); ?>
                </div>
        <?php endswitch; ?>
    <?php endforeach; ?>
<?php endif; ?>
        <?php else: ?>
            <h1>Page Not Found</h1>
            <p>The requested page could not be found.</p>
        <?php endif; ?>
    </div>
</body>
</html> 