<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\AccessToken;
use App\Adapters\AmoCrmClient;
use App\Crm\AmoCrm;
use Illuminate\Support\Facades\Config;

class AmoCrmOAuthService
{
    private $amoCrmClient;

    public function __construct(AmoCrmClient $amoCrmClient)
    {
        $this->amoCrmClient = $amoCrmClient;
    }

    public function getAuthorizationUrl(string $state): string
    {
        return $this->amoCrmClient->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message'
        ]);
    }

    public function handleCallback(string $code): AccessTokenInterface
    {
        $oauth = $this->amoCrmClient->getOAuthClient();
        $oauth->setBaseDomain(config('crm.' . AmoCrm::getKey() . '.domain'));
        return $oauth->getAccessTokenByCode($code);
    }

    public function saveToken(AccessTokenInterface $accessToken)
    {
        Cache::put('amocrm_access_token', $accessToken->getToken(), now()->addSeconds($accessToken->getExpires() - time()));
        Cache::put('amocrm_refresh_token', $accessToken->getRefreshToken(), now()->addDays(30));
        Cache::put('amocrm_expires', $accessToken->getExpires(), now()->addSeconds($accessToken->getExpires() - time()));
    }

    public function getAccessToken(): AccessToken
    {
        return new AccessToken([
            'access_token' => Cache::get('amocrm_access_token'),
            'refresh_token' => Cache::get('amocrm_refresh_token'),
            'expires' => Cache::get('amocrm_expires'),
        ]);
    }
}
