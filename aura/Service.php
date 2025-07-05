<?php

namespace app\aura;

use app\aura\Template;
use app\aura\Repository;
use app\aura\utils\Session;

/**
 * Class Service
 *
 * Provides various utility methods for managing sessions, CSRF tokens, and rendering templates.
 */
class Service {
    /**
     * @var Repository The repository instance.
     */
    private Repository $repository;

    private bool $inTransaction = false;

    /**
     * Service constructor.
     *
     * 
     * */
    public function __construct() {
        $this->repository = new Repository(); 
        $session  = new Session();
    }

    /**
     * Generate a CSRF token for a specified form.
     *
     * @param string $form_name The name of the form for which the token is being generated.
     * @return string The generated CSRF token.
     */
    function setToken(string $form_name): string {
        $csrf_token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'][$form_name] = $csrf_token;

        return $csrf_token;
    }

    /**
     * Check if the provided CSRF token is valid for a specified form.
     *
     * @param string $form_name The name of the form to check the token against.
     * @return bool True if the token is valid, false otherwise.
     */
    function checkToken(string $form_name): bool {
        if (!isset($_SESSION['csrf_token'][$form_name])) {
            return false;
        }

        return $_POST["csrf_token"] === $_SESSION['csrf_token'][$form_name];
    }

    /**
     * Render a template file.
     *
     * @return mixed The rendered template output.
     */
    function render(): mixed {
        $template = new Template;

        return $template->render();
    }
    
    /**
     * Begin a transaction on the repository.
     *
     * Starts a transaction to ensure that multiple database operations are handled atomically.
     *
     * @return bool True if the transaction was started successfully, false otherwise.
     */
    public function beginTransaction(): bool {
        if ($this->inTransaction) {
            return false; // 既にトランザクション中
        }

        // RepositoryからPDOを取得してトランザクションを開始
        $this->repository->getPdo()->beginTransaction();
        $this->inTransaction = true;
        return true;
    }

    /**
     * Commit the current transaction.
     *
     * If all database operations are successful, commit the transaction to make changes permanent.
     *
     * @return bool True if the transaction was committed successfully, false otherwise.
     */
    public function commit(): bool {
        if (!$this->inTransaction) {
            return false; // トランザクションが開始されていない
        }

        // RepositoryからPDOを取得してコミット
        $this->repository->getPdo()->commit();
        $this->inTransaction = false;
        return true;
    }

    /**
     * Roll back the current transaction.
     *
     * If any database operation fails, roll back the transaction to maintain database consistency.
     *
     * @return bool True if the transaction was rolled back successfully, false otherwise.
     */
    public function rollBack(): bool {
        if (!$this->inTransaction) {
            return false; // トランザクションが開始されていない
        }

        // RepositoryからPDOを取得してロールバック
        $this->repository->getPdo()->rollBack();
        $this->inTransaction = false;
        return true;
    }

}
