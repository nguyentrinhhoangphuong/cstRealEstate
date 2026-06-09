<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<p>Bạn có chắc muốn xoá bài viết <strong>"<?= out($post->title) ?>"</strong>?</p>

<?= form_open($form_location) ?>
<div class="form-group actions">
    <button type="submit" class="btn-delete">Xoá</button>
    <a href="<?= $cancel_url ?>" class="button">Huỷ</a>
</div>
<?= form_close() ?>
