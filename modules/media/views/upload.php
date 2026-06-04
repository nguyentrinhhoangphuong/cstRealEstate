<h1>Upload Media</h1>
<?php if ($folder_id > 0): ?><p style="color:#6b7280;margin-top:-8px">Uploading to: <strong><?= out($folder_name ?? '') ?></strong></p><?php endif; ?>
<?php echo flashdata(); ?>
<?= form_open_upload('media/submit', ['id' => 'uploadForm']) ?>
<?= form_hidden('folder_id', $folder_id) ?>
<div class="upload-area" id="uploadArea">
    <div class="upload-prompt" id="uploadPrompt">
        <div class="upload-icon-wrap">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        </div>
        <p class="upload-title">Drop files here or click to browse</p>
        <p class="upload-hint">Supports: JPG, PNG, GIF, WebP, PDF, DOC, ZIP, MP4 &amp; more</p>
    </div>
    <div class="upload-previews" id="uploadPreviews"></div>
    <input type="file" name="userfile[]" id="fileInput" style="display:none" multiple>
</div>
<div class="upload-footer" id="uploadFooter" style="display:none">
    <button type="submit" class="button alt" id="uploadBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Upload <span id="fileCount"></span>
    </button>
    <a href="<?= $cancel_url ?>" class="button">Cancel</a>
</div>
<?= form_close() ?>
