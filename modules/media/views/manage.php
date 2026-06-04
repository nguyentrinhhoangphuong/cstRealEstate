<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<div class="media-layout">
    <aside class="media-sidebar">
        <?php if ($can_upload): ?>
        <a href="<?= BASE_URL ?>media/create/<?= $current_folder_id > 0 ? $current_folder_id : '' ?>" class="btn-upload">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload New
        </a>
        <?php endif; ?>

        <div class="sidebar-section">
            <h3>Folders</h3>
            <ul class="folder-list">
                <li class="<?= ($current_folder_id === 0) ? 'active' : '' ?>">
                    <span class="folder-drag">
                        <a href="<?= BASE_URL ?>media/manage" class="folder-link" data-folder="0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            All Media
                        </a>
                    </span>
                </li>
                <?php foreach ($folders as $f): ?>
                <li class="<?= ($current_folder_id === $f->id) ? 'active' : '' ?>">
                    <span class="folder-drag">
                        <a href="<?= BASE_URL ?>media/folder/<?= $f->id ?>" class="folder-link" data-folder="<?= $f->id ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            <?= out($f->name) ?>
                        </a>
                        <span class="folder-actions">
                            <?php if ($can_rename_folders): ?><a href="#" class="rename-folder" data-id="<?= $f->id ?>" data-name="<?= out($f->name) ?>" title="Rename">&#9998;</a><?php endif; ?>
                            <?php if ($can_delete_folders): ?><a href="<?= BASE_URL ?>media/delete_folder/<?= $f->id ?>" class="delete-folder" title="Delete" onclick="return confirm('Delete this folder? Files will be moved to root.')">&#10005;</a><?php endif; ?>
                        </span>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($can_create_folders): ?>
            <div class="folder-actions-bottom">
                <a href="#" id="newFolderBtn" class="btn-new-folder">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    New Folder
                </a>
            </div>

            <div id="newFolderForm" class="new-folder-form" style="display:none">
                <?= form_open('media/create_folder') ?>
                <input type="text" name="name" placeholder="Folder name" required>
                <div class="form-actions">
                    <button type="submit" class="button small">Create</button>
                    <button type="button" id="cancelFolderBtn" class="button small btn-cancel">Cancel</button>
                </div>
                <?= form_close() ?>
            </div>
            <?php endif; ?>
        </div>
    </aside>

    <div class="media-content">
        <?php if (empty($rows)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <p>No media files found in this folder.</p>
            <?php if ($can_upload): ?><a href="<?= BASE_URL ?>media/create/<?= $current_folder_id > 0 ? $current_folder_id : '' ?>" class="button alt">Upload Files</a><?php endif; ?>
        </div>
        <?php else: ?>
        <?php echo Modules::run('pagination/display', $pagination_data); ?>
        <div class="media-grid" id="mediaGrid">
            <?php foreach ($rows as $row): ?>
            <div class="media-item" data-id="<?= $row->id ?>" draggable="<?= $can_edit ? 'true' : 'false' ?>">
                <a href="<?= BASE_URL ?>media/show/<?= $row->id ?>" class="media-thumb">
                    <?php if ($row->media_type === 'image'): ?>
                    <img src="<?= BASE_URL ?>media/fetch/<?= $row->id ?>" alt="<?= out($row->alt_text ?: $row->original_filename) ?>" loading="lazy">
                    <?php elseif ($row->media_type === 'video'): ?>
                    <div class="media-icon media-type-video">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                        <span class="ext"><?= strtoupper($row->extension) ?></span>
                    </div>
                    <?php elseif ($row->media_type === 'audio'): ?>
                    <div class="media-icon media-type-audio">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="1.5"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                        <span class="ext"><?= strtoupper($row->extension) ?></span>
                    </div>
                    <?php elseif ($row->media_type === 'pdf'): ?>
                    <div class="media-icon media-type-pdf">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <span>PDF</span>
                    </div>
                    <?php else: ?>
                    <div class="media-icon">
                        <span class="ext"><?= strtoupper($row->extension) ?></span>
                    </div>
                    <?php endif; ?>
                </a>
                <div class="media-info">
                    <span class="media-name" title="<?= out($row->original_filename) ?>"><?= out($row->original_filename) ?></span>
                    <span class="media-meta"><?= $row->filesize_formatted ?> &middot; <?= $row->created_at_formatted ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        if (count($rows) > 9) {
            unset($pagination_data['include_showing_statement']);
            echo Modules::run('pagination/display', $pagination_data);
        }
        ?>
        <?php endif; ?>
    </div>
</div>
