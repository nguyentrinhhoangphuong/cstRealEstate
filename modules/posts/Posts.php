<?php
class Posts extends Trongate {
    public function manage(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'posts.manage');

        $data['view_module'] = 'posts';
        $data['view_file'] = 'manage';
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
}