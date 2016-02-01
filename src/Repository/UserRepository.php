<?php

namespace Hilabs\OneAuth\Repository;

use Hilabs\OneAuth\AuthUser;
use Hilabs\OneAuth\Contract\UserRepository;
use Illuminate\Support\Facades\App;

class UserRepository implements UserRepository {

    public function getUserByUserInfo($userInfo) {
        return new AuthUser($userInfo['profile'], $userInfo['accessToken']);
    }

    public function getUserByIdentifier($identifier) {
        //Get the user info of the user logged in (probably in session)
        $user = App::make('one-auth')->getUser();
        if ($user===null) return null;
        // build the user
        $authUser = $this->getUserByUserInfo($user);
        // it is not the same user as logged in, it is not valid
        if ($authUser && $authUser->getAuthIdentifier() == $identifier) {
            return $authUser;
        }
    }

    /**
     * @param $identifier the user credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByCredentials($credentials) {

    }
}
