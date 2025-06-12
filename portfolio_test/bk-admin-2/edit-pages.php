<?php
include 'includes/config.php';
include 'includes/database.php';
include 'includes/admin-header.php';
$db = new Database();

// Debug: Log POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST Data: ' . print_r($_POST, true));
}

// Define available block types
$blockTypes = [
    'text' => 'Text Block',
    'hero1' => 'Hero 1',
    'text-img' => 'Text & Image',
    'image' => 'Image',
    'ovz-klein' => 'Klein Overzicht',
    'ovz-groot' => 'Groot Overzicht',
    'contact' => 'Contact Formulier',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = strtolower(str_replace(' ', '-', $title));

    // Update page
    if (empty($_POST['page_id'])) {
        $db->query(
            "INSERT INTO page (page_name, page_slug, page_image, page_status) VALUES (?, ?, ?, ?)",
            [$title, $slug, $_POST['page_image'] ?? '', 1]
        );
        $pageId = $db->query("SELECT LAST_INSERT_ID() as id")->fetch(PDO::FETCH_ASSOC)['id'];
    } else {
        $pageId = $_POST['page_id'];
        $db->query(
            "UPDATE page SET page_name = ?, page_slug = ?, page_image = ? WHERE page_id = ?",
            [$title, $slug, $_POST['page_image'] ?? '', $pageId]
        );

        // Clear existing content
        $db->query("DELETE FROM pagecontent WHERE page_id = ?", [$pageId]);
    }

    // Process blocks
    if (isset($_POST['blocks']) && is_array($_POST['blocks'])) {
        foreach ($_POST['blocks'] as $index => $block) {
            if (empty($block['type'])) {
                error_log("Skipping block with no type at index " . $index);
                continue;
            }

            // Skip if no content for blocks other than 'image' and 'ovz-groot'
            if ($block['type'] !== 'image' && $block['type'] !== 'ovz-groot' && !isset($block['content'])) {
                error_log("Skipping block with no content at index " . $index);
                continue;
            }

            // Prepare settings array
            $settings = [];
            if (isset($block['settings'])) {
                if (isset($block['settings']['bg_image'])) {
                    $settings['bg_image'] = $block['settings']['bg_image'];
                }
                // Handle image_left setting for text-img blocks
                if ($block['type'] === 'text-img') {
                    $settings['image_left'] = isset($block['settings']['image_left']) ? true : false;
                }
                // Handle selected_posts for ovz-klein blocks
                if ($block['type'] === 'ovz-klein' && isset($block['settings']['selected_posts'])) {
                    $settings['selected_posts'] = $block['settings']['selected_posts'];
                }
                // Handle show_portfolio_button for ovz-klein blocks
                if ($block['type'] === 'ovz-klein') {
                    $settings['show_portfolio_button'] = isset($block['settings']['show_portfolio_button']) ? true : false;
                }
                // Handle recipient_email for contact blocks
                if ($block['type'] === 'contact' && isset($block['settings']['recipient_email'])) {
                    $settings['recipient_email'] = $block['settings']['recipient_email'];
                }
            }

            // For image blocks, we don't need content
            $content = ($block['type'] === 'image') ? '' : ($block['content'] ?? '');

            error_log("Processing block: " . print_r($block, true)); // Debug log

            $db->query(
                "INSERT INTO content (cont_name, cont_content, cont_order, cont_block, cont_settings) 
                 VALUES (?, ?, ?, ?, ?)",
                [$title . ' - Block ' . ($index + 1), $content, $index, $block['type'], json_encode($settings)]
            );

            $contentId = $db->query("SELECT LAST_INSERT_ID() as id")->fetch(PDO::FETCH_ASSOC)['id'];

            // Link content to page
            $db->query(
                "INSERT INTO pagecontent (page_id, cont_id) VALUES (?, ?)",
                [$pageId, $contentId]
            );

            error_log("Successfully inserted block: " . $block['type'] . " with content: " . $content . " and settings: " . json_encode($settings));
        }
    }
}

