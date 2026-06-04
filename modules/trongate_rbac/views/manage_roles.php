<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<div class="rbac-toolbar">
    <a href="<?= BASE_URL ?>trongate_rbac/manage_permissions" class="button">Manage Permissions</a>
    <a href="<?= BASE_URL ?>trongate_rbac/create_role" class="button alt">+ Create Role</a>
</div>

<table class="rbac-table">
    <thead>
        <tr>
            <th>Role</th>
            <th>Slug</th>
            <th>Description</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="5">No roles found.</td></tr>
        <?php else: ?>
        <?php foreach ($rows as $role): ?>
        <tr>
            <td><strong><?= out($role->name) ?></strong></td>
            <td><code><?= out($role->slug) ?></code></td>
            <td><?= out($role->description ?? '-') ?></td>
            <td>
                <?php if ($role->is_system): ?>
                    <span class="rbac-badge system">System</span>
                <?php else: ?>
                    <span class="rbac-badge custom">Custom</span>
                <?php endif; ?>
            </td>
            <td class="rbac-actions">
                <a href="<?= BASE_URL ?>trongate_rbac/assign_permissions/<?= $role->id ?>" class="button" title="Assign Permissions">Permissions</a>
                <?php if (!$role->is_system): ?>
                <a href="<?= BASE_URL ?>trongate_rbac/update_role/<?= $role->id ?>" class="button">Edit</a>
                <a href="<?= BASE_URL ?>trongate_rbac/delete_role_conf/<?= $role->id ?>" class="button" onclick="return confirm('Delete this role?')">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($pagination_data) && $pagination_data['total_rows'] > 0): ?>
<?= Modules::run('pagination/display', $pagination_data) ?>
<?php endif; ?>
