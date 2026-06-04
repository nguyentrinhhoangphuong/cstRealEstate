<?php
class Media extends Trongate {

    private int $default_limit = 20;
    private array $per_page_options = [10, 20, 50, 100];
    private string $upload_dir;

    public function __construct(?string $module_name = null) {
        parent::__construct($module_name);
        $this->upload_dir = APPPATH . 'public/uploads/media';
    }

    public function create(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.upload');
        $folder_id = segment(3, 'int');
        $data['headline'] = 'Upload Media';
        $data['folder_id'] = $folder_id;
        $data['folder_name'] = '';
        if ($folder_id > 0) {
            $folder = $this->model->get_folder($folder_id);
            $data['folder_name'] = $folder ? $folder->name : '';
        }
        $data['cancel_url'] = $folder_id > 0 ? BASE_URL . 'media/folder/' . $folder_id : BASE_URL . 'media/manage';
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'upload';
        $data['additional_includes_top'] = [
            BASE_URL . 'media_module/css/media.css'
        ];
        $data['additional_includes_btm'] = [
            BASE_URL . 'media_module/js/media.js'
        ];
        $this->templates->admin($data);
    }

    public function submit(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.upload');

        if (empty($_FILES)) {
            set_flashdata('No file was uploaded');
            redirect('media/create');
        }

        $folder_id = (int) post('folder_id');
        $userfile = array_keys($_FILES)[0];
        $files = $_FILES[$userfile];

        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'mp4', 'mp3', 'avi', 'mov', 'txt', 'csv'];
        $count = 0;

