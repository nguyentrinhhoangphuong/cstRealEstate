<?php
class Post_types_model extends Model {

    public function get_types(): array {
        $sql = "SELECT * FROM post_types ORDER BY sort_order ASC, name ASC";
        return $this->db->query($sql, 'object');
    }

    public function get_type_by_id(int $id): object|false {
        return $this->db->get_where($id, 'post_types');
    }

    public function create_type(array $data): int {
        return $this->db->insert($data, 'post_types');
    }

    public function update_type(int $id, array $data): bool {
        return $this->db->update($id, $data, 'post_types');
    }

    public function delete_type(int $id): bool {
        return $this->db->delete($id, 'post_types');
    }

    public function count_posts_by_type(string $slug): int {
        $sql = "SELECT COUNT(*) as total FROM posts WHERE type = :slug";
        $rows = $this->db->query_bind($sql, ['slug' => $slug], 'object');
        return (int) ($rows[0]->total ?? 0);
    }

    public function type_slug_exists(string $slug, ?int $exclude_id = null): bool {
        $sql = "SELECT id FROM post_types WHERE slug = :slug";
        $rows = $this->db->query_bind($sql, ['slug' => $slug], 'object');
        if (empty($rows)) return false;
        if ($exclude_id !== null && (int)$rows[0]->id === $exclude_id) return false;
        return true;
    }

    public function get_next_sort_order(): int {
        $sql = "SELECT MAX(sort_order) as max_order FROM post_types";
        $rows = $this->db->query($sql, 'object');
        return ((int)($rows[0]->max_order ?? 0)) + 1;
    }

    public function update_posts_type_slug(string $old_slug, string $new_slug): void {
        $sql = "UPDATE posts SET type = :new_slug WHERE type = :old_slug";
        $this->db->query_bind($sql, ['new_slug' => $new_slug, 'old_slug' => $old_slug]);
    }

    public function update_sort_order(array $ids): void {
        foreach ($ids as $index => $id) {
            $sort_order = $index + 1;
            $sql = "UPDATE post_types SET sort_order = :sort_order WHERE id = :id";
            $this->db->query_bind($sql, ['sort_order' => $sort_order, 'id' => (int)$id]);
        }
    }

}
