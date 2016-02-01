<?php

namespace Hilabs\OneAuth\Contract;

interface UserRepository {

    /**
     * @param array $userInfo representing the user profile and user accessToken
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByUserInfo($userInfo);

    /**
     * @param $identifier the user id
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByIdentifier($identifier);

    /**
     * @param $identifier the user credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByCredentials($credentials);
}
