<?php

namespace Hilabs\OneAuth;

use Hilabs\OneAuth\Contract\UserRepository;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class OneAuthUserProvider implements UserProvider
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Lets make the repository take care of returning the user related to the
     * identifier
     * @param mixed $identifier
     * @return Authenticatable
     */
    public function retrieveByID($identifier) {
        return $this->userRepository->getUserByIdentifier($identifier);
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function retrieveByCredentials(array $credentials) {
        return $this->userRepository->getUserByCredentials($credentials);
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function retrieveByToken($identifier, $token) {
        return false;
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function updateRememberToken(Authenticatable $user, $token) {
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function validateCredentials(Authenticatable $user, array $credentials) {
        return false;
     }
}
