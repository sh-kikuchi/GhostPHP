<?php

namespace app\aura\utils;

class File {
    /**
     * Handles file upload processing.
     *
     * @since 1.1.0
     * @updated 2026-02-11
     * Changes:
     * - Added support for multiple file uploads.
     * - Refactored internal processing logic.
     *
     * @param array $file_data The $_FILES array.
     * @return array Upload result data.
     */
    public function uploadFile(array $file_data): array {
        $results = [];
        foreach ($file_data as $inputName => $data) {
            if (\is_array($data['name'])) {
                foreach ($data['name'] as $i => $name) {
                    $results[] = $this->processFile([
                        'name'     => $data['name'][$i],
                        'tmp_name' => $data['tmp_name'][$i],
                        'error'    => $data['error'][$i],
                    ]);
                }
            } else {
                $results[] = $this->processFile($data);
            }
        }
        return $results;
    }

    /**
     * Processes a single uploaded file.
     *
     * Validates the upload, checks the file extension,
     * saves the file to the storage directory,
     * and returns the upload result.
     *
     * @param array $file Uploaded file information.
     * @return array Upload result containing success status and file path or error message.
     */
    private function processFile(array $file): array {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Upload error'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = explode(',', $_ENV['ALLOWED_EXTENSIONS']);

        if (!\in_array($extension, $allowed_extensions)) {
            return ['success' => false, 'message' => 'Invalid file type'];
        }

        $dest = 'storage/' . uniqid('', true) . '.' . $extension;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'message' => 'Failed to save file'];
        }

        return ['success' => true, 'path' => $dest];
    }
}