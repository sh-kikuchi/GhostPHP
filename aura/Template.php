<?php

namespace app\aura;

/**
 * Class Template
 *
 * Manages the rendering of template files with associated data.
 */
class Template {

    protected string $path;
    protected array $data;

    /**
     * Base directory that template files are resolved against.
     * Set by the consuming application (e.g. in bootstrap.php) since
     * templates are application content, not part of this framework.
     */
    private static string $basePath = 'templates';

    /**
     * Configure the base directory used to resolve template paths.
     *
     * @param string $path Absolute path to the application's templates directory.
     */
    public static function setBasePath(string $path): void {
        self::$basePath = rtrim($path, '/');
    }

    /**
     * Template constructor.
     *
     * @param string $path The path to the template file.
     * @param array $data The data to be passed to the template.
     */
    public function __construct(string $path, array $data) {
        $this->path  = $path;
        $this->data = !empty($data) ? $data : [];
    }

    /**
     * Render the template.
     *
     * Includes the template file and extracts the data for use within the template.
     *
     * @return void
     */
    public function render() {
        if (\count($this->data) > 0) {
            foreach ($this->data as $key => $value) {
                ${$key} = $value;
            }
        }

        include self::$basePath . '/' . $this->path . '.php';
    }
}