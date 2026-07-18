<?php

/**
 * Class Migrate
 * Handles running database migrations from files located in a specified directory.
 */
class Migrate {
    /**
     * @var string The directory where migration files are located.
     */
    protected $directory = 'migrations/migrate';

    /**
     * Executes all migrations found in the specified directory.
     *
     * Connects to the database, scans the migration files directory, and runs each migration file.
     * Assumes each file contains a class that extends the migration and has a `getSql()` method to retrieve SQL statements.
     *
     * @return void
     * @throws PDOException If there is an error executing the SQL statements.
     */
    public function run() {
        require_once 'aura/database/DataBaseConnect.php';
        $dbConnect = new app\aura\database\DataBaseConnect();
        $pdo = $dbConnect->getPDO();

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $files = scandir($this->directory);
        sort($files, SORT_STRING);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;

            require_once $this->directory . '/' . $file;

            $table_class = explode(".", $file)[1];

            echo "== Running: {$file} ==\n";

            $table_instance = new $table_class();
            $sql = $table_instance->getSql();

            $queries = is_array($sql) ? $sql : [$sql];

            foreach ($queries as $q) {
                try {
                    echo "SQL: {$q}\n";
                    $pdo->exec($q);
                } catch (PDOException $e) {

                    if (str_contains($e->getMessage(), 'Duplicate key name')) {
                        echo "[SKIP] Index already exists\n";
                        continue;
                    }

                    echo "[FAILED] {$file}\n";
                    echo "Message: " . $e->getMessage() . "\n";
                    echo "SQL: {$q}\n";
                    echo "Trace:\n" . $e->getTraceAsString() . "\n";

                    exit(1);
                }
            }

            echo "[OK] {$file}\n\n";
        }
    }
};
?>
