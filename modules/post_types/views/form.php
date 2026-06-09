<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<?= form_open($form_location) ?>
<div class="form-group">
    <label>Tên loại *</label>
    <?= form_input('name', out($type->name ?? ''), ['required' => true, 'placeholder' => 'VD: Tin tức, Bất động sản...']) ?>
</div>
<div class="form-group">
    <label>Slug</label>
    <?= form_input('slug', out($type->slug ?? ''), ['placeholder' => 'Để trống để tự động tạo từ tên...']) ?>
</div>
<div class="form-group actions">
    <button type="submit" class="button alt">Lưu</button>
    <a href="<?= $cancel_url ?>" class="button">Huỷ</a>
</div>
<?= form_close() ?>
