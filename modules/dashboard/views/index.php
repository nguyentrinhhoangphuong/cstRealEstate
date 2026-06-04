<h1>Dashboard</h1>
<?php echo flashdata(); ?>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px;margin-top:16px">
    <div class="card">
        <div class="card-heading">Media Library</div>
        <div class="card-body">
            <p>Manage your media files, upload images, videos, and documents.</p>
            <a href="media/manage" class="button alt">Go to Media</a>
        </div>
    </div>
    <div class="card">
        <div class="card-heading">Access Control</div>
        <div class="card-body">
            <p>Manage user roles and permissions.</p>
            <a href="trongate_rbac/manage_roles" class="button alt">Manage Roles</a>
        </div>
    </div>
    <div class="card">
        <div class="card-heading">Administrators</div>
        <div class="card-body">
            <p>Manage administrator accounts.</p>
            <a href="trongate_administrators/manage" class="button alt">Manage Users</a>
        </div>
    </div>
</div>
