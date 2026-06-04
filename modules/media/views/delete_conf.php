<h1><?= out($headline) ?></h1>
<p>Are you sure you want to delete this media file? This action cannot be undone.</p>
<?= form_open($form_location) ?>
    <p><button type="submit" name="submit" value="Yes - Delete Now" class="button danger">Yes - Delete Now</button></p>
    <p><a href="<?= $cancel_url ?>" class="button alt">Cancel</a></p>
<?= form_close() ?>
