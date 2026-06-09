<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<p>Bạn có chắc muốn xoá loại <strong>"<?= out($type->name) ?>"</strong>?</p>

<?php if ($post_count > 0): ?>
<p style="color:#e74c3c">Không thể xoá. Còn <strong><?= $post_count ?></strong> bài viết thuộc loại này.</p>
<a href="<?= $cancel_url ?>" class="button alt">Quay lại</a>
<?php else: ?>
<?= form_open($form_location) ?>
<div class="form-group actions">
    <button type="submit" class="btn-delete">Xoá</button>
    <a href="<?= $cancel_url ?>" class="button">Huỷ</a>
</div>
<?= form_close() ?>
<?php endif; ?>
