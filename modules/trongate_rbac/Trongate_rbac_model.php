<?php
class Trongate_rbac_model extends Model {

    private string $roles_table = 'trongate_roles';
    private string $permissions_table = 'trongate_permissions';
    private string $role_permissions_table = 'trongate_role_permissions';
    private string $role_assignments_table = 'trongate_role_assignments';

    // ── Roles ──────────────────────────────────────────────────────────

    public function get_all_roles(): array {
        $sql = "SELECT * FROM {$this->roles_table} ORDER BY is_system DESC, name ASC";
        return $this->db->query_bind($sql, [], 'object');
    }

    public function get_role(int $id): object|false {
        $sql = "SELECT * FROM {$this->roles_table} WHERE id = :id LIMIT 1";
        $rows = $this->db->query_bind($sql, ['id' => $id], 'object');
        return $rows[0] ?? false;
    }

    public function create_role(array $data): int {
        return $this->db->insert($data, $this->roles_table);
    }

    public function update_role(int $id, array $data): bool {
        return $this->db->update($id, $data, $this->roles_table);
    }

    public function delete_role(int $id): bool {
        $this->db->query_bind("DELETE FROM {$this->role_permissions_table} WHERE role_id = :id", ['id' => $id]);
        $this->db->query_bind("DELETE FROM {$this->role_assignments_table} WHERE role_id = :id", ['id' => $id]);
        $this->db->delete($id, $this->roles_table);
        return true;
    }

    public function count_roles(): int {
        $rows = $this->db->query_bind("SELECT COUNT(*) AS num FROM {$this->roles_table}", [], 'object');
        return (int) ($rows[0]->num ?? 0);
    }

    public function get_role_by_slug(string $slug): object|false {
        $sql = "SELECT * FROM {$this->roles_table} WHERE slug = :slug LIMIT 1";
        $rows = $this->db->query_bind($sql, ['slug' => $slug], 'object');
        return $rows[0] ?? false;
    }

    // ── Permissions ────────────────────────────────────────────────────

    public function get_all_permissions(): array {
        $sql = "SELECT * FROM {$this->permissions_table} ORDER BY module ASC, name ASC";
        return $this->db->query_bind($sql, [], 'object');
    }

    public function get_permission(int $id): object|false {
        $sql = "SELECT * FROM {$this->permissions_table} WHERE id = :id LIMIT 1";
        $rows = $this->db->query_bind($sql, ['id' => $id], 'object');
        return $rows[0] ?? false;
    }

    public function create_permission(array $data): int {
        return $this->db->insert($data, $this->permissions_table);
    }

    public function update_permission(int $id, array $data): bool {
        return $this->db->update($id, $data, $this->permissions_table);
    }

    public function delete_permission(int $id): bool {
        $this->db->query_bind("DELETE FROM {$this->role_permissions_table} WHERE permission_id = :id", ['id' => $id]);
        $this->db->delete($id, $this->permissions_table);
        return true;
    }

    public function count_permissions(): int {
        $rows = $this->db->query_bind("SELECT COUNT(*) AS num FROM {$this->permissions_table}", [], 'object');
        return (int) ($rows[0]->num ?? 0);
    }

    public function get_permission_by_slug(string $slug): object|false {
        $sql = "SELECT * FROM {$this->permissions_table} WHERE slug = :slug LIMIT 1";
        $rows = $this->db->query_bind($sql, ['slug' => $slug], 'object');
        return $rows[0] ?? false;
    }

    public function get_permissions_grouped_by_module(): array {
        $all = $this->get_all_permissions();
        $grouped = [];
        foreach ($all as $perm) {
            $mod = $perm->module ?? '_other';
            $grouped[$mod][] = $perm;
        }
        return $grouped;
    }

    // ── Role ↔ Permission assignments ──────────────────────────────────

    public function get_role_permission_ids(int $role_id): array {
        $sql = "SELECT permission_id FROM {$this->role_permissions_table} WHERE role_id = :role_id";
        $rows = $this->db->query_bind($sql, ['role_id' => $role_id], 'object');
        return array_map(fn($r) => (int) $r->permission_id, $rows);
    }

