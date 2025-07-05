<?php
namespace app\aura;

use app\aura\https\Redirect;
use app\aura\https\Request;
use app\aura\https\Response;
use app\aura\utils\Validator;

/**
 * Class Controller
 *
 * Provides common functionality for controllers, including handling of
 * CSRF tokens, request and response management, and validation.
 */
abstract class Controller {

    /** @var Request */
    protected Request $request;

    /** @var Response */
    protected Response $response;

    /** @var Redirect */
    protected Redirect $redirect;

    /** @var Validator */
    protected Validator $validator;

    /**
     * Controller constructor.
     *
     * Initializes the request, response, redirect, and validator instances.
     */
    public function __construct() {
        $this->request   = new Request();
        $this->response  = new Response();
        $this->redirect  = new Redirect();
        $this->validator = new Validator();
    }

    /**
     * Generates a CSRF token and stores it in the session.
     *
     * @return string The generated CSRF token.
     */
    public function generateCsrfToken(): string {
       // Converts the binary data into a hexadecimal string.
        $csrf_token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrf_token;

        return $csrf_token;
    }

    /**
     * Validates the provided CSRF token against the one stored in the session.
     *
     * @param string $token The CSRF token submitted in the form.
     * @return bool True if the tokens match, false otherwise.
     */
    public function validateCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
