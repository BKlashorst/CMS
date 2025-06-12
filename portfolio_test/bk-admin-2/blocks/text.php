<div class="content-block tekst">
    <input type="hidden" name="blocks[<?php echo $index; ?>][type]" value="text">
    <div class="block-header">
        <h3>Tekst</h3>
        <button type="button" class="remove-block">Remove</button>
    </div>
    <?php 
    $settings = json_decode($content['settings'], true) ?? [];
    ?>
    <div class="form-group">
        <label>Tekst</label>
        <textarea 
               id="wysiwyg-text-<?php echo $index; ?>"
               name="blocks[<?php echo $index; ?>][content]" 
               class="form-control wysiwyg-editor"
               rows="10"><?php echo htmlspecialchars($content['content']); ?></textarea>
    </div>
</div> 