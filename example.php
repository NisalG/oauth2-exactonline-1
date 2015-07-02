<?php

require_once 'vendor/autoload.php';

$provider = new \Picqer\OAuth2\Client\Provider\ExactOnline([
    'clientId'     => '--EXACTCLIENTID--',
    'clientSecret' => '--EXACTCLIENTSECRET--',
    'redirectUri'  => '--CALLBACKURL--'
]);

if ( ! isset( $_GET['code'] )) {

    // If we don't have an authorization code, then get one
    $authUrl = $provider->getAuthorizationUrl();
    header('Location: ' . $authUrl);
    exit;

} else {

    // Try to get an access token (using the authorization code grant)
    $grant = new \Picqer\OAuth2\Client\Grant\ExactOnlineAccessToken;
    $token = $provider->getAccessToken($grant, $provider->getExactOnlineAccessTokenParams(urldecode($_GET['code'])));

    // Optional: Now you have a token you can look up a users profile details
    try {

        // We got an access token, let's now get the user's details
        /** @var Picqer\OAuth2\Client\Provider\ExactOnlineUser $userDetails */
        $userDetails = $provider->getUser($token);

        printf('Hello %s!', $userDetails->getFullName());

    } catch (Exception $e) {
        exit( 'Didn\'t go well.. ' . $e->getMessage() );
    }

}