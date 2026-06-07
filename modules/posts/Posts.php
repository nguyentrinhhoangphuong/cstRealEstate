<?php
class Posts extends Trongate {
    public function manage(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');
        
        $type_filter = $_GET['type'] ?? '';
        $status_filter = $_GET['status'] ?? '';
        $type_counts = $this->model->get_type_counts();

        if ($type_filter !== '' && $status_filter !== '') {
            $rows = $this->model->get_by_type_and_status($type_filter, $status_filter);
        } elseif ($type_filter !== '') {
            $rows = $this->model->get_by_type($type_filter);
        } elseif ($status_filter !== '') {
            $rows = $this->model->get_by_status($status_filter);
        } else {
            $rows = $this->model->get_all();
        }
        
        $data['headline'] = 'Quản lý Bài viết';
        $data['rows'] = $rows;
        $data['type_counts'] = $type_counts;
        $data['current_type'] = $type_filter;
        $data['current_status'] = $status_filter;
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
        $data['view_module'] = 'posts';
        $data['view_file'] = 'create';
        $this->templates->admin($data);
    }

    public function submit(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.create');
        $data['title'] = post('title', true);
        $data['body'] = post('body');
        $data['type'] = post('type', true);
        $data['status'] = post('status', true);
        $data['slug'] = url_title($data['title'], 'dash', true);
        $data['created_by'] = $this->trongate_tokens->get_user_id();
        $id = $this->model->create($data);
        set_flashdata('Bài viết đã được tạo thành công!');
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