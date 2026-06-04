<?php
class Media_model extends Model {

    private string $table_name = 'media';
    private string $folder_table = 'media_folders';

    private function _find(string $table, string $column, $value): object|bool {
        $sql = "SELECT * FROM {$table} WHERE {$column} = :{$column} LIMIT 1";
        $params = [$column => $value];
        $rows = $this->db->query_bind($sql, $params, 'object');
        return !empty($rows) ? $rows[0] : false;
    }

    public function get_data_from_db(int $record_id): array|bool {
        $record = $this->_find($this->table_name, 'id', $record_id);
        return $record !== false ? (array) $record : false;
    }

    public function get_data_from_post(): array {
        return [
            'alt_text' => post('alt_text', true),
            'caption' => post('caption', true)
        ];
    }

    public function update(int $update_id, array $data): bool {
        return $this->db->update($update_id, $data, $this->table_name);
    }

    public function get_all_paginated(int $limit, int $offset, int $folder_id = 0): array {
        if ($folder_id > 0) {
            $sql = "SELECT * FROM {$this->table_name} WHERE folder_id = :folder_id ORDER BY id DESC LIMIT :limit OFFSET :offset";
            $params = ['folder_id' => $folder_id, 'limit' => $limit, 'offset' => $offset];
        } else {
            $sql = "SELECT * FROM {$this->table_name} WHERE folder_id = 0 ORDER BY id DESC LIMIT :limit OFFSET :offset";
            $params = ['limit' => $limit, 'offset' => $offset];
        }
        return $this->db->query_bind($sql, $params, 'object');
    }

    public function prepare_records_for_display(array $rows): array {
        $prepared = [];
        foreach ($rows as $row) {
            $item = (array) $row;
            $item['filesize_formatted'] = $this->_fmt_size((int) ($item['filesize'] ?? 0));
            $item['created_at_formatted'] = ($item['created_at'] > 0) ? date('Y-m-d H:i', (int) $item['created_at']) : '-';
            $item['media_type'] = $this->_get_media_type($item['extension'] ?? '');
            $prepared[] = (object) $item;
        }
        return $prepared;
    }

    public function prepare_for_display(array $data): array {
        $data['filesize_formatted'] = $this->_fmt_size((int) ($data['filesize'] ?? 0));
        $data['created_at_formatted'] = ($data['created_at'] > 0) ? date('Y-m-d H:i', (int) $data['created_at']) : '-';
        $data['media_type'] = $this->_get_media_type($data['extension'] ?? '');
        return $data;
    }

    private function _fmt_size(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function count_all(int $folder_id = 0): int {
        if ($folder_id > 0) {
            $sql = "SELECT COUNT(*) AS num FROM {$this->table_name} WHERE folder_id = :folder_id";
            $rows = $this->db->query_bind($sql, ['folder_id' => $folder_id], 'object');
        } else {
            $sql = "SELECT COUNT(*) AS num FROM {$this->table_name} WHERE folder_id = 0";
            $rows = $this->db->query_bind($sql, [], 'object');
        }
        return !empty($rows) ? (int) $rows[0]->num : 0;
    }

    public function insert(array $data): int {
        return $this->db->insert($data, $this->table_name);
    }

    public function delete_record(int $update_id): bool {
        $sql = 'SELECT filepath FROM ' . $this->table_name . ' WHERE id = :id';
        $rows = $this->db->query_bind($sql, ['id' => $update_id], 'object');
        if (empty($rows)) return false;
        $filepath = $rows[0]->filepath;
        $full_path = APPPATH . $filepath;
        if (!empty($filepath) && file_exists($full_path)) {
            unlink($full_path);
        }
        $this->db->delete($update_id, $this->table_name);
        return true;
    }

    public function move_to_folder(int $media_id, int $folder_id): bool {
        return $this->db->update($media_id, ['folder_id' => $folder_id], $this->table_name);
    }

    public function get_folders(): array {
        $sql = 'SELECT * FROM ' . $this->folder_table . ' ORDER BY name ASC';
        return $this->db->query_bind($sql, [], 'object');
    }

    public function get_folder(int $id): object|bool {
        return $this->_find($this->folder_table, 'id', $id);
    }

    public function create_folder(string $name, int $created_by): int {
        $data = [
            'name' => $name,
            'parent_id' => 0,
            'created_by' => $created_by,
            'created_at' => time()
        ];
        return $this->db->insert($data, $this->folder_table);
    }

    public function rename_folder(int $id, string $name): bool {
        return $this->db->update($id, ['name' => $name], $this->folder_table);
    }

    public function delete_folder(int $id): bool {
        $sql = "UPDATE {$this->table_name} SET folder_id = 0 WHERE folder_id = :folder_id";
        $this->db->query_bind($sql, ['folder_id' => $id]);
        $this->db->delete($id, $this->folder_table);
        return true;
    }

    private function _get_media_type(string $ext): string {
        $ext = strtolower($ext);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) return 'image';
        if (in_array($ext, ['mp4', 'webm', 'avi', 'mov', 'wmv', 'mkv', 'ogg'])) return 'video';
        if (in_array($ext, ['mp3', 'wav', 'aac', 'wma', 'flac', 'm4a'])) return 'audio';
        if (in_array($ext, ['pdf'])) return 'pdf';
        if (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) return 'archive';
        if (in_array($ext, ['doc', 'docx'])) return 'word';
        if (in_array($ext, ['xls', 'xlsx'])) return 'excel';
        return 'other';
    }

    public function count_files_in_folder(int $folder_id): int {
        $sql = "SELECT COUNT(*) AS num FROM {$this->table_name} WHERE folder_id = :folder_id";
        $rows = $this->db->query_bind($sql, ['folder_id' => $folder_id], 'object');
        return !empty($rows) ? (int) $rows[0]->num : 0;
    }
}
