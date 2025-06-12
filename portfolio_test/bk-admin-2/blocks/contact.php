<div class="content-block contact-block">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="contact">
    <div class="block-header">
        <h3>Contact</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <?php 
    $settings = json_decode($content['settings'] ?? '{}', true);
    $block_content = $content['content'] ?? '';
    ?>
    <div class="form-group">
        <label>Tekst</label>
        <textarea 
            name="blocks[<?php echo $index; ?>][content]"
            class="form-control wysiwyg-editor"
            rows="5"><?php echo htmlspecialchars($block_content); ?></textarea>
    </div>
    <div class="form-group">
        <label>Afbeelding</label>
        <button type="button" class="select-media">Select Image</button>
        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]" class="selected-image" value="<?php echo htmlspecialchars($settings['bg_image'] ?? ''); ?>">
        <div class="selected-image-preview">
            <?php if (!empty($settings['bg_image'])): ?>
                <img src="<?php echo htmlspecialchars($settings['bg_image']); ?>" alt="Selected image">
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label>E-mail ontvanger</label>
        <input type="email" 
               name="blocks[<?php echo $index; ?>][settings][recipient_email]" 
               class="form-control" 
               value="<?php echo htmlspecialchars($settings['recipient_email'] ?? ''); ?>"
               required>
    </div>
</div>