$page = null;
$contents = [];
if (isset($_GET['id'])) {
    $page = $db->query(
        "SELECT page_id as id, page_name as title, page_slug as slug, page_status as status, page_image 
         FROM page WHERE page_id = ?",
        [$_GET['id']]
    )->fetch(PDO::FETCH_ASSOC);

    $contents = $db->query(
        "SELECT c.*, pc.page_id 
         FROM content c 
         JOIN pagecontent pc ON c.cont_id = pc.cont_id 
         WHERE pc.page_id = ? 
         ORDER BY c.cont_order",
        [$_GET['id']]
    )->fetchAll(PDO::FETCH_ASSOC);
}
?>
<html>

<head>
    <title>Edit Page</title>
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

            <div class="form-group">
                <label>Featured Image</label>
                <button type="button" class="select-media">Select Image</button>
                <input type="hidden" name="page_image" class="selected-image" value="<?php echo $page ? htmlspecialchars($page['page_image']) : ''; ?>">
                <div class="selected-image-preview">
                    <?php if (!empty($page['page_image'])): ?>
                        <img src="<?php echo htmlspecialchars($page['page_image']); ?>" alt="Selected image">
                    <?php endif; ?>
                </div>
            </div>

            <div id="content-blocks">
                <?php if ($page && $contents): ?>
                    <?php foreach ($contents as $index => $content): ?>
                        <div class="dropZone">
                            <div class="content-block <?php echo $content['cont_block']; ?>-block" draggable="true">
                                <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="<?php echo $content['cont_block']; ?>">
                                <div class="block-header">
                                    <h3><?php echo ucfirst($content['cont_block']); ?></h3>
                                    <button type="button" class="remove-block">Remove</button>
                                </div>
                                <?php if ($content['cont_block'] === 'hero1'): ?>
                                    <div class="form-group">
                                        <label>Title</label>
                                        <textarea
                                            id="wysiwyg-hero-<?php echo $index; ?>"
                                            name="blocks[<?php echo $index; ?>][content]"
                                            class="form-control wysiwyg-editor"
                                            rows="5"><?php echo htmlspecialchars($content['cont_content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Background Image</label>
                                        <button type="button" class="select-media">Select Image</button>
                                        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]"
                                            class="selected-image"
                                            value="<?php echo htmlspecialchars(json_decode($content['cont_settings'], true)['bg_image'] ?? ''); ?>" alt="Selected image">
                                        <div class="selected-image-preview"><?php if (!empty($content['cont_settings'])): ?>
                                                <img src="<?php echo htmlspecialchars(json_decode($content['cont_settings'], true)['bg_image'] ?? ''); ?>" alt="Selected image">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif ($content['cont_block'] === 'text'): ?>
                                    <div class="form-group">
                                        <label>Tekst</label>
                                        <textarea
                                            id="wysiwyg-text-<?php echo $index; ?>"
                                            name="blocks[<?php echo $index; ?>][content]"
                                            class="form-control wysiwyg-editor"
                                            rows="10"><?php echo htmlspecialchars($content['cont_content']); ?></textarea>
                                    </div>
                                <?php elseif ($content['cont_block'] === 'text-img'): ?>
                                    <?php
                                    $settings = json_decode($content['cont_settings'], true) ?? [];
                                    $imageLeft = isset($settings['image_left']) ? $settings['image_left'] : false;
                                    ?>
                                    <div class="form-group">
                                        <label>Layout Order</label>
                                        <div class="toggle-switch">
                                            <label class="switch">
                                                <input type="checkbox" name="blocks[<?php echo $index; ?>][settings][image_left]" <?php echo $imageLeft ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="toggle-label"><?php echo $imageLeft ? 'Image Left, Text Right' : 'Text Left, Image Right'; ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Tekst</label>
                                        <textarea
                                            id="wysiwyg-text-img-<?php echo $index; ?>"
                                            name="blocks[<?php echo $index; ?>][content]"
                                            class="form-control wysiwyg-editor"
                                            rows="10"><?php echo htmlspecialchars($content['cont_content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Image</label>
                                        <button type="button" class="select-media">Select Image</button>
                                        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]" class="selected-image" value="<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>">
                                        <div class="selected-image-preview">
                                            <?php if (!empty($settings['bg_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($settings['bg_image']); ?>" alt="Selected image">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif ($content['cont_block'] === 'image'): ?>
                                    <?php
                                    $settings = json_decode($content['cont_settings'], true) ?? [];
                                    ?>
                                    <div class="form-group">
                                        <label>Image</label>
                                        <button type="button" class="select-media">Select Image</button>
                                        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]" class="selected-image" value="<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>">
                                        <div class="selected-image-preview">
                                            <?php if (!empty($settings['bg_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($settings['bg_image']); ?>" alt="Selected image">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif ($content['cont_block'] === 'ovz-klein'): ?>
                                    <?php
                                    $settings = json_decode($content['cont_settings'], true) ?? [];
                                    $selectedPosts = $settings['selected_posts'] ?? [];

                                    // Fetch all posts for the dropdown
                                    $allPosts = $db->query(
                                        "SELECT post_id, post_name FROM Post WHERE post_status = 1 ORDER BY post_name"
                                    )->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="form-group">
                                        <label>Tekst</label>
                                        <textarea
                                            name="blocks[<?php echo $index; ?>][content]"
                                            class="form-control"
                                            rows="3"><?php echo htmlspecialchars($content['cont_content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Selecteer Posts (maximaal 3)</label>
                                        <select name="blocks[<?php echo $index; ?>][settings][selected_posts][]" class="post-select" multiple size="5">
                                            <?php foreach ($allPosts as $post): ?>
                                                <option value="<?php echo $post['post_id']; ?>"
                                                    <?php echo in_array($post['post_id'], $selectedPosts) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($post['post_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Houd Ctrl (of Cmd op Mac) ingedrukt om meerdere posts te selecteren</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Portfolio Knop</label>
                                        <div class="toggle-switch">
                                            <label class="switch">
                                                <input type="checkbox" name="blocks[<?php echo $index; ?>][settings][show_portfolio_button]" <?php echo isset($settings['show_portfolio_button']) && $settings['show_portfolio_button'] ? 'checked' : ''; ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="toggle-label"><?php echo isset($settings['show_portfolio_button']) && $settings['show_portfolio_button'] ? 'Knop zichtbaar' : 'Knop verborgen'; ?></span>
                                        </div>
                                    </div>
                                <?php elseif ($content['cont_block'] === 'ovz-groot'): ?>
                                    <?php
                                    // Geen specifieke velden nodig in de admin voor ovz-groot
                                    // Alle content wordt dynamisch opgehaald op de frontend
                                    ?>
                                    <!-- Omdat ovz-groot geen specifieke admin velden heeft, tonen we alleen de header -->
                                    <p>Dit blok toont automatisch alle posts. Geen extra instellingen hier.</p>
                                <?php elseif ($content['cont_block'] === 'contact'): ?>
                                    <div class="form-group">
                                        <label>Tekst</label>
                                        <textarea
                                            id="wysiwyg-contact-<?php echo $index; ?>"
                                            name="blocks[<?php echo $index; ?>][content]"
                                            class="form-control wysiwyg-editor"
                                            rows="5"><?php echo htmlspecialchars($content['cont_content']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Afbeelding</label>
                                        <button type="button" class="select-media">Select Image</button>
                                        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]" class="selected-image">
                                        <div class="selected-image-preview"></div>
                                    </div>
                                    <div class="form-group">
                                        <label>E-mail ontvanger</label>
                                        <input type="email" 
                                               name="blocks[<?php echo $index; ?>][settings][recipient_email]" 
                                               class="form-control" 
                                               required>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="dropZone"></div>
            </div>

            <div class="block-controls">
                <select id="block-type">
                    <?php foreach ($blockTypes as $value => $label): ?>
                        <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn-primary" id="add-block">Add Content Block</button>
            </div>

            <button class="save" type="submit">Save Page</button>
        </form>
    </main>
</body>
<script>
    // Add event listeners for all remove buttons (both existing and new blocks)
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle block removal
        function handleBlockRemoval(button) {
            const block = button.closest('.content-block');
            if (block) {
                block.remove();
                updateBlockIndices();
            }
        }

        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-block').forEach(button => {
            button.addEventListener('click', function() {
                handleBlockRemoval(this);
            });
        });

        // Add event listeners to existing media buttons
        document.querySelectorAll('.select-media').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("Media button clicked"); // Debug log
                openMediaPicker(this);
            });
        });

        // Add event listeners to toggle switches
        document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const label = this.closest('.toggle-switch').querySelector('.toggle-label');
                const block = this.closest('.content-block');
                const blockType = block.querySelector('input[name$="[type]"]').value;

                if (blockType === 'text-img') {
                    if (this.checked) {
                        label.textContent = 'Image Left, Text Right';
                    } else {
                        label.textContent = 'Text Left, Image Right';
                    }
                } else if (blockType === 'ovz-klein') {
                    if (this.checked) {
                        label.textContent = 'Knop zichtbaar';
                    } else {
                        label.textContent = 'Knop verborgen';
                    }
                }
            });
        });

        // Add event listener for new blocks
        document.getElementById('add-block').addEventListener('click', function() {
            const blockType = document.getElementById('block-type').value;
            addBlock(blockType);
        });

        // Initialize drag and drop for content blocks
        initializeDragAndDrop();

        // Add keyboard shortcut for saving (Ctrl+S)
        document.addEventListener('keydown', function(e) {
            // Check if Ctrl+S is pressed
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault(); // Prevent the browser's save dialog

                // Submit the form
                document.querySelector('form').submit();
            }
        });
    });

    function initializeDragAndDrop() {
        const contentBlocks = document.querySelectorAll('.content-block');
        const dropZones = document.querySelectorAll('.dropZone');

        // Make each content block draggable
        contentBlocks.forEach(block => {
            block.setAttribute('draggable', 'true');

            block.addEventListener('dragstart', function(e) {
                // Remove TinyMCE before dragging
                const editor = this.querySelector('.wysiwyg-editor');
                if (editor && tinymce.get(editor.id)) {
                    tinymce.get(editor.id).remove();
                }

                e.dataTransfer.setData('text/plain', ''); // Required for Firefox
                this.classList.add('beingDragged');

                // Store the original parent for reference
                this.dataset.originalParent = this.parentNode.id || 'unknown';

                // Create a clone for the drag image to preserve content
                const clone = this.cloneNode(true);
                clone.style.position = 'absolute';
                clone.style.top = '-1000px';
                document.body.appendChild(clone);
                e.dataTransfer.setDragImage(clone, 0, 0);

                // Remove the clone after a short delay
                setTimeout(() => {
                    document.body.removeChild(clone);
                }, 100);
            });

            block.addEventListener('dragend', function() {
                this.classList.remove('beingDragged');

                // Reinitialize TinyMCE after drag
                const editor = this.querySelector('.wysiwyg-editor');
                if (editor) {
                    tinymce.init({
                        selector: '#' + editor.id,
                        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                        tinycomments_mode: 'embedded',
                        tinycomments_author: 'Author name',
                        mergetags_list: [{
                                value: 'First.Name',
                                title: 'First Name'
                            },
                            {
                                value: 'Email',
                                title: 'Email'
                            },
                        ],
                        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
                    });
                }
            });
        });

        // Set up drop zones
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('hoverOver');
            });

            zone.addEventListener('dragleave', function() {
                this.classList.remove('hoverOver');
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('hoverOver');

                const draggedBlock = document.querySelector('.beingDragged');
                if (draggedBlock) {
                    // If the drop zone already has a block, swap them
                    if (this.children.length > 0) {
                        const existingBlock = this.children[0];
                        const draggedParent = draggedBlock.parentNode;

                        // Remove TinyMCE from existing block before swapping
                        const existingEditor = existingBlock.querySelector('.wysiwyg-editor');
                        if (existingEditor && tinymce.get(existingEditor.id)) {
                            tinymce.get(existingEditor.id).remove();
                        }

                        // Swap the blocks
                        draggedParent.appendChild(existingBlock);
                        this.appendChild(draggedBlock);
                    } else {
                        // If the drop zone is empty, just move the block
                        this.appendChild(draggedBlock);
                    }

                    // Update block indices
                    updateBlockIndices();
                }
            });
        });
    }

    // Function to update the indices of all blocks
    function updateBlockIndices() {
        const dropZones = document.querySelectorAll('.dropZone');
        let index = 0;

        dropZones.forEach(zone => {
            const block = zone.querySelector('.content-block');
            if (block) {
                // Update the hidden input for block type
                const typeInput = block.querySelector('input[name^="blocks"][name$="[type]"]');
                if (typeInput) {
                    typeInput.name = `blocks[${index}][type]`;
                }

                // Update the content textarea
                const contentTextarea = block.querySelector('textarea[name^="blocks"][name$="[content]"]');
                if (contentTextarea) {
                    contentTextarea.name = `blocks[${index}][content]`;
                }

                // Update the settings input
                const settingsInput = block.querySelector('input[name^="blocks"][name$="[settings]"]');
                if (settingsInput) {
                    settingsInput.name = `blocks[${index}][settings][bg_image]`;
                }

                // Update the ID of the wysiwyg editor
                const wysiwygEditor = block.querySelector('.wysiwyg-editor');
                if (wysiwygEditor) {
                    const blockType = typeInput ? typeInput.value : 'text';
                    wysiwygEditor.id = `wysiwyg-${blockType}-${index}`;
                }

                index++;
            }
        });
    }

    function addBlock(type) {
        const dropZones = document.querySelectorAll('.dropZone');
        let targetZone = null;

        // Find the first empty drop zone
        for (let i = 0; i < dropZones.length; i++) {
            if (dropZones[i].children.length === 0) {
                targetZone = dropZones[i];
                break;
            }
        }

        // If no empty drop zone found, use the last one
        if (!targetZone && dropZones.length > 0) {
            targetZone = dropZones[dropZones.length - 1];
        }

        if (!targetZone) {
            console.error('No drop zones found');
            return;
        }

        const index = document.querySelectorAll('.content-block').length;
        const block = document.createElement('div');
        block.className = `content-block ${type}-block`;
        block.draggable = true;

        switch (type) {
            case 'hero1':
                block.innerHTML = `
                <div class="content-block hero1-block">
                    <input type="hidden" name="blocks[${index}][type]" value="hero1">
                    <div class="block-header">
                        <h3>Hero 1</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <div class="form-group">
                        <label>Heading</label>
                        <textarea 
                               id="wysiwyg-hero-${index}"
                               name="blocks[${index}][content]" 
                               class="form-control wysiwyg-editor"
                               rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Background Image</label>
                        <button type="button" class="select-media">Select Image</button>
                        <input type="hidden" name="blocks[${index}][settings][bg_image]" class="selected-image">
                        <div class="selected-image-preview"></div>
                    </div>
                </div>
            `;
                break;

            case 'text':
                block.innerHTML = `
                <div class="content-block text-block">
                    <input type="hidden" name="blocks[${index}][type]" value="text">
                    <div class="block-header">
                        <h3>Text Block</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <div class="form-group">
                        <label>Tekst</label>
                        <textarea 
                               id="wysiwyg-text-${index}"
                               name="blocks[${index}][content]" 
                               class="form-control wysiwyg-editor"
                               rows="10"></textarea>
                    </div>
                </div>
            `;
                break;

            case 'text-img':
                block.innerHTML = `
                <div class="content-block tekst-img-block">
                    <input type="hidden" name="blocks[${index}][type]" value="text-img">
                    <div class="block-header">
                        <h3>Tekst & Image</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <div class="form-group">
                        <label>Layout Order</label>
                        <div class="toggle-switch">
                            <label class="switch">
                                <input type="checkbox" name="blocks[${index}][settings][image_left]">
                                <span class="slider round"></span>
                            </label>
                            <span class="toggle-label">Text Left, Image Right</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tekst</label>
                        <textarea 
                            id="wysiwyg-text-${index}"
                            name="blocks[${index}][content]" 
                            class="form-control wysiwyg-editor"
                            rows="10"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <button type="button" class="select-media">Select Image</button>
                        <input type="hidden" name="blocks[${index}][settings][bg_image]" class="selected-image">
                        <div class="selected-image-preview"></div>
                    </div>
                    <!-- Add a hidden input to store the image_left setting for the frontend -->
                    <input type="hidden" name="blocks[${index}][settings][image_left]" value="0">
                </div> 
            `;
                break;
            case 'image':
                block.innerHTML = `
                <div class="content-block image-block">
                    <input type="hidden" name="blocks[${index}][type]" value="image">
                    <div class="block-header">
                        <h3>Image Block</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <button type="button" class="select-media">Select Image</button>
                        <input type="hidden" name="blocks[${index}][settings][bg_image]" class="selected-image">
                        <div class="selected-image-preview"></div>
                    </div>
                </div>
            `;
                break;
            case 'ovz-klein':
                block.innerHTML = `
                <div class="content-block ovz-klein-block">
                    <input type="hidden" name="blocks[${index}][type]" value="ovz-klein">
                    <div class="block-header">
                        <h3>Klein Overzicht</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <div class="form-group">
                        <label>Tekst</label>
                        <input type="text" 
                               name="blocks[${index}][content]">
                    </div>
                    <div class="form-group">
                        <label>Selecteer Posts (maximaal 3)</label>
                        <select name="blocks[${index}][settings][selected_posts][]" class="post-select" multiple size="5">
                            <option value="">Loading posts...</option>
                        </select>
                        <small class="form-text text-muted">Houd Ctrl (of Cmd op Mac) ingedrukt om meerdere posts te selecteren</small>
                    </div>
                </div>
            `;
                // Fetch posts for the new block
                fetch('includes/get_posts.php')
                    .then(response => response.json())
                    .then(posts => {
                        const select = block.querySelector('.post-select');
                        select.innerHTML = posts.map(post => 
                            `<option value="${post.post_id}">${post.post_name}</option>`
                        ).join('');
                    })
                    .catch(error => {
                        console.error('Error loading posts:', error);
                        const select = block.querySelector('.post-select');
                        select.innerHTML = '<option value="">Error loading posts</option>';
                    });
                break;
            case 'ovz-groot':
                block.innerHTML = `
                <div class="content-block ovz-groot-block">
                    <input type="hidden" name="blocks[${index}][type]" value="ovz-groot">
                    <div class="block-header">
                        <h3>Groot Overzicht</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <p>Dit blok toont automatisch alle posts. Geen extra instellingen hier.</p>
                </div>
            `;
                break;
            case 'contact':
                block.innerHTML = `
                <div class="content-block contact-block">
                    <input type="hidden" name="blocks[${index}][type]" value="contact">
                    <div class="block-header">
                        <h3>Contact Formulier</h3>
                        <button type="button" class="remove-block">Remove</button>
                    </div>
                    <div class="form-group">
                        <label>Tekst</label>
                        <textarea 
                            id="wysiwyg-contact-${index}"
                            name="blocks[${index}][content]" 
                            class="form-control wysiwyg-editor"
                            rows="5"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Afbeelding</label>
                        <button type="button" class="select-media">Select Image</button>
                        <input type="hidden" name="blocks[${index}][settings][bg_image]" class="selected-image">
                        <div class="selected-image-preview"></div>
                    </div>
                    <div class="form-group">
                        <label>E-mail ontvanger</label>
                        <input type="email" 
                               name="blocks[${index}][settings][recipient_email]" 
                               class="form-control" 
                               required>
                    </div>
                </div>
            `;
                break;
        }

        targetZone.appendChild(block);

        // Add event listeners for the new block
        const removeButton = block.querySelector('.remove-block');
        if (removeButton) {
            removeButton.addEventListener('click', function() {
                handleBlockRemoval(this);
            });
        }

        // Add event listeners for media buttons in the new block
        const mediaButtons = block.querySelectorAll('.select-media');
        mediaButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log("New block media button clicked"); // Debug log
                openMediaPicker(this);
            });
        });

        // Add event listener for toggle switch in the new block
        const toggleSwitch = block.querySelector('.toggle-switch input[type="checkbox"]');
        if (toggleSwitch) {
            toggleSwitch.addEventListener('change', function() {
                const label = this.closest('.toggle-switch').querySelector('.toggle-label');
                const block = this.closest('.content-block');

                if (this.checked) {
                    label.textContent = 'Image Left, Text Right';
                } else {
                    label.textContent = 'Text Left, Image Right';
                }

                // Update the hidden input value
                const hiddenInput = block.querySelector('input[name$="[settings][image_left]"]');
                if (hiddenInput) {
                    hiddenInput.value = this.checked ? '1' : '0';
                }
            });
        }

        // Initialize TinyMCE for the new block
        const editor = block.querySelector('.wysiwyg-editor');
        if (editor) {
            tinymce.init({
                selector: '#' + editor.id,
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                mergetags_list: [{
                        value: 'First.Name',
                        title: 'First Name'
                    },
                    {
                        value: 'Email',
                        title: 'Email'
                    },
                ],
                ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
            });
        }

        // Make the new block draggable
        block.setAttribute('draggable', 'true');
        block.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', '');
            this.classList.add('beingDragged');

            // Store the original parent for reference
            this.dataset.originalParent = this.parentNode.id || 'unknown';

            // Create a clone for the drag image to preserve content
            const clone = this.cloneNode(true);
            clone.style.position = 'absolute';
            clone.style.top = '-1000px';
            document.body.appendChild(clone);
            e.dataTransfer.setDragImage(clone, 0, 0);

            // Remove the clone after a short delay
            setTimeout(() => {
                document.body.removeChild(clone);
            }, 100);
        });
        block.addEventListener('dragend', function() {
            this.classList.remove('beingDragged');
        });

        // Update block indices after adding a new block
        updateBlockIndices();
    }

    // Media Picker functionality
    function openMediaPicker(button) {
        const mediaPicker = document.createElement('div');
        mediaPicker.className = 'media-picker-modal';
        const originalBlock = button.closest('.form-group');

        // Create modal
        mediaPicker.innerHTML = `
            <div class="media-picker-content">
                <div class="media-picker-header">
                    <h3>Select Media</h3>
                    <button type="button" class="close-media-picker">×</button>
                </div>
                <div class="media-picker-grid">
                    <div class="loading">Loading media...</div>
                </div>
            </div>
        `;

        document.body.appendChild(mediaPicker);

        // Add event listener for close button
        const closeButton = mediaPicker.querySelector('.close-media-picker');
        closeButton.addEventListener('click', function() {
            mediaPicker.remove();
        });

        // Close modal when clicking outside
        mediaPicker.addEventListener('click', function(e) {
            if (e.target === mediaPicker) {
                mediaPicker.remove();
            }
        });

        // Fetch media from the server
        fetch('includes/get_media.php')
            .then(response => response.json())
            .then(media => {
                const mediaGrid = mediaPicker.querySelector('.media-picker-grid');
                mediaGrid.innerHTML = media.map(item => `
                    <div class="media-item" data-url="${item.filepath}">
                        <img src="${item.filepath}" alt="${item.filename}">
                        <p>${item.filename}</p>
                    </div>
                `).join('');

                // Add event listeners for media items
                mediaPicker.querySelectorAll('.media-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const imageUrl = this.dataset.url;
                        const hiddenInput = originalBlock.querySelector('.selected-image');
                        const preview = originalBlock.querySelector('.selected-image-preview');

                        hiddenInput.value = imageUrl;
                        preview.innerHTML = `<img src="${imageUrl}" alt="Selected image">`;
                        mediaPicker.remove();
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                const mediaGrid = mediaPicker.querySelector('.media-picker-grid');
                mediaGrid.innerHTML = `<div class="error">Error loading media. Please try again.</div>`;
            });
    }

    // Make sure block indices are updated before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        updateBlockIndices();
    });
</script>

</html>