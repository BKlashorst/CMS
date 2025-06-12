<div class="content-block hero-1">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="hero1">
    <div class="block-header">
        <h3>Hero 1</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <div class="form-group">
        <label>Heading</label>
        <textarea 
               name="blocks[<?php echo $index; ?>][content]" 
               class="form-control wysiwyg-editor"
               rows="5"><?php echo htmlspecialchars($content['content']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Image</label>
        <button type="button" class="select-media">Select Image</button>
        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]" class="selected-image">
        <div class="selected-image-preview"></div>
    </div>
</div>