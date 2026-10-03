<?php
namespace app\aura\utils;

/**
 * Class Session
 *
 * Starts the PHP session and manages its expiry, ID regeneration,
 * old input values and CSRF tokens.
 */
class Session {
    /**
     * @var bool Whether the session has been started.
     */
    protected static $sessionStarted = false;

    /**
     * @var bool Whether the session ID has been regenerated.
     */
    protected static $sessionIdRegenerated = false;

    /**
     * Session constructor.
     *
     * Starts the session if needed, sets the expiry time,
     * regenerates the session ID and clears the session when expired.
     */
    public function __construct(){
        // check session status
        $this->isSessionStarted();

        //set expiry time
        $this->setSessionExpiry();

        //generate session id
        $this->regenerateSessionId();

        //check session status
        if ($this->isSessionExpired()) {
          $this->clear();
        }
    }

    /**
     * Clears all session data.
     *
     * @return void
     */
    public function clear(){
      $_SESSION = [];
    }

    /**
     * Check Session Status.
     */
    private function isSessionStarted() {
      if(session_status() === PHP_SESSION_NONE){
        session_start();
      }
    }

    /**
     * Regenerates the session ID.
     */
    public function regenerateId() {
      session_regenerate_id(true);
    }

    /**
     * When returning to the screen on a validation error, 
     * the value that was entered is also returned.
     * @param array $oldPostValue 
     * @return void
     */
    public function oldPostValue(array $oldPostValue){
      foreach($oldPostValue as $key => $value){
          $_SESSION['old'][$key] = $value;
      }
    }

    /**
     * Generates a CSRF token and stores it in the session.
     *
     * @return string The generated CSRF token.
     */
    public function setToken() :string {
      $csrf_token = bin2hex(random_bytes(32));
      $_SESSION['csrf_token'] = $csrf_token;
  
      return $csrf_token;
    }

    /**
     * Sets the session expiration time.
     */
    private function setSessionExpiry() {
      // False if the session has not started.
      if (!isset($_SESSION)) {
          return false;
      }

      //  Sets the session expiration time(ex. 1Hour)
      $_SESSION['expiry_time'] = time() + 3600;
    }

    /**
     * Checks if the session has expired.
     *
     * @return bool 
     */
    private function isSessionExpired() {
        //False if session has not started, true otherwise.
        if (!isset($_SESSION)) {
          return false;
        }

        //Check expiration of session.
        $expiryTime = isset($_SESSION['expiry_time']) ? $_SESSION['expiry_time'] : 0;
        $currentTime = time(); //get current timestamp

        //True if the session has expired, false otherwise.
        return $expiryTime !== 0 && $currentTime > $expiryTime;
    }

    /**
     * Regenerates the session ID and refreshes the expiry time
     * when the session has expired.
     * @return void
     */
    private function regenerateSessionId(){
      if ($this->isSessionExpired()){
        $this->regenerateId();
        $this->setSessionExpiry(); // update the session's expiration time if the one is  regenerated
      }
    }
}