<?php
class Posts extends Trongate {
    public function manage(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');
        
        $type_filter = $_GET['type'] ?? '';
        $status_filter = $_GET['status'] ?? '';

        $limit = 10;
        $page_num = max(1, segment(3, 'int') ?: 1);
        $offset = ($page_num > 1) ? ($page_num - 1) * $limit : 0;

        if ($type_filter !== '' && $status_filter !== '') {
            $total_rows = $this->model->count_by_type_and_status($type_filter, $status_filter);
            $rows = $this->model->get_by_type_and_status($type_filter, $status_filter, $limit, $offset);
        } elseif ($type_filter !== '') {
            $total_rows = $this->model->count_by_type($type_filter);
            $rows = $this->model->get_by_type($type_filter, $limit, $offset);
        } elseif ($status_filter !== '') {
            $total_rows = $this->model->count_by_status($status_filter);
            $rows = $this->model->get_by_status($status_filter, $limit, $offset);
        } else {
            $total_rows = $this->model->count_all();
            $rows = $this->model->get_all($limit, $offset);
        }
        
        $type_counts = $this->model->get_type_counts();
        $all_types = $this->model->get_types();
        $type_names = [];
        foreach ($all_types as $t) {
            $type_names[$t->slug] = $t->name;
        }

        $data['headline'] = 'Quản lý Bài viết';
        $data['rows'] = $rows;
        $data['type_counts'] = $type_counts;
        $data['type_names'] = $type_names;
        $data['current_type'] = $type_filter;
        $data['current_status'] = $status_filter;
        $data['pagination_data'] = [
            'total_rows' => $total_rows,
            'page_num_segment' => 3,
            'limit' => $limit,
            'pagination_root' => '',
            'record_name_plural' => 'bài viết',
            'include_showing_statement' => true,
            'showing_statement' => 'Hiển thị {start} đến {end} / {total} bài viết.',
            'num_links_per_page' => 7,
        ];
        $data['view_module'] = 'posts';
        $data['view_file'] = 'manage';
        $data['additional_includes_top'] = ['posts_module/css/posts.css'];
        $data['additional_includes_btm'] = ['posts_module/js/posts.js'];
        $this->templates->admin($data);
    }

    public function create(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.create');
        $data['headline'] = 'Thêm bài viết';
        $data['post'] = null;
        $data['types'] = $this->model->get_types();
        $data['form_location'] = BASE_URL . 'posts/submit';
        $data['cancel_url'] = BASE_URL . 'posts/manage';
        $data['view_module'] = 'posts';
        $data['view_file'] = 'form';
        $this->templates->admin($data);
    }

    public function edit(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.edit');
        $id = segment(3, 'int');
        $post = $this->model->get_by_id($id);
        if (!$post) { redirect('posts/manage'); return; }
        $data['headline'] = 'Sửa bài viết';
        $data['post'] = $post;
        $data['types'] = $this->model->get_types();
        $data['form_location'] = BASE_URL . 'posts/submit/' . $id;
        $data['cancel_url'] = BASE_URL . 'posts/manage';
        $data['view_module'] = 'posts';
        $data['view_file'] = 'form';
        $this->templates->admin($data);
    }

    public function submit(): void {
        $this->trongate_security->make_sure_allowed();
        $id = segment(3, 'int') ?: null;

        if ($id) {
            Modules::run('trongate_rbac/require_permission', 'posts.edit');
        } else {
            Modules::run('trongate_rbac/require_permission', 'posts.create');
        }

        $data['title'] = post('title', true);
        $data['body'] = post('body');
        $data['type'] = post('type', true);
        $data['status'] = post('status', true);
        $data['slug'] = url_title($data['title'], 'dash', true);

        if ($id) {
            $this->model->update_record($id, $data);
            set_flashdata('Đã cập nhật bài viết.');
        } else {
            $data['created_by'] = $this->trongate_tokens->get_user_id();
            $this->model->create($data);
            set_flashdata('Bài viết đã được tạo thành công!');
        }

        redirect('posts/manage');
    }

    public function delete_conf(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.delete');
        $id = segment(3, 'int');
        $post = $this->model->get_by_id($id);
        if (!$post) { redirect('posts/manage'); return; }
        $data['headline'] = 'Xoá bài viết';
        $data['post'] = $post;
        $data['form_location'] = BASE_URL . 'posts/submit_delete/' . $id;
        $data['cancel_url'] = BASE_URL . 'posts/manage';
        $data['view_module'] = 'posts';
        $data['view_file'] = 'delete_conf';
        $this->templates->admin($data);
    }

    public function submit_delete(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.delete');
        $id = segment(3, 'int');
        $post = $this->model->get_by_id($id);
        if (!$post) { redirect('posts/manage'); return; }
        $this->model->delete_record($id);
        set_flashdata('Đã xoá bài viết.');
        redirect('posts/manage');
    }

    public function toggle_status(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.edit');
        $id = segment(3, 'int');
        $post = $this->model->get_by_id($id);
        if (!$post) {
            echo json_encode(['success' => false, 'error' => 'Not found']);
            return;
        }
        $new_status = $post->status === 'publish' ? 'draft' : 'publish';
        $title = $post->title;
        $this->model->update_record($id, ['status' => $new_status]);
        echo json_encode([
            'success' => true,
            'status' => $new_status,
            'title' => $title,
            'label' => $new_status === 'publish' ? 'xuất bản' : 'nháp'
        ]);
    }

}