<h1><?= $headline ?></h1>
<?php echo flashdata(); ?>
<a href="<?= BASE_URL ?>post_types/create" class="button alt">Thêm loại</a>

<table class="records-table" style="margin-top:16px">
    <thead>
        <tr>
            <th style="width:30px"></th>
            <th>Tên loại</th>
            <th>Slug</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody id="sortable-types">
        <?php foreach ($rows as $index => $row): ?>
        <tr data-id="<?= $row->id ?>" draggable="true" style="cursor:grab">
            <td style="text-align:center;cursor:grab;font-size:18px;user-select:none">☰</td>
            <td><?= out($row->name) ?></td>
            <td><code><?= out($row->slug) ?></code></td>
            <td>
                <a href="<?= BASE_URL ?>post_types/edit/<?= $row->id ?>"><i class="tg tg-pencil"></i></a>
                <a href="<?= BASE_URL ?>post_types/delete_conf/<?= $row->id ?>" class="btn-delete"><i class="tg tg-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
