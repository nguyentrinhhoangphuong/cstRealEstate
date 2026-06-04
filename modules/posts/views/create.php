<h1>Thêm bài viết</h1>
<?php echo flashdata(); ?>
<?= form_open('posts/submit') ?>
<div class="form-group">
    <label>Tiêu đề *</label>
    <?= form_input('title', '', ['placeholder' => 'Nhập tiêu đề...', 'required' => true]) ?>
</div>
<div class="form-group">
    <label>Nội dung</label>
    <?= form_textarea('body', '', ['placeholder' => 'Nhập nội dung...', 'rows' => 10]) ?>
</div>
<div class="form-group">
    <label>Loại *</label>
    <?= form_dropdown('type', [
        'post' => 'Bài viết chung',
        'news' => 'Tin tức',
        'property' => 'Bất động sản',
        'product' => 'Sản phẩm'
    ], 'post') ?>
</div>
<div class="form-group">
    <label>Trạng thái</label>
    <?= form_dropdown('status', [
        'draft' => 'Nháp',
        'publish' => 'Xuất bản'
    ], 'draft') ?>
</div>
<div class="form-group actions">
    <button type="submit" name="submit" value="Save" class="button alt">Đăng bài</button>
    <a href="<?= BASE_URL ?>posts/manage" class="button">Huỷ</a>
</div>
<?= form_close() ?>