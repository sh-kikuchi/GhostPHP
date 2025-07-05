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
        if (count($this->data) > 0) {
            foreach ($this->data as $key => $value) {
                ${$key} = $value;
            }
        }

        include "templates/" . $this->path . '.php';
    }
}
