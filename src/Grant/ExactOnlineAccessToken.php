<?php

namespace Picqer\OAuth2\Client\Grant;

use League\OAuth2\Client\Grant\AbstractGrant;

class ExactOnlineAccessToken extends AbstractGrant
{

    protected function getName()
    {
        return 'exactonline_access_token';
    }


    protected function getRequiredRequestParameters()
    {
        return [
            'code',
            'redirect_uri',
            'grant_type',
            'client_id',
            'client_secret'
        ];
    }
}
