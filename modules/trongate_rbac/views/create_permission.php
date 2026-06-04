<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<?php if (isset($permission)): ?>
<?= form_open('trongate_rbac/submit_update_permission/' . $permission->id) ?>
<?php else: ?>
<?= form_open('trongate_rbac/submit_permission') ?>
<?php endif; ?>

<div class="rbac-form">
    <div class="rbac-field">
        <label for="name">Permission Name *</label>
        <input type="text" name="name" id="name" value="<?= out($permission->name ?? '') ?>" required>
        <small>e.g. "Upload Media"</small>
    </div>
    <div class="rbac-field">
        <label for="slug">Slug *</label>
        <input type="text" name="slug" id="slug" value="<?= out($permission->slug ?? '') ?>" required>
        <small>Use dot notation, e.g. "media.upload"</small>
    </div>
    <div class="rbac-field">
        <label for="module">Module</label>
        <input type="text" name="module" id="module" value="<?= out($permission->module ?? '') ?>">
        <small>The module this permission belongs to, e.g. "media", "trongate_rbac"</small>
    </div>
    <div class="rbac-field">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3"><?= out($permission->description ?? '') ?></textarea>
    </div>
    <div class="rbac-field actions">
        <button type="submit" class="button alt">Save</button>
        <a href="<?= BASE_URL ?>trongate_rbac/manage_permissions" class="button">Cancel</a>
    </div>
</div>

<?= form_close() ?>