        if (is_array($files['name'])) {
            $total = count($files['name']);
            for ($i = 0; $i < $total; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed)) continue;
                if ($this->_save_file($files['name'][$i], $files['tmp_name'][$i], $files['type'][$i], $files['size'][$i], $ext, $folder_id)) {
                    $count++;
                }
            }
        } else {
            $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                if ($this->_save_file($files['name'], $files['tmp_name'], $files['type'], $files['size'], $ext, $folder_id)) {
                    $count++;
                }
            } else {
                set_flashdata('File type not allowed: .' . $ext);
                redirect('media/create');
            }
        }

        set_flashdata($count . ' file(s) uploaded successfully');
        if ($folder_id > 0) {
            redirect('media/folder/' . $folder_id);
        } else {
            redirect('media/manage');
        }
    }

    private function _save_file(string $orig_name, string $tmp_name, string $type, int $size, string $ext, int $folder_id = 0): bool {
        $rand_name = make_rand_str(20);
        $dest_filename = $rand_name . '.' . $ext;
        $dest_path = $this->upload_dir . '/' . $dest_filename;

        if (!move_uploaded_file($tmp_name, $dest_path)) {
            return false;
        }

        $data = [
            'filename' => $dest_filename,
            'original_filename' => $orig_name,
            'filepath' => 'public/uploads/media/' . $dest_filename,
            'filesize' => $size,
            'filetype' => $type,
            'extension' => $ext,
            'alt_text' => '',
            'caption' => '',
            'folder_id' => $folder_id,
            'uploaded_by' => $this->_get_user_id(),
            'created_at' => time()
        ];

        $this->model->insert($data);
        return true;
    }

    public function show(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.manage');
        $update_id = segment(3, 'int');
        $record_data = $this->model->get_data_from_db($update_id);
        if ($record_data === false) {
            $this->not_found();
            return;
        }
        $data = $this->model->prepare_for_display($record_data);
        $data['update_id'] = $update_id;
        $data['headline'] = $data['original_filename'];
        $data['back_url'] = $this->_back_url();
        $data['can_edit'] = Modules::run('trongate_rbac/check_permission', 'media.edit');
        $data['can_delete'] = Modules::run('trongate_rbac/check_permission', 'media.delete');
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'show';
        $data['additional_includes_top'] = [
            BASE_URL . 'media_module/css/media.css'
        ];
        $this->templates->admin($data);
    }

    public function update_meta(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.edit');
        $update_id = segment(3, 'int');
        $submit = post('submit', true);
        if ($submit !== 'Save') {
            redirect('media/manage');
        }
        $this->validation->set_rules('alt_text', 'alt text', 'max_length[500]');
        if ($this->validation->run() !== true) {
            $this->show();
            return;
        }
        $post_data = $this->model->get_data_from_post();
        $this->model->update($update_id, $post_data);
        set_flashdata('Media updated successfully');
        redirect('media/show/' . $update_id);
    }

    public function manage(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.manage');
        $this->_render_manage(0, segment(3, 'int'));
    }

    public function folder(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.manage');
        $folder_id = segment(3, 'int');
        if ($folder_id === 0) {
            redirect('media/manage');
        }
        $this->_render_manage($folder_id, segment(4, 'int'));
    }

    private function _render_manage(int $folder_id, int $page_num): void {
        $folder_name = '';
        if ($folder_id > 0) {
            $folder = $this->model->get_folder($folder_id);
            $folder_name = $folder ? $folder->name : '';
        }

        $limit = $this->_limit();
        $offset = ($page_num > 1) ? ($page_num - 1) * $limit : 0;

        $rows = $this->model->get_all_paginated($limit, $offset, $folder_id);
        $rows = $this->model->prepare_records_for_display($rows);
        $total_rows = $this->model->count_all($folder_id);
        $folders = $this->model->get_folders();

        $headline = $folder_name ? 'Media Library &raquo; ' . $folder_name : 'Media Library';
        $page_seg = $folder_id > 0 ? 4 : 3;
        $pagination_root = $folder_id > 0 ? $this->module_name . '/folder/' . $folder_id : $this->module_name . '/manage';

        $data = [
            'headline' => $headline,
            'rows' => $rows,
            'folders' => $folders,
            'current_folder_id' => $folder_id,
            'can_upload' => Modules::run('trongate_rbac/check_permission', 'media.upload'),
            'can_edit' => Modules::run('trongate_rbac/check_permission', 'media.edit'),
            'can_delete' => Modules::run('trongate_rbac/check_permission', 'media.delete'),
            'can_create_folders' => Modules::run('trongate_rbac/check_permission', 'media.folders.create'),
            'can_rename_folders' => Modules::run('trongate_rbac/check_permission', 'media.folders.rename'),
            'can_delete_folders' => Modules::run('trongate_rbac/check_permission', 'media.folders.delete'),
            'pagination_data' => [
                'total_rows' => $total_rows,
                'page_num_segment' => $page_seg,
                'limit' => $limit,
                'pagination_root' => $pagination_root,
                'record_name_plural' => 'files',
                'include_showing_statement' => true
            ],
            'view_module' => $this->module_name,
            'view_file' => 'manage',
            'per_page_options' => $this->per_page_options,
            'selected_per_page' => $this->_selected_per_page()
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'media_module/css/media.css'
        ];
        $data['additional_includes_btm'] = [
            BASE_URL . 'media_module/js/media.js'
        ];
        $this->templates->admin($data);
    }

    public function delete_conf(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.delete');
        $update_id = segment(3, 'int');
        $record_data = $this->model->get_data_from_db($update_id);
        if ($record_data === false) {
            $this->not_found();
            return;
        }
        $data['update_id'] = $update_id;
        $data['headline'] = 'Delete Media';
        $data['cancel_url'] = BASE_URL . $this->module_name . '/show/' . $update_id;
        $data['form_location'] = BASE_URL . $this->module_name . '/submit_delete/' . $update_id;
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'delete_conf';
        $this->templates->admin($data);
    }

    public function submit_delete(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.delete');
        $submit = post('submit', true);
        if ($submit !== 'Yes - Delete Now') {
            redirect($this->module_name . '/manage');
        }
        $update_id = segment(3, 'int');
        if ($update_id === 0) {
            redirect($this->module_name . '/manage');
        }
        $this->model->delete_record($update_id);
        set_flashdata('The file was successfully deleted');
        redirect($this->module_name . '/manage');
    }

    public function fetch(): void {
        $update_id = segment(3, 'int');
        if ($update_id === 0) {
            http_response_code(404);
            die();
        }
        $record = $this->model->get_data_from_db($update_id);
        $full_path = APPPATH . $record['filepath'];
        if ($record === false || !file_exists($full_path)) {
            http_response_code(404);
            die();
        }
        $mime = $record['filetype'] ?: mime_content_type($full_path);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . $record['filesize']);
        header('Cache-Control: public, max-age=31536000');
        readfile($full_path);
        die();
    }

    public function not_found(): void {
        $data = [
            'headline' => 'Media Not Found',
            'message' => 'The media file you\'re looking for doesn\'t exist or has been deleted.',
            'back_url' => $this->_back_url(),
            'back_label' => 'Go Back',
            'view_module' => $this->module_name,
            'view_file' => 'not_found'
        ];
        $this->templates->admin($data);
    }

    public function set_per_page(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.manage');
        $selected_index = segment(3, 'int');
        if (!isset($this->per_page_options[$selected_index])) {
            $selected_index = 1;
        }
        $_SESSION['selected_per_page'] = $selected_index;
        redirect($this->module_name . '/manage');
    }

    public function create_folder(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.folders.create');
        $name = post('name', true);
        if (empty($name)) {
            set_flashdata('Folder name is required');
            redirect('media/manage');
        }
        $this->model->create_folder($name, $this->_get_user_id());
        set_flashdata('Folder created successfully');
        redirect('media/manage');
    }

    public function rename_folder(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.folders.rename');
        $folder_id = segment(3, 'int');
        $name = post('name', true);
        if ($folder_id === 0 || empty($name)) {
            http_response_code(400);
            echo 'Invalid request';
            return;
        }
        $this->model->rename_folder($folder_id, $name);
        echo 'OK';
    }

    public function delete_folder(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.folders.delete');
        $folder_id = segment(3, 'int');
        if ($folder_id === 0) {
            redirect('media/manage');
        }
        $this->model->delete_folder($folder_id);
        set_flashdata('Folder deleted successfully');
        redirect('media/manage');
    }

    public function move_to_folder(): void {
        $this->trongate_security->make_sure_allowed();
        Modules::run('trongate_rbac/require_permission', 'media.edit');
        $media_id = (int) post('media_id');
        $folder_id = (int) post('folder_id');
        if ($media_id === 0) {
            http_response_code(400);
            echo 'Invalid media ID';
            return;
        }
        $this->model->move_to_folder($media_id, $folder_id);
        echo 'OK';
    }

    private function _get_user_id(): int {
        $token = $this->trongate_tokens->attempt_get_valid_token(1);
        if ($token === false) return 0;
        $sql = "SELECT user_id FROM trongate_tokens WHERE token = :token LIMIT 1";
        $rows = $this->model->db->query_bind($sql, ['token' => $token], 'object');
        return !empty($rows) ? (int) $rows[0]->user_id : 0;
    }

    private function _back_url(): string {
        $prev = previous_url();
        if ($prev !== '' && strpos($prev, BASE_URL . $this->module_name) === 0) {
            return $prev;
        }
        return BASE_URL . $this->module_name . '/manage';
    }

    private function _limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            return $this->per_page_options[$_SESSION['selected_per_page']];
        }
        return $this->default_limit;
    }

    private function _selected_per_page(): int {
        return $_SESSION['selected_per_page'] ?? 1;
    }
}
