<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<?= form_open($form_location) ?>
<div class="rbac-permission-grid">
    <?php foreach ($grouped_permissions as $module => $perms): ?>
    <div class="rbac-permission-group">
        <h3 class="rbac-group-title">
            <label>
                <input type="checkbox" class="rbac-group-toggle" data-module="<?= out($module) ?>">
                <?= out($module !== '_other' ? $module : 'Other') ?>
            </label>
        </h3>
        <div class="rbac-permission-list">
            <?php foreach ($perms as $perm): ?>
            <label class="rbac-permission-item">
                <input type="checkbox" name="permissions[]" value="<?= $perm->id ?>" class="rbac-perm-cb" data-module="<?= out($module) ?>" <?= in_array($perm->id, $assigned_ids) ? 'checked' : '' ?>>
                <span class="rbac-perm-name"><?= out($perm->name) ?></span>
                <span class="rbac-perm-slug"><code><?= out($perm->slug) ?></code></span>
                <?php if ($perm->description): ?>
                <span class="rbac-perm-desc"><?= out($perm->description) ?></span>
                <?php endif; ?>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="rbac-form">
    <div class="rbac-field actions">
        <button type="submit" class="button alt">Save Permissions</button>
        <a href="<?= $cancel_url ?>" class="button">Cancel</a>
    </div>
</div>
<?= form_close() ?>
