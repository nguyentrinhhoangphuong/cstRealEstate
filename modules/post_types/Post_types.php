<?php
class Post_types extends Trongate {

    public function manage(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $data['headline'] = 'Quản lý Loại bài viết';
        $data['rows'] = $this->model->get_types();
        $data['view_module'] = 'post_types';
        $data['view_file'] = 'manage';
        $data['additional_includes_btm'] = [
            'post_types_module/js/post_types.js'
        ];
        $this->templates->admin($data);
    }

    public function create(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $data['headline'] = 'Thêm loại bài viết';
        $data['type'] = null;
        $data['form_location'] = BASE_URL . 'post_types/submit';
        $data['cancel_url'] = BASE_URL . 'post_types/manage';
        $data['view_module'] = 'post_types';
        $data['view_file'] = 'form';
        $this->templates->admin($data);
    }

    public function edit(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $id = segment(3, 'int');
        $type = $this->model->get_type_by_id($id);
        if (!$type) { redirect('post_types/manage'); return; }

        $data['headline'] = 'Sửa loại bài viết';
        $data['type'] = $type;
        $data['form_location'] = BASE_URL . 'post_types/submit/' . $id;
        $data['cancel_url'] = BASE_URL . 'post_types/manage';
        $data['view_module'] = 'post_types';
        $data['view_file'] = 'form';
        $this->templates->admin($data);
    }

    public function submit(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $id = segment(3, 'int') ?: null;

        $data['name'] = post('name', true);
        $slug_input = post('slug', true);
        $data['slug'] = $slug_input !== '' ? url_title($slug_input, 'dash', true) : url_title($data['name'], 'dash', true);

        if ($data['name'] === '') {
            set_flashdata('Vui lòng nhập tên loại.');
            redirect($id ? 'post_types/edit/' . $id : 'post_types/create');
        }

        if ($this->model->type_slug_exists($data['slug'], $id)) {
            set_flashdata('Loại này đã tồn tại.');
            redirect($id ? 'post_types/edit/' . $id : 'post_types/create');
        }

        if ($id) {
            $old_type = $this->model->get_type_by_id($id);
            $old_slug = $old_type->slug;
            $this->model->update_type($id, $data);
            if ($old_slug !== $data['slug']) {
                $this->model->update_posts_type_slug($old_slug, $data['slug']);
            }
            set_flashdata('Đã cập nhật loại bài viết.');
        } else {
            $data['sort_order'] = $this->model->get_next_sort_order();
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->model->create_type($data);
            set_flashdata('Đã thêm loại bài viết.');
        }

        redirect('post_types/manage');
    }

    public function delete_conf(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $id = segment(3, 'int');
        $type = $this->model->get_type_by_id($id);
        if (!$type) { redirect('post_types/manage'); return; }

        $post_count = $this->model->count_posts_by_type($type->slug);

        $data['headline'] = 'Xoá loại bài viết';
        $data['type'] = $type;
        $data['post_count'] = $post_count;
        $data['form_location'] = BASE_URL . 'post_types/submit_delete/' . $id;
        $data['cancel_url'] = BASE_URL . 'post_types/manage';
        $data['view_module'] = 'post_types';
        $data['view_file'] = 'delete_conf';
        $this->templates->admin($data);
    }

    public function submit_delete(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $id = segment(3, 'int');
        $type = $this->model->get_type_by_id($id);
        if (!$type) { redirect('post_types/manage'); return; }

        $post_count = $this->model->count_posts_by_type($type->slug);
        if ($post_count > 0) {
            set_flashdata('Không thể xoá. Còn ' . $post_count . ' bài viết thuộc loại "' . $type->name . '".');
            redirect('post_types/manage');
        }

        $this->model->delete_type($id);
        set_flashdata('Đã xoá loại bài viết.');
        redirect('post_types/manage');
    }

    public function reorder(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');
        $ids_json = post('ids');
        if (!$ids_json) {
            echo json_encode(['success' => false]);
            return;
        }
        $ids = json_decode($ids_json);
        if (!is_array($ids) || empty($ids)) {
            echo json_encode(['success' => false]);
            return;
        }
        $this->model->update_sort_order($ids);
        echo json_encode(['success' => true]);
    }
}
