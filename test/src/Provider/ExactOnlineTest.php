<?php

namespace Stephangroen\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Mockery as m;
use Picqer\OAuth2\Client\Provider\ExactOnline;

/**
 * Class ExactOnlineTest
 * @package Stephangroen\OAuth2\Client\Test\Provider
 */
class ExactOnlineTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ExactOnline
     */
    protected $provider;


    /**
     * Mock the ExactOnline object
     */
    protected function setUp()
    {

        $this->provider = new ExactOnline([
            'clientId' => 'mock',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }


    /**
     * Tear down testsuite
     */
    public function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    /**
     * Test the authorization URL and check required query params
     */
    public function testGetAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }


    /**
     * Test getting the access token with expected response
     */
    public function testGetAccessToken()
    {
        $response = m::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeader')->times(1)->andReturn('application/json');
        $response->shouldReceive('getBody')
            ->times(1)
            ->andReturn('{"access_token":"mock_access_token","token_type":"bearer","expires_in":600,"refresh_token":"mock_refresh_token"}');

        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        /** @var AccessToken $token */
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertLessThanOrEqual(time() + 600, $token->getExpires());
        $this->assertGreaterThanOrEqual(time(), $token->getExpires());
        $this->assertEquals('mock_refresh_token', $token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId(), 'Exact Online does not return user ID with access token. Expected null.');
    }


    /**
     *  Test proper handling of the response Exact Online returns on errors
     */
    public function testProperlyHandlesErrorResponses()
    {
        $postResponse = m::mock('Psr\Http\Message\ResponseInterface');
        $postResponse->shouldReceive('getHeader')->times(1)->andReturn('application/json');
        $postResponse->shouldReceive('getBody')
            ->times(1)
            ->andReturn('{"error":{"code":123,"message":{"value":"Error Message Response"}}}');
        $client = m::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($postResponse);
        $this->provider->setHttpClient($client);
        $errorMessage = '';
        $errorCode = 0;
        try {
            $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        } catch (IdentityProviderException $e) {
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
        }
        $this->assertEquals('Error Message Response', $errorMessage);
        $this->assertEquals(123, $errorCode);
    }

    /**
     * Test correct setup of the Exact Online access token params
     */
    public function testExactOnlineAccessTokenParams()
    {
        $response = $this->provider->getExactOnlineAccessTokenParams('testcode');
        $this->assertEquals($response['redirect_uri'], 'none');
        $this->assertEquals($response['client_id'], 'mock');
        $this->assertEquals($response['client_secret'], 'mock_secret');
        $this->assertEquals($response['grant_type'], 'authorization_code');
        $this->assertEquals($response['code'], 'testcode');
    }


}