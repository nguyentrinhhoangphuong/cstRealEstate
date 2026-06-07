<?php
class Posts_model extends Model {
    protected string $table = 'posts';
    protected string $order_by = 'created_at DESC';
    protected string $primary_key = 'id';

    public function get_all(): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        return $this->db->query($sql, 'object');
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

    public function get_type_counts(): array {
        $sql = "SELECT `type`, COUNT(*) as `total` FROM `posts` GROUP BY `type` ORDER BY `type`";
        return $this->db->query($sql, 'object');
    }

    public function get_by_type(string $type): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.type = :type 
                ORDER BY p.created_at DESC";
        return $this->db->query_bind($sql, ['type' => $type], 'object');
    }

    public function get_by_type_and_status(string $type, string $status): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.type = :type AND p.status = :status 
                ORDER BY p.created_at DESC";
        return $this->db->query_bind($sql, ['type' => $type, 'status' => $status], 'object');
    }
    
    public function get_by_status(string $status): array {
        $sql = "SELECT p.*, c.title as category_title 
                FROM posts p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = :status 
                ORDER BY p.created_at DESC";
        return $this->db->query_bind($sql, ['status' => $status], 'object');
    }

}