    public function get_role_permissions(int $role_id): array {
        $sql = "SELECT p.* FROM {$this->permissions_table} p
                JOIN {$this->role_permissions_table} rp ON rp.permission_id = p.id
                WHERE rp.role_id = :role_id
                ORDER BY p.module ASC, p.name ASC";
        return $this->db->query_bind($sql, ['role_id' => $role_id], 'object');
    }

    public function set_role_permissions(int $role_id, array $permission_ids): void {
        $this->db->query_bind("DELETE FROM {$this->role_permissions_table} WHERE role_id = :role_id", ['role_id' => $role_id]);
        foreach ($permission_ids as $pid) {
            $pid = (int) $pid;
            if ($pid > 0) {
                $this->db->insert(['role_id' => $role_id, 'permission_id' => $pid], $this->role_permissions_table);
            }
        }
    }

    // ── Admin ↔ Role assignments ───────────────────────────────────────

    public function get_admin_role_ids(int $admin_id): array {
        $sql = "SELECT role_id FROM {$this->role_assignments_table} WHERE admin_id = :admin_id";
        $rows = $this->db->query_bind($sql, ['admin_id' => $admin_id], 'object');
        return array_map(fn($r) => (int) $r->role_id, $rows);
    }

    public function get_admin_roles(int $admin_id): array {
        $sql = "SELECT r.* FROM {$this->roles_table} r
                JOIN {$this->role_assignments_table} ra ON ra.role_id = r.id
                WHERE ra.admin_id = :admin_id
                ORDER BY r.name ASC";
        return $this->db->query_bind($sql, ['admin_id' => $admin_id], 'object');
    }

    public function set_admin_roles(int $admin_id, array $role_ids): void {
        $this->db->query_bind("DELETE FROM {$this->role_assignments_table} WHERE admin_id = :admin_id", ['admin_id' => $admin_id]);
        foreach ($role_ids as $rid) {
            $rid = (int) $rid;
            if ($rid > 0) {
                $this->db->insert(['admin_id' => $admin_id, 'role_id' => $rid], $this->role_assignments_table);
            }
        }
    }

    // ── Permission checking ────────────────────────────────────────────

    public function has_permission(int $admin_id, string $permission_slug): bool {
        $sql = "SELECT COUNT(*) AS num FROM {$this->role_assignments_table} ra
                JOIN {$this->role_permissions_table} rp ON rp.role_id = ra.role_id
                JOIN {$this->permissions_table} p ON p.id = rp.permission_id
                WHERE ra.admin_id = :admin_id AND p.slug = :slug";
        $rows = $this->db->query_bind($sql, ['admin_id' => $admin_id, 'slug' => $permission_slug], 'object');
        return ($rows[0]->num ?? 0) > 0;
    }

    public function get_admin_permissions(int $admin_id): array {
        $sql = "SELECT DISTINCT p.* FROM {$this->permissions_table} p
                JOIN {$this->role_permissions_table} rp ON rp.permission_id = p.id
                JOIN {$this->role_assignments_table} ra ON ra.role_id = rp.role_id
                WHERE ra.admin_id = :admin_id
                ORDER BY p.module ASC, p.name ASC";
        return $this->db->query_bind($sql, ['admin_id' => $admin_id], 'object');
    }

    public function admin_has_role(int $admin_id, string $role_slug): bool {
        $sql = "SELECT COUNT(*) AS num FROM {$this->role_assignments_table} ra
                JOIN {$this->roles_table} r ON r.id = ra.role_id
                WHERE ra.admin_id = :admin_id AND r.slug = :slug";
        $rows = $this->db->query_bind($sql, ['admin_id' => $admin_id, 'slug' => $role_slug], 'object');
        return ($rows[0]->num ?? 0) > 0;
    }

    // ── Admin helpers ──────────────────────────────────────────────────

    public function is_super_admin(int $admin_id): bool {
        $sql = "SELECT is_super_admin FROM trongate_administrators WHERE id = :id LIMIT 1";
        $rows = $this->db->query_bind($sql, ['id' => $admin_id], 'object');
        return !empty($rows) && (int) $rows[0]->is_super_admin === 1;
    }

    public function get_all_admins(): array {
        $sql = "SELECT id, username, email, active, is_super_admin FROM trongate_administrators ORDER BY username ASC";
        return $this->db->query_bind($sql, [], 'object');
    }
}
