<?php

namespace app\aura\https;

/**
 * Class Redirect
 *
 * Provides methods for handling HTTP redirects.
 */
class Redirect {
    /**
     * Redirect to a specified URL.
     *
     * @param string $path The path to redirect to.
     * @param int $statusCode The HTTP status code for the redirect (optional, default is 302).
     * @return void
     */
    public static function to($path, $statusCode = 302) {
        $url = dirname($_SERVER['SCRIPT_NAME']) . '/' . ltrim($path, '/');
        header("Location: " . $url, true, $statusCode);
        exit();
    }

    /**
     * Redirect to the error page.
     *
     * @param int $statusCode The HTTP status code for the error (optional, default is 404).
     * @return void
     */
    public static function error($statusCode = 404) {
        http_response_code($statusCode);
        if ($statusCode == 500) {
            include('templates/errors/500.php');
        } else {
            include('templates/errors/404.php');
        }
        exit();
    }
}
