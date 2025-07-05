<?php

/**
 * Class ImportCSV
 * Handles importing data from a CSV file into a database table.
 */
class ImportCSV {

    /**
     * @var string Comma-separated list of column names for the table.
     */
    private $table_cols;

    /**
     * @var string The name of the table to import data into.
     */
    private $table_name;

    /**
     * @var string The file path of the CSV file to import.
     */
    private $filePath;

    /**
     * Runs the import process by specifying the table name.
     *
     * @param string $table_name The name of the table to import data into.
     * @return void
     * @throws Exception If the CSV file is not found, not readable, or if reading the header fails.
     * @throws PDOException If there is an error executing the SQL statement.
     */
    public function run($table_name) {
        $this->table_name = $table_name;
        $this->filePath = 'storage/csv/' . $this->table_name . '.csv';

        try {
            $dbConnect = new app\aura\database\DataBaseConnect();
            $pdo = $dbConnect->getPDO();

            if (!file_exists($this->filePath) || !is_readable($this->filePath)) {
                throw new Exception("CSV file not found or not readable: $this->filePath");
            }

            $f = fopen($this->filePath, "r");

            // Read the first row to get column names
            $header = fgetcsv($f);
            if (!$header) {
                throw new Exception("Failed to read the header from the CSV file.");
            }
            $this->table_cols = implode(', ', $header);

            while ($data = fgetcsv($f)) {
                $col_arr = [];
                if (implode($data) != null) {
                    foreach ($data as $col) {
                        array_push($col_arr, "'" . $col . "'");
                    }
                    $col_str = implode(',', $col_arr);
                    $sql = "INSERT INTO {$this->table_name} ({$this->table_cols}) VALUES ({$col_str});";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                }
            }
            fclose($f);
        } catch (PDOException $e) {
            print "[ERROR] {$e->getMessage()}\n";
            die();
        } catch (Exception $e) {
            print "[ERROR] {$e->getMessage()}\n";
            die();
        }
    }
}
