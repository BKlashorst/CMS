<div class="content-block ovz-klein">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="<?php echo $block_type; ?>">
    <div class="block-header">
        <h3>Klein Overzicht</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <?php 
    $settings = json_decode($content['settings'] ?? '{}', true);
    $block_content = $content['content'] ?? '';
    $selected_posts = isset($settings['selected_posts']) ? $settings['selected_posts'] : [];
    $show_portfolio_button = isset($settings['show_portfolio_button']) ? $settings['show_portfolio_button'] : false;
    ?>
    <div class="form-group">
        <label>Tekst</label>
        <textarea 
            name="blocks[<?php echo $index; ?>][content]"
            class="form-control"
            rows="3"><?php echo htmlspecialchars($block_content); ?></textarea>
    </div>
    <div class="form-group">
        <label>Selecteer Posts (maximaal 3)</label>
        <select name="blocks[<?php echo $index; ?>][settings][selected_posts][]" class="post-select" multiple size="5">
            <?php
            // Fetch all published posts from the database
            $query = "SELECT post_id, post_name FROM Post WHERE post_status = 1 ORDER BY post_name";
            $posts = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
            
            // Display posts in the select dropdown
            foreach ($posts as $post) {
                $selected = in_array($post['post_id'], $selected_posts) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($post['post_id']) . '" ' . $selected . '>' . 
                     htmlspecialchars($post['post_name']) . '</option>';
            }
            ?>
        </select>
        <small class="form-text text-muted">Houd Ctrl (of Cmd op Mac) ingedrukt om meerdere posts te selecteren</small>
    </div>
    <div class="form-group">
        <label>Portfolio Knop</label>
        <div class="toggle-switch">
            <label class="switch">
                <input type="checkbox" 
                       name="blocks[<?php echo $index; ?>][settings][show_portfolio_button]" 
                       <?php echo $show_portfolio_button ? 'checked' : ''; ?>>
                <span class="slider round"></span>
            </label>
            <span class="toggle-label"><?php echo $show_portfolio_button ? 'Knop zichtbaar' : 'Knop verborgen'; ?></span>
        </div>
    </div>
</div>
