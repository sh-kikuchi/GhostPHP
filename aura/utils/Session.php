<?php
namespace app\aura\utils;

class Session{
    protected static $sessionStarted = false;
    protected static $sessionIdRegenerated = false;

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
    public function clear(){
      $_SESSION = array();
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
    function oldPostValue($oldPostValue){
      foreach($oldPostValue as $key => $value){
          $_SESSION['old'][$key] = $value;
      }
    }

    
    function setToken() :string {
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
     * When returning to the screen on a validation error, 
     * the value that was entered is also returned.
     * @param  void 
     * @return void
     */
    private function regenerateSessionId(){
      if ($this->isSessionExpired()){
        $this->regenerateId();
        $this->setSessionExpiry(); // update the session's expiration time if the one is  regenerated
      }
    }
}