<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<div class="rbac-admin-info">
    <p>
        Admin: <strong><?= out($admin->username) ?></strong>
        <?php if ($admin->email): ?>
        &mdash; <?= out($admin->email) ?>
        <?php endif; ?>
        <?php if ($admin->is_super_admin): ?>
        <span class="rbac-badge super-admin">Super Admin</span>
        <?php endif; ?>
    </p>
</div>

<?= form_open($form_location) ?>
<div class="rbac-role-grid">
    <?php foreach ($all_roles as $role): ?>
    <label class="rbac-role-item <?= $role->is_system ? 'system' : '' ?>">
        <input type="checkbox" name="roles[]" value="<?= $role->id ?>" <?= in_array($role->id, $assigned_ids) ? 'checked' : '' ?>>
        <span class="rbac-role-name"><?= out($role->name) ?></span>
        <span class="rbac-role-slug"><code><?= out($role->slug) ?></code></span>
        <?php if ($role->description): ?>
        <span class="rbac-role-desc"><?= out($role->description) ?></span>
        <?php endif; ?>
    </label>
    <?php endforeach; ?>
</div>

<div class="rbac-form">
    <div class="rbac-field actions">
        <button type="submit" class="button alt">Save Assignments</button>
        <a href="<?= $cancel_url ?>" class="button">Cancel</a>
    </div>
</div>
<?= form_close() ?>
