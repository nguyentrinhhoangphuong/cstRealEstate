<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<?php if (isset($role)): ?>
<?= form_open('trongate_rbac/submit_update_role/' . $role->id) ?>
<?php else: ?>
<?= form_open('trongate_rbac/submit_role') ?>
<?php endif; ?>

<div class="rbac-form">
    <div class="rbac-field">
        <label for="name">Role Name *</label>
        <input type="text" name="name" id="name" value="<?= out($role->name ?? '') ?>" required>
    </div>
    <div class="rbac-field">
        <label for="slug">Slug</label>
        <input type="text" name="slug" id="slug" value="<?= out($role->slug ?? '') ?>" placeholder="Auto-generated from name">
        <small>Leave blank to auto-generate. Use lowercase with hyphens (e.g. "editor")</small>
    </div>
    <div class="rbac-field">
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="3"><?= out($role->description ?? '') ?></textarea>
    </div>
    <div class="rbac-field actions">
        <button type="submit" class="button alt">Save</button>
        <a href="<?= BASE_URL ?>trongate_rbac/manage_roles" class="button">Cancel</a>
    </div>
</div>

<?= form_close() ?>
