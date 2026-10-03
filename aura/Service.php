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

    /**
     * @var bool Whether this instance started the current transaction.
     */
    private bool $ownsTransaction = false;

    /**
     * Service constructor.
     *
     * Initializes the repository used for transaction management.
     */
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
     * If a transaction is already active on the shared connection (e.g. started by an outer Service),
     * this instance joins it and leaves commit/rollBack to the owner.
     *
     * @return bool True if this instance started the transaction, false if it joined an existing one.
     */
    public function beginTransaction(): bool {
        $pdo = $this->repository->getPdo();

        if ($pdo->inTransaction()) {
            return false; // 外側のトランザクションに参加する
        }

        $pdo->beginTransaction();
        $this->ownsTransaction = true;
        return true;
    }

    /**
     * Commit the current transaction.
     *
     * Only the instance that started the transaction commits it.
     *
     * @return bool True if committed, false if this instance does not own the transaction.
     */
    public function commit(): bool {
        if (!$this->ownsTransaction) {
            return false; // 自分が開始したトランザクションではない
        }

        $this->repository->getPdo()->commit();
        $this->ownsTransaction = false;
        return true;
    }

    /**
     * Roll back the current transaction.
     *
     * Only the instance that started the transaction rolls it back.
     * A joined (inner) Service should throw instead, so the owner can roll back.
     *
     * @return bool True if rolled back, false if this instance does not own the transaction.
     */
    public function rollBack(): bool {
        if (!$this->ownsTransaction) {
            return false; // 自分が開始したトランザクションではない
        }

        $pdo = $this->repository->getPdo();
        // 既に終了している場合に二重ロールバックで落ちないようにする
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $this->ownsTransaction = false;
        return true;
    }

}