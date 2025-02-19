<?php
include '../includes/config.php';
include '../includes/database.php';
include '../includes/header.php';

$db = new Database();

// Define available block types
$blockTypes = [
    'text' => 'Text Block',
    'hero' => 'Hero Section',
    'image' => 'Image Block',
    'two_columns' => 'Two Columns'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle page save
    $title = $_POST['title'];
    $slug = strtolower(str_replace(' ', '-', $title));
    
    // Insert or update page
    if (empty($_POST['page_id'])) {
        $db->query(
            "INSERT INTO pages (title, slug, status) VALUES (?, ?, ?)",
            [$title, $slug, 'draft']
        );
        $pageId = $db->$connection->lastInsertId();
    } else {
        $pageId = $_POST['page_id'];
        $db->query(
            "UPDATE pages SET title = ?, slug = ? WHERE id = ?",
            [$title, $slug, $pageId]
        );
        
        // Clear existing content
        $db->query("DELETE FROM page_contents WHERE page_id = ?", [$pageId]);
    }
    
    // Handle content blocks
    foreach ($_POST['blocks'] as $index => $block) {
        $settings = isset($block['settings']) ? json_encode($block['settings']) : null;
        $db->query(
            "INSERT INTO page_contents (page_id, content_type, content, sort_order, block_type, settings) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [$pageId, 'content', $block['content'], $index, $block['type'], $settings]
        );
    }
}

$page = null;
if (isset($_GET['id'])) {
    $page = $db->query(
        "SELECT * FROM pages WHERE id = ?", 
        [$_GET['id']]
    )->fetch(PDO::FETCH_ASSOC);
    
    $contents = $db->query(
        "SELECT * FROM page_contents WHERE page_id = ? ORDER BY sort_order",
        [$_GET['id']]
    )->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Page</title>
    <link rel="stylesheet" href="../public/css/style-admin.css">
</head>
<body>
    <main> 
        <h1><?php echo $page ? 'Edit Page' : 'Create New Page'; ?></h1>
        
        <form method="POST">
            <?php if ($page): ?>
                <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo $page ? htmlspecialchars($page['title']) : ''; ?>" required>
            </div>
            
            <div id="content-blocks">
                <?php if ($page && $contents): ?>
                    <?php foreach ($contents as $index => $content): ?>
                        <?php include 'blocks/' . $content['block_type'] . '.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="block-controls">
                <select id="block-type">
                    <?php foreach ($blockTypes as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" id="add-block">Add Content Block</button>
            </div>
            
            <button type="submit">Save Page</button>
        </form>
    </main>

    <script>
        document.getElementById('add-block').addEventListener('click', function() {
            const blockType = document.getElementById('block-type').value;
            addBlock(blockType);
        });

        function addBlock(type) {
            const blocks = document.getElementById('content-blocks');
            const index = blocks.children.length;
            
            const block = document.createElement('div');
            block.className = `content-block ${type}-block`;
            block.draggable = true;
            
            switch(type) {
                case 'hero':
                    block.innerHTML = `
                        <input type="hidden" name="blocks[${index}][type]" value="hero1">
                        <div class="block-header">
                            <h3>Hero 1</h3>
                            <button type="button" class="remove-block">Remove</button>
                        </div>
                        <div class="form-group">
                            <label>Heading</label>
                            <input type="text" name="blocks[${index}][content]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Background Image URL</label>
                            <input type="text" name="blocks[${index}][settings][bg_image]" class="form-control">
                        </div>
                    `;
                    break;
                    
                case 'two_columns':
                    block.innerHTML = `
                        <input type="hidden" name="blocks[${index}][type]" value="two_columns">
                        <div class="block-header">
                            <h3>Two Columns</h3>
                            <button type="button" class="remove-block">Remove</button>
                        </div>
                        <div class="two-columns-block">
                            <div class="form-group">
                                <label>Left Column</label>
                                <textarea name="blocks[${index}][settings][left_content]" rows="4"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Right Column</label>
                                <textarea name="blocks[${index}][settings][right_content]" rows="4"></textarea>
                            </div>
                        </div>
                    `;
                    break;
                    
                default: // text block
                    block.innerHTML = `
                        <input type="hidden" name="blocks[${index}][type]" value="text">
                        <div class="block-header">
                            <h3>Text Block</h3>
                            <button type="button" class="remove-block">Remove</button>
                        </div>
                        <textarea name="blocks[${index}][content]" rows="4"></textarea>
                    `;
            }
            
            blocks.appendChild(block);
        }

        // Handle remove buttons
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-block')) {
                e.target.closest('.content-block').remove();
            }
        });
    </script>
</body>
</html>