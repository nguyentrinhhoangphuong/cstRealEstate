<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>

<div class="posts-toolbar">
    <div style="display:flex;gap:8px">
        <a href="<?= BASE_URL ?>posts/create" class="button alt">Thêm bài viết</a>
    </div>
    <div style="display:flex;gap:8px">
        <select id="typeFilter" class="posts-filter">
            <option value=""<?= !$current_type ? ' selected' : '' ?>>Tất cả loại (<?= array_sum(array_column($type_counts, 'total')) ?>)</option>
            <?php foreach ($type_counts as $tc): ?>
            <option value="<?= $tc->type ?>"<?= $current_type === $tc->type ? ' selected' : '' ?>>
                <?= out($type_names[$tc->type] ?? ucfirst($tc->type)) ?> (<?= $tc->total ?>)
            </option>
            <?php endforeach; ?>
        </select>

        <select id="statusFilter" class="posts-filter">
            <option value=""<?= !$current_status ? ' selected' : '' ?>>Tất cả trạng thái</option>
            <option value="publish"<?= $current_status === 'publish' ? ' selected' : '' ?>>Xuất bản</option>
            <option value="draft"<?= $current_status === 'draft' ? ' selected' : '' ?>>Nháp</option>
        </select>
            <a href="<?= BASE_URL ?>posts/manage" class="button" style="<?= $current_type || $current_status ? '' : 'display:none;' ?> margin: unset; line-height: unset">Reset</a>
    </div>

</div>

<?php if (empty($rows)): ?>
<p style="text-align:center;color:#888">Chưa có bài viết nào.</p>
<?php else: ?>
<table class="records-table" style="margin-top:16px">
    <thead>
        <tr>
            <th>Tiêu đề</th>
            <th>Loại bài viết</th>
            <th>Trạng thái</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= out($row->title) ?></td>
            <td><?= out($row->category_title ?? $row->type) ?></td>
            <td>
                <span class="status-toggle post-update-status <?= $row->status === 'publish'? 'posts-publish' : 'posts-draft' ?>" data-id="<?= $row->id ?>" data-status="<?= $row->status ?>">
                    <?= $row->status === 'publish' ? 'xuất bản' : 'nháp' ?>
                </span>
            </td>
            <td><?= date('d/m/Y', strtotime($row->created_at)) ?></td>
            <td style="white-space:nowrap">
                <a href="<?= BASE_URL ?>posts/edit/<?= $row->id ?>"><i class="tg tg-pencil"></i></a>
                <a href="<?= BASE_URL ?>posts/delete_conf/<?= $row->id ?>" class="btn-delete" onclick="return confirm('Xoá bài viết này?')"><i class="tg tg-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo Modules::run('pagination/display', $pagination_data); ?>
<?php endif; ?>