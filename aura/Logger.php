<?php

namespace app\aura;

use DateTime;
use Exception;

/**
 * Logger class
 * 
 * Provides logging functionality with different log levels.
 */
class Logger {

    // Log levels
    const LOG_LEVEL_ERROR = 0;
    const LOG_LEVEL_WARN  = 1;
    const LOG_LEVEL_INFO  = 2;
    const LOG_LEVEL_DEBUG = 3;

    /** @var string Log file directory */
    private string $logDirectory;

    /** @var string Log file name */
    private string $logFile;

    /**
     * Constructor
     * 
     * @param string|null $fileName Custom log file name (optional).
     */
    public function __construct(?string $fileName = null) {
        // Set log directory inside project root
        $this->logDirectory = dirname(__DIR__) . '/logs';

        // Create log directory if not exists
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0777, true);
        }

        // Set log file name (default or custom)
        $this->setLogFile($fileName ?? 'log_' . date('Y-m-d') . '.log');
    }

    /**
     * Set log file name dynamically.
     *
     * @param string $fileName Custom log file name.
     */
    public function setLogFile(string $fileName): void {
        // Sanitize file name (allow only alphanumeric, underscores, hyphens, and .log extension)
        if (!preg_match('/^[a-zA-Z0-9_-]+\.log$/', $fileName)) {
            throw new Exception("Invalid log file name.");
        }
        $this->logFile = $this->logDirectory . '/' . $fileName;
    }

    /**
     * Log an ERROR message.
     *
     * @param string $msg Message to log.
     */
    public function error(string $msg): void {
        $this->out('ERROR', $msg);
    }

    /**
     * Log a WARN message.
     *
     * @param string $msg Message to log.
     */
    public function warn(string $msg): void {
        $this->out('WARN', $msg);
    }

    /**
     * Log an INFO message.
     *
     * @param string $msg Message to log.
     */
    public function info(string $msg): void {
        $this->out('INFO', $msg);
    }

    /**
     * Log a DEBUG message.
     *
     * @param string $msg Message to log.
     */
    public function debug(string $msg): void {
        $this->out('DEBUG', $msg);
    }

    /**
     * Write a log entry to the file.
     *
     * @param string $level Log level.
     * @param string $msg Log message.
     */
    private function out(string $level, string $msg): void {
        $time = $this->getTime();
        $logEntry = "[$time] [$level] $msg" . PHP_EOL;

        try {
            file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            error_log("Logger Error: " . $e->getMessage());
        }
    }

    /**
     * Get the current timestamp in `Y-m-d H:i:s.ms` format.
     *
     * @return string Current timestamp.
     */
    private function getTime(): string {
        $miTime = explode('.', microtime(true));
        $msec = str_pad(substr($miTime[1], 0, 3), 3, "0");
        return date('Y-m-d H:i:s', $miTime[0]) . '.' . $msec;
    }
}
