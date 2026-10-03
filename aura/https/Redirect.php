<?php

namespace app\aura\https;

/**
 * Class Redirect
 *
 * Provides methods for handling HTTP redirects.
 */
class Redirect
{
    /**
     * Base directory that error templates are resolved against.
     * Set by the consuming application (e.g. in bootstrap.php) since
     * templates are application content, not part of this framework.
     */
    private static string $errorTemplatesBasePath = 'templates/errors';

    /**
     * Configure the base directory used to resolve error template paths.
     *
     * @param string $path Absolute path to the application's error templates directory.
     */
    public static function setErrorTemplatesBasePath(string $path): void {
        self::$errorTemplatesBasePath = rtrim($path, '/');
    }

    /**
     * Redirect to a specified URL.
     *
     * @param string $path The path to redirect to.
     * @param int $statusCode The HTTP status code for the redirect (optional, default is 302).
     * @return void
     */
    public static function to(string $path, int $statusCode = 302): void  {
        $url = dirname($_SERVER['SCRIPT_NAME']) . '/' . ltrim($path, '/');
        header("Location: {$url}", true, $statusCode);
    }

    /**
     * Redirect to the error page.
     *
     * @param int $statusCode The HTTP status code for the error (optional, default is 404).
     * @return void
     */
    public static function error(int $statusCode = 404)
    {
        http_response_code($statusCode);
        if ($statusCode == 500) {
            include(self::$errorTemplatesBasePath . '/500.php');
        } else {
            include(self::$errorTemplatesBasePath . '/404.php');
        }
        exit();
    }
}
