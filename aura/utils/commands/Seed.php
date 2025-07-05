<?php

/**
 * Class Seed
 * Handles running database seeders from files located in a specified directory.
 */
class Seed {
    /**
     * @var string The directory where seeder files are located.
     */
    protected $directory = 'migrations/seed';

    /**
     * Executes all seeders found in the specified directory.
     *
     * Scans the seeder files directory and runs each seeder file.
     * Assumes each file contains a class with a `seed()` method that performs the seeding.
     *
     * @return void
     * @throws PDOException If there is an error executing the seeder methods.
     */
    function run() {
        // Scan files in the directory
        $files = scandir($this->directory);

        // Run seeders
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            require_once $this->directory . '/' . $file;
            
            try {
                // Extract the class name from the file name
                $table_class = explode(".", $file)[1];
                $table_instance = new $table_class();
                $table_instance->seed();

            } catch (PDOException $e) {
                print($e->getMessage());
            }
        }
    }
}

?>
