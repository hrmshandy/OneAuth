<?php

namespace Hilabs\OneAuth;

use Hilabs\OneAuth\OneAuthSDK;

class OneAuth
{

    private $auth;

    /**
     * Creates an instance of the OneAuth SDK using
     * the config set in the laravel way and using a LaravelSession
     * as a store mechanism
     */
    private function getSDK() {
        if (is_null($this->auth)) {
            $authConfig = config('one-auth');
            $this->auth = new OneAuthSDK($authConfig);
        }
        return $this->auth;
    }

    /**
     * Logs the user out from the SDK.
     */
    public function logout() {
        $this->getSDK()->logout();
    }

    /**
     * If the user is logged in, returns the user information
     *
     */
    public function getUser() {
        // Get the user info from oneAuth
        $auth = $this->getSDK();
        $user = $auth->getUser();
        if ($user === null) return null;
        return [
            'profile' => $user,
            'accessToken' => $auth->getAccessToken()
        ];
    }

    private $_onLoginCb = null;

    /**
     * Sets a callback to be called when the user is logged in
     * @param  callback $cb A function that receives an auth0User and receives a Laravel user
     */
    public function onLogin($cb) {
        $this->_onLoginCb = $cb;
    }

    public function hasOnLogin () {
        return $this->_onLoginCb !== null;
    }

    public function callOnLogin($authUser) {
        return call_user_func($this->_onLoginCb, $authUser);
    }

}
