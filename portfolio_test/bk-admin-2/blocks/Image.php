<div class="content-block image">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="image">
    <div class="block-header">
        <h3>Image</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <div class="form-group">
        <label>Image</label>
        <button type="button" class="select-media">Select Image</button>
        <input type="hidden" name="blocks[<?php echo $index; ?>][settings][bg_image]" class="selected-image">
        <div class="selected-image-preview"></div>
    </div>
</div>