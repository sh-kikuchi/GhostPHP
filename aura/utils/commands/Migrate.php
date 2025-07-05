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
    function run() {
        // Connect to the database
        require_once 'aura/database/DataBaseConnect.php';
        $dbConnect = new app\aura\database\DataBaseConnect();
        $pdo = $dbConnect->getPDO(); // Get the PDO instance

        // Scan files in the directory
        $files = scandir($this->directory);

        // Run migrations
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            require_once $this->directory . '/' . $file;
            
            try {
                // Extract the class name from the file name
                $table_class = explode(".", $file)[1];
                $table_instance = new $table_class();
                $sql = $table_instance->getSql();
                $pdo->query($sql);

            } catch (PDOException $e) {
                print($e->getMessage());
            }
        }
    }
}

?>
