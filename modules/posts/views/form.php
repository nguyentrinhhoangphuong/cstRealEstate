<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<?= form_open($form_location) ?>
<div class="form-group">
    <label>Tiêu đề *</label>
    <?= form_input('title', out($post->title ?? ''), ['required' => true, 'placeholder' => 'Nhập tiêu đề...']) ?>
</div>
<div class="form-group">
    <label>Nội dung</label>
    <?= form_textarea('body', $post->body ?? '', ['placeholder' => 'Nhập nội dung...', 'rows' => 10]) ?>
</div>
<div class="form-group">
    <label>Loại *</label>
    <select name="type" required>
        <option value="">-- Chọn loại --</option>
        <?php foreach ($types as $t): ?>
        <option value="<?= $t->slug ?>"<?= ($post->type ?? '') === $t->slug ? ' selected' : '' ?>><?= out($t->name) ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label>Trạng thái</label>
    <select name="status">
        <option value="draft"<?= ($post->status ?? 'draft') === 'draft' ? ' selected' : '' ?>>Nháp</option>
        <option value="publish"<?= ($post->status ?? '') === 'publish' ? ' selected' : '' ?>>Xuất bản</option>
    </select>
</div>
<div class="form-group actions">
    <button type="submit" class="button alt">Lưu</button>
    <a href="<?= $cancel_url ?>" class="button">Huỷ</a>
</div>
<?= form_close() ?>
