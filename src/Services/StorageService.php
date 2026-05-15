<?php

namespace Leart\JsonDerulo\Services;

use PDO;

/**
 * StorageService — MySQL-backed persistence layer
 *
 * Replaces the old flat-file JSON storage.
 * Each "collection" maps to a MySQL table (study_sessions, research_sessions).
 * Uses the DatabaseService PDO singleton with prepared statements only.
 */
class StorageService
{
    private PDO $db;
    private string $table;

    /**
     * @param string $collection  Table name to operate on (e.g. 'study_sessions')
     */
    public function __construct(string $collection)
    {
        $this->db    = DatabaseService::getConnection();
        $this->table = $this->sanitizeTableName($collection);
    }

    /**
     * Save (insert or update) a record.
     *
     * @param string $id   Primary key value
     * @param array  $data Associative array with the data payload
     * @return bool
     */
    public function save(string $id, array $data): bool
    {
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
        $title    = $data['title'] ?? $data['query'] ?? $data['_type'] ?? null;
        $now      = date('Y-m-d H:i:s');

        $sql = "INSERT INTO `{$this->table}` (id, title, created_at, updated_at)
                VALUES (:id, :title, :now1, :now2)
                ON DUPLICATE KEY UPDATE
                    title = VALUES(title),
                    updated_at = VALUES(updated_at)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'    => $id,
            ':title' => $title,
            ':now1'  => $now,
            ':now2'  => $now,
        ]);
    }

    /**
     * Load a record by ID.
     *
     * @param string $id
     * @return array|null
     */
    public function load(string $id): ?array
    {
        $sql  = "SELECT * FROM `{$this->table}` WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * List all records, newest first.
     *
     * @param int $limit
     * @return array
     */
    public function listAll(int $limit = 100): array
    {
        $sql  = "SELECT * FROM `{$this->table}` ORDER BY updated_at DESC LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete a record by ID.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        $sql  = "DELETE FROM `{$this->table}` WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Sanitize the table name to prevent SQL injection.
     * Only allows alphanumerics and underscores.
     */
    private function sanitizeTableName(string $name): string
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $name);
    }
}
