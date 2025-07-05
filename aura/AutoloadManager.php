<?php

namespace app\aura;

/**
 * Class AutoloadManager
 *
 * Manages the automatic loading of classes from specified directories.
 */
class AutoloadManager {
    protected array $dirs;

    /**
     * Register a directory for autoloading.
     *
     * @param string $target_dir The directory to register.
     * @return void
     */
    public function registerDir(string $target_dir) {
        $this->dirs[] = $target_dir;
    }

    /**
     * Automatically load classes from registered directories.
     *
     * Scans each registered directory and includes all files found within.
     *
     * @return void
     */
    public function autoload() {
        foreach ($this->dirs as $dir) {
            // Scan files in the directory
            $files = scandir($dir);

            if ($files === false) {
                echo "Failed to open directory: " . $dir;
                die();
            } 

            // Auto load
            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file == '.' || $file == '..') continue;
                    require_once $dir . '/' . $file;
                }
            } 
        }
    }
}
