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
     * @param PDO|null $pdo An existing PDO instance to use (optional, e.g. for tests).
     */
    public function __construct(string $table = '', ?PDO $pdo = null){
        // PDOをDataBaseConnectから取得
          $this->pdo = $pdo ?? (new DataBaseConnect())->getPDO();
        
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
     * Validates column names using a whitelist approach.
     *
     * @param array $columns The column names to validate.
     * @throws \InvalidArgumentException If a column name contains invalid characters.
     */
    private function assertValidColumns(array $columns): void {
        foreach ($columns as $column) {
            if (!preg_match('/^[a-zA-Z0-9_]+$/', (string)$column)) {
                throw new \InvalidArgumentException("Invalid column name.");
            }
        }
    }

    /**
     * Find a record by ID.
     * 
     * @param mixed $id The ID of the record.
     * @return array|null The record data or null if not found.
     */
    public function findById(mixed $id): ?array {
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
     * @throws \InvalidArgumentException If a column name contains invalid characters.
     */
    public function create(array $data): bool {
        $this->assertValidColumns(array_keys($data));
        $columns = implode(", ", array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    /**
     * Update a record in the database.
     * 
     * @param mixed $id The ID of the record to update.
     * @param array $data The data to update (associative array).
     * @return bool True on success, false on failure.
     * @throws \InvalidArgumentException If a column name contains invalid characters.
     */
    public function update(mixed $id, array $data): bool {
        $this->assertValidColumns(array_keys($data));
        $setClause = implode(", ", array_map(fn($key) => "`$key` = ?", array_keys($data)));
        $sql = "UPDATE `{$this->table}` SET {$setClause} WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Executes an SQL query and retrieves the results.
     *
     * @param mixed $sql SQL statement to execute
     * @param array $params Parameters to bind to the SQL statement
     * @param bool $fetchAll Whether to fetch all records or a single record
     *
     * @return array|null An array of records when fetching all, or a single record or null
     */
    public function query(mixed $sql, array $params = [], bool $fetchAll = true): array|null {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : ($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
    }

    /**
     * Delete a record from the database.
     * 
     * @param mixed $id The ID of the record to delete.
     * @return bool True on success, false on failure.
     */
    public function delete(mixed $id): bool {
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
     * @throws \InvalidArgumentException If the column name contains invalid characters.
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
     * Executes a write SQL statement (INSERT / UPDATE / DELETE, etc.).
     *
     * Database errors are not caught here; error handling and transactions are the caller's (Service's) responsibility.
     *
     * @param string $sql SQL statement to execute
     * @param array $params Parameters to bind to the SQL statement
     *
     * @return bool True on success, false on failure.
     * @throws \PDOException If the statement fails.
     */
    public function executeAction(string $sql, array $params = []): bool {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Insert multiple records with a single INSERT statement.
     *
     * Column names are taken from the keys of the first row; every row must have the same keys.
     * Values are bound as placeholders. A single INSERT is atomic, so no transaction is started here;
     * wrap it in a Service transaction when combining it with other operations.
     *
     * @param array $rows List of records to insert (array of associative arrays).
     * @return bool True on success, false if $rows is empty.
     * @throws \InvalidArgumentException If a column name is invalid or the rows have mismatched keys.
     * @throws \PDOException If the statement fails.
     */
    public function bulkInsert(array $rows): bool {
        if (empty($rows)) {
            return false;
        }

        $rows = array_values($rows);
        $keys = array_keys($rows[0]);
        $this->assertValidColumns($keys);

        $columns = implode(", ", array_map(fn($col) => "`$col`", $keys));
        $rowPlaceholder = "(" . implode(", ", array_fill(0, count($keys), "?")) . ")";
        $params = [];
        foreach ($rows as $row) {
            if (array_keys($row) !== $keys) {
                throw new \InvalidArgumentException("All rows must have the same columns in the same order.");
            }
            array_push($params, ...array_values($row));
        }
        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES "
            . implode(", ", array_fill(0, count($rows), $rowPlaceholder));

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
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
