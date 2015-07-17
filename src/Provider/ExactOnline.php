<?php

namespace Picqer\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class ExactOnline extends AbstractProvider
{

    use BearerAuthorizationTrait;

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
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->apiUrl . '/oauth2/token';
    }


    /**
     * Get the default scopes used by this provider.
     *
     * Note: Exact Online does not use scopes
     *
     */
    protected function getDefaultScopes()
    {
        return [];
    }


    /**
     * Check the response for errors
     *
     * @param ResponseInterface $response
     * @param string $data
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $error = $data['error']['message']['value'];
            $code = $data['error']['code'] ?: 0;
            throw new IdentityProviderException($error, $code, $data);
        }
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
        ];
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
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code
        ];
    }


    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->apiUrl . '/' . $this->apiVersion . '/current/Me';
    }

    /**
     * Generate a resource owner object from a successful resource owner details request.
     *
     * @param object $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new ExactOnlineUser($response);
    }

    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        return $this->createResourceOwner($response['d']['results'][0], $token);
    }

}
