<?php

namespace Hilabs\OneAuth;

use Hilabs\OneAuth\LaravelSessionStore;
use Hilabs\OneAuth\Exception\ApiException;
use Hilabs\OneAuth\Exception\CoreException;
use League\OAuth2\Client\Provider\GenericProvider;

class OneAuthSDK
{
    /**
     * Available keys to persist data.
     *
     * @var array
     */
    public $persistantMap = array(
        'access_token',
        'user'
    );

    /**
     * Auth0 URL Map.
     *
     * @var array
     */
    public static $URL_MAP = array(
        'api'           => 'http://auth.server.app/api/',
        'authorize'     => 'http://auth.server.app/authorize/',
        'token'         => 'http://auth.server.app/oauth/token/',
        'user_info'     => 'http://auth.server.app/userinfo/',
    );

    /**
     * OneAuth Client ID
     *
     * @var string
     */
    protected $client_id;

    /**
     * OneAuth Client Secret
     *
     * @var string
     */
    protected $client_secret;

    /**
     * The access token retrieved after authorization.
     * NULL means that there is no authorization yet.
     *
     * @var string
     */
    protected $access_token;

    /**
     * The user object
     *
     * @var string
     */
    protected $user;

    /**
     * OAuth2 Client.
     *
     * @var \OAuth2\Client
     */
    protected $oauth_client;


    function __construct(array $config)
    {
        // check for system requirements
        $this->checkRequirements();

        if (isset($config['client_id'])) {
            $this->client_id = $config['client_id'];
        } else {
            throw new CoreException('Invalid client_id');
        }

        if (isset($config['client_secret'])) {
            $this->client_secret = $config['client_secret'];
        } else {
            throw new CoreException('Invalid client_secret');
        }

        $this->store = new LaravelSessionStore();

        $this->oauth_client = new GenericProvider([
            'clientId'                => $this->client_id,    // The client ID assigned to you by the provider
            'clientSecret'            => $this->client_secret,   // The client password assigned to you by the provider
            'redirectUri'             => isset($config['redirect_uri']) ? $config['redirect_uri'] : '',
            'urlAuthorize'            => self::$URL_MAP['authorize'],
            'urlAccessToken'          => self::$URL_MAP['token'],
            'urlResourceOwnerDetails' => self::$URL_MAP['user_info']
        ]);

        $this->user = $this->store->get("user");
        $this->access_token = $this->store->get("access_token");

        // if (!$this->access_token) {
        //     $this->oauth_client->setAccessToken($this->access_token);
        // }

    }

    private function login() {

        try {

            // Make the call
            $auth_response = $this->oauth_client->getAccessToken('password', [
                'username' => 'shandy05@mail.com',
                'password' => 'Asdw1234##'
            ]);

            // Parse it
            $access_token = null !== $auth_response->getToken() ? $auth_response->getToken() : false;
            if (!$access_token) {
                throw new ApiException('Invalid access_token - Retry login.');
            }

            // Set it and persist it, if needed
            $this->setAccessToken($access_token);

            // get user info from resource owner
            $resourceOwner = $this->oauth_client->getResourceOwner($auth_response);
            $user = $resourceOwner->toArray();

            $this->setUser($user);

            return true;

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

            // Failed to get the access token
            exit($e->getMessage());

        }

    }

    /**
     * Requests user info to Auth0 server.
     *
     * @return array
     */
    public function getUser() {
        // Ensure we have the user info
        if ($this->user === null) {
            $this->login();
        }
        if (!is_array($this->user)) {
            return null;
        }
        return $this->user;
    }

    public function setUser($user) {
        $key = array_search('user',$this->persistantMap);
        if ($key !== false) {
            $this->store->set('user', $user);
        }
        $this->user = $user;
        return $this;
    }

     /**
     * Sets and persists $access_token.
     *
     * @param string $access_token
     *
     * @return Auth0\SDK\BaseAuth0
     */
    public function setAccessToken($access_token) {
        $key = array_search('access_token',$this->persistantMap);
        if ($key !== false) {
            $this->store->set('access_token', $access_token);
        }
        $this->access_token = $access_token;
        return $this;
    }

    /**
     * Gets $access_token.
     * @return string
     */
    final public function getAccessToken() {
        if ($this->access_token === null) {
            $this->login();
        }
        return $this->access_token;
    }

    /**
     * Logout (removes all persisten data)
     */
    final public function logout()
    {
        $this->deleteAllPersistentData();
        $this->access_token = NULL;
    }

    /**
     * Deletes all persistent data, for every mapped key.
     */
    public function deleteAllPersistentData()
    {
        foreach ($this->persistantMap as $key) {
            $this->store->delete($key);
        }
    }

    /**
     * Checks for all dependencies of SDK or API.
     *
     * @throws CoreException If CURL extension is not found.
     * @throws CoreException If JSON extension is not found.
     */
    final public function checkRequirements()
    {
        if (!function_exists('curl_version')) {
            throw new CoreException('CURL extension is needed to use Auth0 SDK. Not found.');
        }
        if (!function_exists('json_decode')) {
            throw new CoreException('JSON extension is needed to use Auth0 SDK. Not found.');
        }
    }

    // -------------------------------------------------------------------------------------------------------------- //

    /**
     * Sets $client_id.
     *
     * @param string $client_id
     *
     * @return Auth0\SDK\BaseAuth0
     */
    final public function setClientId($client_id)
    {
        $this->client_id = $client_id;
        return $this;
    }

    /**
     * Gets $client_id.
     *
     * @return string
     */
    final public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Sets $client_secret.
     *
     * @param string $client_secret
     *
     * @return Auth0\SDK\BaseAuth0
     */
    final public function setClientSecret($client_secret)
    {
        $this->client_secret = $client_secret;
        return $this;
    }

    /**
     * Gets $client_secret.
     *
     * @return string
     */
    final public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * Sets $redirect_uri.
     *
     * @param string $redirect_uri
     *
     * @return Auth0\SDK\BaseAuth0
     */
    final public function setRedirectUri($redirect_uri)
    {
        $this->redirect_uri = $redirect_uri;
        return $this;
    }

    /**
     * Gets $redirect_uri.
     *
     * @return string
     */
    final public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

}
