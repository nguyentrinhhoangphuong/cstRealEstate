<?php
class Posts_model extends Model {
    protected string $table = 'posts';
    protected string $order_by = 'created_at DESC';
    protected string $primary_key = 'id';

    public function get_all(?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        return $this->db->query($sql, 'object');
    }

    public function count_all(): int {
        $sql = "SELECT COUNT(*) as total FROM posts";
        $result = $this->db->query($sql, 'object');
        return (int) ($result[0]->total ?? 0);
    }

    public function get_by_id(int $id): object|false {
        $result = $this->db->get_where($id, 'posts');
        return $result;
    }

    public function create(array $data): int {
        return $this->db->insert($data, 'posts');
    }

    public function update_record(int $id, array $data): bool {
        return $this->db->update($id, $data, 'posts');
    }

    public function delete_record(int $id): bool {
        return $this->db->delete($id, 'posts');
    }

    public function get_types(): array {
        $sql = "SELECT * FROM post_types ORDER BY sort_order ASC, name ASC";
        return $this->db->query($sql, 'object');
    }

    public function get_type_counts(): array {
        $sql = "SELECT p.type, COUNT(*) as total, t.name as type_name 
                FROM posts p 
                LEFT JOIN post_types t ON p.type = t.slug 
                GROUP BY p.type ORDER BY t.sort_order ASC";
        return $this->db->query($sql, 'object');
    }

    public function get_by_type(string $type, ?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.type = :type 
                ORDER BY p.created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        return $this->db->query_bind($sql, ['type' => $type], 'object');
    }

    public function count_by_type(string $type): int {
        $sql = "SELECT COUNT(*) as total FROM posts WHERE type = :type";
        $result = $this->db->query_bind($sql, ['type' => $type], 'object');
        return (int) ($result[0]->total ?? 0);
    }

    public function get_by_type_and_status(string $type, string $status, ?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.type = :type AND p.status = :status 
                ORDER BY p.created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        return $this->db->query_bind($sql, ['type' => $type, 'status' => $status], 'object');
    }

    public function count_by_type_and_status(string $type, string $status): int {
        $sql = "SELECT COUNT(*) as total FROM posts WHERE type = :type AND status = :status";
        $result = $this->db->query_bind($sql, ['type' => $type, 'status' => $status], 'object');
        return (int) ($result[0]->total ?? 0);
    }
    
    public function get_by_status(string $status, ?int $limit = null, ?int $offset = null): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = :status 
                ORDER BY p.created_at DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
            if ($offset !== null) {
                $sql .= " OFFSET " . (int) $offset;
            }
        }
        return $this->db->query_bind($sql, ['status' => $status], 'object');
    }

    public function count_by_status(string $status): int {
        $sql = "SELECT COUNT(*) as total FROM posts WHERE status = :status";
        $result = $this->db->query_bind($sql, ['status' => $status], 'object');
        return (int) ($result[0]->total ?? 0);
    }
}