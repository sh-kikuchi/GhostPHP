<?php

namespace app\aura;

use PDO;
use app\aura\database\DataBaseConnect;

/**
 * Class Repository
 * 
 * Provides generic CRUD operations for database entities.
 */
class Repository {
    /**
     * @var PDO The PDO instance for database interaction.
     */
    protected PDO $pdo;
    
    /**
     * @var string The name of the database table.
     */
    protected string $table;
    

    /**
     * Repository constructor.
     * 
     * Initializes the PDO instance via DataBaseConnect and optionally sets the table name.
     * 
     * @param string $table The name of the table (optional).
     */
    public function __construct(string $table = '') {
        // PDOをDataBaseConnectから取得
        $this->pdo = (new DataBaseConnect())->getPDO();
        
        if (!empty($table)) {
            $this->setTable($table);
        }
    }

    /**
     * Sets the table name safely using a whitelist approach.
     *
     * @param string $table The table name.
     * @throws \InvalidArgumentException If the table name contains invalid characters.
     */
    protected function setTable(string $table): void {
        // Allow only alphanumeric table names to prevent SQL injection
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new \InvalidArgumentException("Invalid table name.");
        }
        $this->table = $table;
    }

    /**
     * Find a record by ID.
     * 
     * @param int $id The ID of the record.
     * @return array|null The record data or null if not found.
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Retrieve all records from the table.
     * 
     * @return array List of all records.
     */
    public function findAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}`");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new record in the database.
     * 
     * @param array $data The data to insert (associative array).
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool {
        $columns = implode(", ", array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    /**
     * Update a record in the database.
     * 
     * @param int $id The ID of the record to update.
     * @param array $data The data to update (associative array).
     * @return bool True on success, false on failure.
     */
    public function update(int $id, array $data): bool {
        $setClause = implode(", ", array_map(fn($key) => "`$key` = ?", array_keys($data)));
        $sql = "UPDATE `{$this->table}` SET {$setClause} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Delete a record from the database.
     * 
     * @param int $id The ID of the record to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM `{$this->table}` WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Find a record by arbitrary column.
     * 
     * @param string $column The column name.
     * @param mixed $value The value to match.
     * @return array|null The record data or null if not found.
    */
    public function findByColumn(string $column, $value): ?array {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name.");
        }
        $stmt = $this->pdo->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get the PDO instance.
     *
     * @return PDO
     */
    public function getPdo(): PDO {
        return $this->pdo;
    }
}
