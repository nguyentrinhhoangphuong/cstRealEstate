<h1><?= out($headline) ?></h1>
<?php echo flashdata(); ?>

<div class="media-detail">
    <div class="media-preview">
        <?php if ($media_type === 'video'): ?>
            <video controls style="width:100%;max-height:360px;border-radius:10px;background:#000">
                <source src="<?= BASE_URL ?>media/fetch/<?= $update_id ?>" type="<?= out($filetype) ?>">
            </video>
        <?php elseif ($media_type === 'audio'): ?>
            <div class="audio-player-wrap">
                <div class="audio-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                </div>
                <audio controls style="width:100%">
                    <source src="<?= BASE_URL ?>media/fetch/<?= $update_id ?>" type="<?= out($filetype) ?>">
                </audio>
            </div>
        <?php elseif ($media_type === 'pdf'): ?>
            <div class="pdf-viewer">
                <iframe src="<?= BASE_URL ?>media/fetch/<?= $update_id ?>" width="100%" height="500px" style="border:none"></iframe>
                <div class="pdf-toolbar">
                    <a href="<?= BASE_URL ?>media/fetch/<?= $update_id ?>" target="_blank" class="button alt" style="font-size:13px">Open in new tab</a>
                </div>
            </div>
        <?php elseif ($media_type === 'image'): ?>
            <img src="<?= BASE_URL ?>media/fetch/<?= $update_id ?>" alt="<?= out($alt_text ?: $original_filename) ?>">
        <?php else: ?>
            <div class="media-icon-lg">
                <span class="ext"><?= strtoupper($extension) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="media-meta-detail">
        <div class="detail-section">
            <h3>File Info</h3>
            <dl class="meta-list">
                <dt>Filename</dt>
                <dd><?= out($original_filename) ?></dd>
                <dt>Size</dt>
                <dd><?= $filesize_formatted ?></dd>
                <dt>Type</dt>
                <dd><?= out($filetype) ?></dd>
                <dt>Uploaded</dt>
                <dd><?= $created_at_formatted ?></dd>
                <dt>URL</dt>
                <dd><input type="text" readonly value="<?= BASE_URL ?>media/fetch/<?= $update_id ?>" onclick="this.select()" class="url-copy"></dd>
            </dl>
        </div>

        <?php if ($can_edit): ?>
        <?= form_open('media/update_meta/'.$update_id) ?>
        <div class="detail-section">
            <h3>Metadata</h3>
            <dl class="meta-list">
                <dt>Alt Text</dt>
                <dd><?= form_input('alt_text', out($alt_text), ['placeholder' => 'Describe the image...']) ?></dd>
                <dt>Caption</dt>
                <dd><?= form_textarea('caption', out($caption), ['placeholder' => 'Add a caption...', 'rows' => 3]) ?></dd>
            </dl>
        </div>
        <div class="detail-actions">
            <button type="submit" name="submit" value="Save" class="button alt">Save Changes</button>
            <a href="<?= $back_url ?>" class="button">Back to Library</a>
            <?php if ($can_delete): ?><a href="<?= BASE_URL ?>media/delete_conf/<?= $update_id ?>" class="btn-delete">Delete</a><?php endif; ?>
        </div>
        <?= form_close() ?>
        <?php else: ?>
        <div class="detail-actions">
            <a href="<?= $back_url ?>" class="button">Back to Library</a>
        </div>
        <?php endif; ?>
    </div>
</div>
