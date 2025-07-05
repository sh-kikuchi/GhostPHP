<?php

/**
 * Class ExportCSV
 * Handles exporting data from a database table to a CSV file.
 */
class ExportCSV {

    /**
     * @var string The name of the table to export.
     */
    private $tableName;

    /**
     * @var string The file path where the CSV will be saved.
     */
    private $filePath;

    /**
     * Runs the export process by specifying the table name.
     *
     * @param string $tableName The name of the table to export.
     * @return void
     */
    function run($tableName) {
        $this->tableName = $tableName;
        $this->filePath = 'storage/csv/' . $this->tableName . '.csv';

        try {
            $fp = fopen($this->filePath, 'w');
            $dbConnect = new app\aura\database\DataBaseConnect();
            $pdo = $dbConnect->getPDO();

            $sql = 'SELECT * FROM ' . $this->tableName;
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            foreach ($stmt as $row) {
                $this->writeRowToCsv($fp, $row);
            }
            fclose($fp);  // Close the file after writing

        } catch (PDOException $e) {
            $this->handleError($e);
        }
    }

    /**
     * Writes a row of data to the CSV file.
     *
     * @param resource $fp The file pointer resource.
     * @param array $row The row data to write.
     * @return void
     */
    private function writeRowToCsv($fp, $row) {
        $rowTmp = '"' . implode('","', $row) . '"' . "\n";
        fwrite($fp, $rowTmp);
    }

    /**
     * Handles errors by printing the error message.
     *
     * @param PDOException $e The PDOException instance containing the error message.
     * @return void
     */
    private function handleError($e) {
        print "[ERROR] {{$e->getMessage()}}\n";
        die();
    }
}
