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
    }

    /**
     * Generate a CSRF token for a specified form.
     * @deprecated Moved to Controller::setToken(). This method will be removed by the end of 2026.
     * @param string $form_name The name of the form for which the token is being generated.
     * @return string The generated CSRF token.
     */
    public function setToken(string $form_name): string {
        $csrf_token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'][$form_name] = $csrf_token;

        return $csrf_token;
    }

    /**
     * Check if the provided CSRF token is valid for a specified form.
     * @deprecated Moved to Controller::checkToken(). This method will be removed by the end of 2026.
     * @param string $form_name The name of the form to check the token against.
     * @return bool True if the token is valid, false otherwise.
     */
    public function checkToken(string $form_name): bool {
        if (!isset($_SESSION['csrf_token'][$form_name])) {
            return false;
        }

        return $_POST["csrf_token"] === $_SESSION['csrf_token'][$form_name];
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
