<div class="content-block tekst-img-block">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="text-img">
    <div class="block-header">
        <h3>Tekst & Image</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <?php 
    $settings = json_decode($content['settings'], true) ?? [];
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
               id="wysiwyg-text-<?php echo $index; ?>"
               name="blocks[<?php echo $index; ?>][content]" 
               class="form-control wysiwyg-editor"
               rows="10"><?php echo htmlspecialchars($content['content']); ?></textarea>
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
    <input type="hidden" name="blocks[<?php echo $index; ?>][settings][image_left]" value="<?php echo $imageLeft ? '1' : '0'; ?>">
</div> 