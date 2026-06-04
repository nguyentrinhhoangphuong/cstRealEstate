<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<div class="rbac-toolbar">
    <a href="<?= BASE_URL ?>trongate_rbac/manage_roles" class="button">Back to Roles</a>
    <a href="<?= BASE_URL ?>trongate_rbac/create_permission" class="button alt">+ Create Permission</a>
</div>

<table class="rbac-table">
    <thead>
        <tr>
            <th>Permission</th>
            <th>Slug</th>
            <th>Module</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="5">No permissions found.</td></tr>
        <?php else: ?>
        <?php foreach ($rows as $perm): ?>
        <tr>
            <td><strong><?= out($perm->name) ?></strong></td>
            <td><code><?= out($perm->slug) ?></code></td>
            <td><span class="rbac-badge module"><?= out($perm->module ?? '-') ?></span></td>
            <td><?= out($perm->description ?? '-') ?></td>
            <td class="rbac-actions">
                <a href="<?= BASE_URL ?>trongate_rbac/update_permission/<?= $perm->id ?>" class="button">Edit</a>
                <a href="<?= BASE_URL ?>trongate_rbac/delete_permission_conf/<?= $perm->id ?>" class="button" onclick="return confirm('Delete this permission?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
