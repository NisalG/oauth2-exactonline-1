<?php

namespace Picqer\OAuth2\Client\Provider;

use InvalidArgumentException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class ExactOnline extends AbstractProvider
{

    /**
     * API url
     *
     * @var string
     */
    protected $apiUrl = 'https://start.exactonline.nl/api';

    /**
     * Used API version
     *
     * @var string
     */
    protected $apiVersion = 'v1';


    /**
     * Return the base authorization url
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->apiUrl . '/oauth2/auth';
    }


    /**
     * Return the base access token url
     *
     * @return string
     */
    public function getBaseAccessTokenUrl()
    {
        return $this->apiUrl . '/oauth2/token';
    }


    /**
     * Return the url for retrieving user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getUserDetailsUrl(AccessToken $token)
    {
        return $this->apiUrl . '/' . $this->apiVersion . '/current/Me';
    }


    /**
     * Get the default scopes used by this provider.
     *
     * Note: Exact Online does not use scopes
     *
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        return [ ];
    }


    /**
     * Check the response for errors
     *
     * @param ResponseInterface $response
     * @param string            $data
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ( ! empty( $data['error'] )) {
            $error = $data['error']['message']['value'];
            $code = $data['error']['code'] ?: 0;
            throw new IdentityProviderException($error, $code, $data);
        }
    }


    /**
     * Return the default collection of access token params for Exact Online
     *
     * @param $code
     *
     * @return array
     */
    public function getExactOnlineAccessTokenParams($code)
    {
        return [
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code'          => $code
        ];
    }


    /**
     * Returns a prepared request for requesting an access token.
     *
     * @param array $params Query string parameters
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function getAccessTokenRequest(array $params)
    {
        $url = $this->getBaseAccessTokenUrl();
        $query = $this->getAccessTokenQuery($params);
        $method = strtoupper($this->getAccessTokenMethod());

        $options = [ ];

        switch ($method) {
            case 'GET':
                $url = $this->appendQuery($url, $query);
                break;
            case 'POST':
                $options['body'] = $query;
                break;
            default:
                throw new InvalidArgumentException("Unsupported access token request method: '$method'");
        }

        // Added the application/x-www-form-urlencoded header as required by Exact Online
        $options['headers'] = [ 'Content-Type' => 'application/x-www-form-urlencoded' ];

        return $this->getRequest($method, $url, $options);
    }


    /**
     * Generate a user object from a successful user details request.
     *
     * @param array|object $response
     * @param AccessToken  $token
     *
     * @return \League\OAuth2\Client\Provider\UserInterface
     */
    protected function createUser(array $response, AccessToken $token)
    {
        return new ExactOnlineUser($response);
    }


    /**
     * @param AccessToken $token
     *
     * @return \League\OAuth2\Client\Provider\UserInterface
     */
    public function getUser(AccessToken $token)
    {
        $response = $this->fetchUserDetails($token);

        return $this->createUser($response['d']['results'][0], $token);
    }


    /**
     * Return the default authorization headers for each request
     *
     * @param AccessToken|null $token
     *
     * @return array
     */
    protected function getAuthorizationHeaders($token = null)
    {
        if (is_null($token)) {
            return [ ];
        }

        return [ 'Authorization' => 'Bearer ' . $token->getToken() ];
    }


    /**
     * Return the default headers for each request
     *
     * @param AccessToken|null $token
     *
     * @return array
     */
    protected function getDefaultHeaders($token = null)
    {
        return [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

}
