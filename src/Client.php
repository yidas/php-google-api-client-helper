<?php

namespace yidas\google\apiHelper;

use Google_Client;
use Exception;

/**
 * Google API Client Helper
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @version 1.0.0
 */
class Client
{
    const TOKENINFO_URI = 'https://www.googleapis.com/oauth2/v3/tokeninfo';
    
    /**
     * Google_Client
     *
     * @var Google_Client
     */
    protected static $client;

    /**
     * New a Google_Client with config or set Google_Client by giving
     *
     * @param array|Google_Client Config array or Google_Client
     * @return self
     */
    public static function setClient($input=null)
    {
        // New or encapsulate
        self::$client = ($input instanceof Google_client) ? $input : new Google_Client();

        // While new and there has the config
        if (is_array($input)) {
            
            foreach ($input as $key => $value) {
                // ex. 'authConfig' => setAuthConfig()
                $method = "set" . ucfirst($key);
                // Config method check
                if (!method_exists(self::$client, $method)) {
                    throw new Exception("setClient() Config Key: `{$key}` is invalid referred to Google_Client->{$method}()", 500);
                }
                // Call set method by Google_Client
                call_user_func([self::$client, $method], $value);
            }
        }

        return new self;
    }
    
    /**
     * Get Google_Client from current helper
     *
     * @return Google_Client
     */
    public static function getClient()
    {
        if (!self::$client) {
            
            self::$client = new Google_Client();
        }

        return self::$client;
    }

    /**
     * Refresh AccessToken
     * 
     * Simple way to get refreshed access token or false expired to skip
     *
     * @return array|false return false if AccessToken is not expired, else return new AccessToken
     * @example
     *  if ($newAccessToken = yidas\google\apiHelper\Client::refreshAccessToken())
     *      saveNewAccessToken($newAccessToken);
     */
    public static function refreshAccessToken()
    {
        // Refresh token setting trick
        // @see https://github.com/google/google-api-php-client/issues/263
        self::getClient()->setAccessType('offline');
        self::getClient()->setApprovalPrompt('force');
        
        if (self::getClient()->isAccessTokenExpired()) {
            
            // Refresh the token if it's expired.
            self::getClient()->fetchAccessTokenWithRefreshToken(self::getClient()->getRefreshToken());

            return self::getClient()->getAccessToken();
            
        } else {

            return false;
        }
    }

    /**
     * Verify an access_token. This method will verify the current access_token by Google API, 
     * if one isn't provided.
     *
     * @param string|null $accessToken The token (access_token) that should be verified.
     * @return array|false Returns the token payload as an array if the verification was
     * successful, false otherwise.
     */
    public static function verifyAccessToken($accessToken=null)
    {
        // Check access_token
        if (null === $accessToken) {
            $token = self::getClient()->getAccessToken();
            if (!isset($token['access_token'])) {
                throw new Exception(
                    'access_token must be passed in or set as part of setAccessToken'
                );
            }
            $accessToken = $token['access_token'];
        }

        // Google API request
        $response = (new \GuzzleHttp\Client())
            ->request('GET', self::TOKENINFO_URI, [
                'query' => [
                    'access_token' => $accessToken,
                ],
                'http_errors' => false,
            ]);
        // var_dump($response);exit;

        // Result check
        if (200 != $response->getStatusCode()) {
            return false;
        }
        
        return json_decode($response->getBody(), true);
    }

    /**
     * Verify scopes of tokenInfo by access_token. This method will verify the current access_token 
     * by Google API, if one isn't provided.
     *
     * @param array $scopes Google client scope list, ex: ['https://www.googleapis.com/auth/userinfo.profile']
     * @param string|null $accessToken The token (access_token) that should be verified.
     * @return array|false Returns the token payload as an array if the verification was
     * successful, false otherwise.
     * @example 
     *  $result = \yidas\google\apiHelper\Client::verifyScopes([
     *      'https://www.googleapis.com/auth/userinfo.profile',
     *  ]);
     */
    public static function verifyScopes($scopes, $accessToken=null)
    {
        // Check access_token
        if (!$tokenInfo = self::verifyAccessToken($accessToken)) return false;

        $scopeString = $tokenInfo['scope'];

        // Check scope for each
        foreach ($scopes as $key => $scope) {
            
            if (stripos($scopeString, $scope) === false) return false;
        }

        return true;
    }

    /**
     * __call for encapsulating Google_Client
     *
     * @param string $method
     * @param array $arguments
     * @return self
     */
    public function __call($method, $arguments)
    {
        $return = call_user_func_array([self::getClient(), $method], $arguments);

        return ($return) ? $return : new self;
    }

    /**
     * __callStatic for encapsulating Google_Client
     *
     * @param string $method
     * @param array $arguments
     * @return self
     */
    public static function __callStatic($method, $arguments)
    {
        $return = call_user_func_array([self::getClient(), $method], $arguments);

        return ($return) ? $return : new self;
    }
}
