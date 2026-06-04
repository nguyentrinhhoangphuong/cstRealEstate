<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<p>Are you sure you want to delete the role <strong>"<?= out($record->name) ?>"</strong>?</p>
<p style="color:#dc2626">This will remove this role from all administrators and revoke all associated permissions.</p>

<div class="rbac-form">
    <?= form_open($form_location) ?>
    <div class="rbac-field actions">
        <button type="submit" name="submit" value="Yes - Delete Now" class="button alt" style="background:#dc2626">Yes - Delete Now</button>
        <a href="<?= $cancel_url ?>" class="button">Cancel</a>
    </div>
    <?= form_close() ?>
</div>
