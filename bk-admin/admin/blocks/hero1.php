<div class="content-block hero-1">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="hero1">
    <div class="block-header">
        <h3>Hero 1</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <?php 
    $settings = json_decode($content['settings'], true) ?? [];
    ?>
    <div class="form-group">
        <label>Heading</label>
        <input type="text" 
               name="blocks[<?php echo $index; ?>][content]" 
               value="<?php echo htmlspecialchars($content['content']); ?>"
               class="form-control">
    </div>
    <div class="form-group">
        <label>Background Image URL</label>
        <input type="text" 
               name="blocks[<?php echo $index; ?>][settings][bg_image]" 
               value="<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>"
               class="form-control">
    </div>
</div> 