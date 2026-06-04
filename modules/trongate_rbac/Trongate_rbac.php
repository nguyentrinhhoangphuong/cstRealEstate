<?php
class Trongate_rbac extends Trongate {

    private int $default_limit = 20;
    private array $per_page_options = [10, 20, 50, 100];

    // ── Permission check (callable from other modules) ─────────────────

    public function check_permission(string $permission_slug): bool {
        $admin_id = $this->_get_current_admin_id();
        if ($admin_id === 0) return false;
        if ($this->model->is_super_admin($admin_id)) return true;
        return $this->model->has_permission($admin_id, $permission_slug);
    }

    public function require_permission(string $permission_slug): void {
        if (!$this->check_permission($permission_slug)) {
            set_flashdata('You do not have permission to perform this action.');
            redirect('dashboard');
        }
    }

    // ── Roles ──────────────────────────────────────────────────────────

    public function manage_roles(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.roles.manage');

        $limit = $this->_limit();
        $page_num = segment(3, 'int') ?: 1;
        $offset = ($page_num > 1) ? ($page_num - 1) * $limit : 0;

        $rows = $this->model->get_all_roles();
        $total = $this->model->count_roles();

        $data = [
            'headline' => 'Manage Roles',
            'rows' => $rows,
            'pagination_data' => [
                'total_rows' => $total,
                'page_num_segment' => 3,
                'limit' => $limit,
                'pagination_root' => 'trongate_rbac/manage_roles',
                'record_name_plural' => 'roles',
                'include_showing_statement' => true
            ],
            'view_module' => $this->module_name,
            'view_file' => 'manage_roles',
            'per_page_options' => $this->per_page_options,
            'selected_per_page' => $this->_selected_per_page()
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function create_role(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.roles.create');
        $data['headline'] = 'Create Role';
        $data['form_location'] = BASE_URL . 'trongate_rbac/submit_role';
        $data['cancel_url'] = BASE_URL . 'trongate_rbac/manage_roles';
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'create_role';
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function submit_role(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.roles.create');
        $name = post('name', true);
        if (empty($name)) {
            set_flashdata('Role name is required');
            redirect('trongate_rbac/create_role');
        }
        $slug = post('slug', true);
        if (empty($slug)) {
            $slug = str_replace(' ', '-', strtolower(trim($name)));
        }
        $existing = $this->model->get_role_by_slug($slug);
        if ($existing) {
            set_flashdata('A role with slug "' . $slug . '" already exists');
            redirect('trongate_rbac/create_role');
        }
        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => post('description', true),
            'is_system' => 0,
            'created_at' => time()
        ];
        $this->model->create_role($data);
        set_flashdata('Role "' . $name . '" created successfully');
        redirect('trongate_rbac/manage_roles');
    }

    public function update_role(): void {
        $this->trongate_security->make_sure_allowed();
        $update_id = segment(3, 'int');
        $role = $this->model->get_role($update_id);
        if (!$role) { $this->not_found(); return; }
        if ($role->is_system) {
            $this->require_permission('rbac.roles.manage');
        } else {
            $this->require_permission('rbac.roles.edit');
        }
        $data['headline'] = 'Edit Role';
        $data['form_location'] = BASE_URL . 'trongate_rbac/submit_update_role/' . $update_id;
        $data['cancel_url'] = BASE_URL . 'trongate_rbac/manage_roles';
        $data['role'] = $role;
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'create_role';
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function submit_update_role(): void {
        $update_id = segment(3, 'int');
        $role = $this->model->get_role($update_id);
        if (!$role) { $this->not_found(); return; }
        $this->trongate_security->make_sure_allowed();
        if ($role->is_system) {
            $this->require_permission('rbac.roles.manage');
        } else {
            $this->require_permission('rbac.roles.edit');
        }
        $name = post('name', true);
        if (empty($name)) {
            set_flashdata('Role name is required');
            redirect('trongate_rbac/update_role/' . $update_id);
        }
        $slug = post('slug', true);
        if (empty($slug)) {
            $slug = str_replace(' ', '-', strtolower(trim($name)));
        }
        $existing = $this->model->get_role_by_slug($slug);
        if ($existing && $existing->id !== $update_id) {
            set_flashdata('A role with slug "' . $slug . '" already exists');
            redirect('trongate_rbac/update_role/' . $update_id);
        }
        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => post('description', true)
        ];
        $this->model->update_role($update_id, $data);
        set_flashdata('Role updated successfully');
        redirect('trongate_rbac/manage_roles');
    }

    public function delete_role_conf(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.roles.delete');
        $update_id = segment(3, 'int');
        $role = $this->model->get_role($update_id);
        if (!$role) { $this->not_found(); return; }
        if ($role->is_system) {
            set_flashdata('System roles cannot be deleted');
            redirect('trongate_rbac/manage_roles');
        }
        $data = [
            'headline' => 'Delete Role',
            'record' => $role,
            'cancel_url' => BASE_URL . 'trongate_rbac/manage_roles',
            'form_location' => BASE_URL . 'trongate_rbac/submit_delete_role/' . $update_id,
            'view_module' => $this->module_name,
            'view_file' => 'delete_role_conf'
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function submit_delete_role(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.roles.delete');
        $submit = post('submit', true);
        if ($submit !== 'Yes - Delete Now') {
            redirect('trongate_rbac/manage_roles');
        }
        $update_id = segment(3, 'int');
        $role = $this->model->get_role($update_id);
        if (!$role || $role->is_system) {
            redirect('trongate_rbac/manage_roles');
        }
        $this->model->delete_role($update_id);
        set_flashdata('Role "' . $role->name . '" deleted successfully');
        redirect('trongate_rbac/manage_roles');
    }

    // ── Permissions ────────────────────────────────────────────────────

    public function manage_permissions(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.manage');
        $rows = $this->model->get_all_permissions();
        $data = [
            'headline' => 'Manage Permissions',
            'rows' => $rows,
            'view_module' => $this->module_name,
            'view_file' => 'manage_permissions'
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function create_permission(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.create');
        $data['headline'] = 'Create Permission';
        $data['form_location'] = BASE_URL . 'trongate_rbac/submit_permission';
        $data['cancel_url'] = BASE_URL . 'trongate_rbac/manage_permissions';
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'create_permission';
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function submit_permission(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.create');
        $name = post('name', true);
        if (empty($name)) {
            set_flashdata('Permission name is required');
            redirect('trongate_rbac/create_permission');
        }
        $slug = post('slug', true);
        if (empty($slug)) {
            set_flashdata('Permission slug is required');
            redirect('trongate_rbac/create_permission');
        }
        $existing = $this->model->get_permission_by_slug($slug);
        if ($existing) {
            set_flashdata('A permission with slug "' . $slug . '" already exists');
            redirect('trongate_rbac/create_permission');
        }
        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => post('description', true),
            'module' =>  strtolower(post('module', true)),
            'created_at' => time()
        ];
        $this->model->create_permission($data);
        set_flashdata('Permission "' . $name . '" created successfully');
        redirect('trongate_rbac/manage_permissions');
    }

    public function update_permission(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.edit');
        $update_id = segment(3, 'int');
        $perm = $this->model->get_permission($update_id);
        if (!$perm) { $this->not_found(); return; }
        $data['headline'] = 'Edit Permission';
        $data['form_location'] = BASE_URL . 'trongate_rbac/submit_update_permission/' . $update_id;
        $data['cancel_url'] = BASE_URL . 'trongate_rbac/manage_permissions';
        $data['permission'] = $perm;
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'create_permission';
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function submit_update_permission(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.edit');
        $update_id = segment(3, 'int');
        $perm = $this->model->get_permission($update_id);
        if (!$perm) { $this->not_found(); return; }
        $name = post('name', true);
        $slug = post('slug', true);
        if (empty($name) || empty($slug)) {
            set_flashdata('Name and slug are required');
            redirect('trongate_rbac/update_permission/' . $update_id);
        }
        $existing = $this->model->get_permission_by_slug($slug);
        if ($existing && $existing->id !== $update_id) {
            set_flashdata('A permission with slug "' . $slug . '" already exists');
            redirect('trongate_rbac/update_permission/' . $update_id);
        }
        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => post('description', true),
            'module' => post('module', true)
        ];
        $this->model->update_permission($update_id, $data);
        set_flashdata('Permission updated successfully');
        redirect('trongate_rbac/manage_permissions');
    }

    public function delete_permission_conf(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.delete');
        $update_id = segment(3, 'int');
        $perm = $this->model->get_permission($update_id);
        if (!$perm) { $this->not_found(); return; }
        $data = [
            'headline' => 'Delete Permission',
            'record' => $perm,
            'cancel_url' => BASE_URL . 'trongate_rbac/manage_permissions',
            'form_location' => BASE_URL . 'trongate_rbac/submit_delete_permission/' . $update_id,
            'view_module' => $this->module_name,
            'view_file' => 'delete_permission_conf'
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $this->templates->admin($data);
    }

    public function submit_delete_permission(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.permissions.delete');
        $submit = post('submit', true);
        if ($submit !== 'Yes - Delete Now') {
            redirect('trongate_rbac/manage_permissions');
        }
        $update_id = segment(3, 'int');
        $perm = $this->model->get_permission($update_id);
        if (!$perm) { redirect('trongate_rbac/manage_permissions'); }
        $this->model->delete_permission($update_id);
        set_flashdata('Permission "' . $perm->name . '" deleted successfully');
        redirect('trongate_rbac/manage_permissions');
    }

    // ── Assign Permissions to Role ─────────────────────────────────────

    public function assign_permissions(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.assign.permissions');
        $role_id = segment(3, 'int');
        $role = $this->model->get_role($role_id);
        if (!$role) { $this->not_found(); return; }

        $grouped = $this->model->get_permissions_grouped_by_module();
        $assigned_ids = $this->model->get_role_permission_ids($role_id);

        $data = [
            'headline' => 'Assign Permissions — ' . $role->name,
            'role' => $role,
            'grouped_permissions' => $grouped,
            'assigned_ids' => $assigned_ids,
            'form_location' => BASE_URL . 'trongate_rbac/submit_assign_permissions/' . $role_id,
            'cancel_url' => BASE_URL . 'trongate_rbac/manage_roles',
            'view_module' => $this->module_name,
            'view_file' => 'assign_permissions'
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $data['additional_includes_btm'] = [
            BASE_URL . 'trongate_rbac_module/js/rbac.js'
        ];
        $this->templates->admin($data);
    }

    public function submit_assign_permissions(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.assign.permissions');
        $role_id = segment(3, 'int');
        $role = $this->model->get_role($role_id);
        if (!$role) { $this->not_found(); return; }

        $permission_ids = post('permissions') ?? [];
        $this->model->set_role_permissions($role_id, $permission_ids);
        set_flashdata('Permissions updated for role "' . $role->name . '"');
        redirect('trongate_rbac/manage_roles');
    }

    // ── Assign Roles to Admin ──────────────────────────────────────────

    public function assign_roles(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.assign.roles');
        $admin_id = segment(3, 'int');
        $sql = "SELECT id, username, email, is_super_admin FROM trongate_administrators WHERE id = :id LIMIT 1";
        $rows = $this->model->db->query_bind($sql, ['id' => $admin_id], 'object');
        if (empty($rows)) { $this->not_found(); return; }
        $admin = $rows[0];

        $all_roles = $this->model->get_all_roles();
        $assigned_ids = $this->model->get_admin_role_ids($admin_id);

        $data = [
            'headline' => 'Assign Roles — ' . $admin->username,
            'admin' => $admin,
            'all_roles' => $all_roles,
            'assigned_ids' => $assigned_ids,
            'form_location' => BASE_URL . 'trongate_rbac/submit_assign_roles/' . $admin_id,
            'cancel_url' => BASE_URL . 'trongate_rbac/manage_roles',
            'view_module' => $this->module_name,
            'view_file' => 'assign_roles'
        ];
        $data['additional_includes_top'] = [
            BASE_URL . 'trongate_rbac_module/css/rbac.css'
        ];
        $data['additional_includes_btm'] = [
            BASE_URL . 'trongate_rbac_module/js/rbac.js'
        ];
        $this->templates->admin($data);
    }

    public function submit_assign_roles(): void {
        $this->trongate_security->make_sure_allowed();
        $this->require_permission('rbac.assign.roles');
        $admin_id = segment(3, 'int');
        $sql = "SELECT id, username FROM trongate_administrators WHERE id = :id LIMIT 1";
        $rows = $this->model->db->query_bind($sql, ['id' => $admin_id], 'object');
        if (empty($rows)) { $this->not_found(); return; }

        $role_ids = post('roles') ?? [];
        $this->model->set_admin_roles($admin_id, $role_ids);
        set_flashdata('Roles updated for "' . $rows[0]->username . '"');
        redirect('trongate_rbac/manage_roles');
    }

    // ── Helpers ────────────────────────────────────────────────────────

    public function not_found(): void {
        $data = [
            'headline' => 'Not Found',
            'message' => 'The requested record could not be found.',
            'back_url' => BASE_URL . 'trongate_rbac/manage_roles',
            'back_label' => 'Back to Roles',
            'view_module' => $this->module_name,
            'view_file' => 'not_found'
        ];
        $this->templates->admin($data);
    }

    private function _get_current_admin_id(): int {
        $token = $this->trongate_tokens->attempt_get_valid_token(1);
        if ($token === false) return 0;
        $sql = "SELECT user_id FROM trongate_tokens WHERE token = :token LIMIT 1";
        $rows = $this->model->db->query_bind($sql, ['token' => $token], 'object');
        if (empty($rows)) return 0;
        $trongate_user_id = (int) $rows[0]->user_id;
        $sql = "SELECT id FROM trongate_administrators WHERE trongate_user_id = :uid LIMIT 1";
        $rows = $this->model->db->query_bind($sql, ['uid' => $trongate_user_id], 'object');
        return !empty($rows) ? (int) $rows[0]->id : 0;
    }

    private function _limit(): int {
        $limit = $this->default_limit;
        $selected = $this->_selected_per_page();
        if (isset($this->per_page_options[$selected])) {
            $limit = $this->per_page_options[$selected];
        }
        return $limit;
    }

    private function _selected_per_page(): int {
        return (int) ($_SESSION['trongate_rbac_selected_per_page'] ?? 1);
    }

    public function set_per_page(): void {
        $this->trongate_security->make_sure_allowed();
        $selected_index = segment(3, 'int');
        if (!isset($this->per_page_options[$selected_index])) {
            $selected_index = 1;
        }
        $_SESSION['trongate_rbac_selected_per_page'] = $selected_index;
        redirect('trongate_rbac/manage_roles');
    }
}
