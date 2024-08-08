<?php

namespace App\Services\AmoCrm;

use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\AccessToken;
use AmoCRM\OAuth\AmoCRMOAuth;
use App\Adapters\AmoCrmClient;

class OAuthService
{
    private AmoCRMOAuth $oauth;

    public function __construct() {
        $this->oauth = app(AmoCrmClient::class)->getOAuthClient();
    }

    public function getAuthorizationUrl(): string
    {
        $state = bin2hex(random_bytes(16));

        session(['oauth2state' => $state]);

        return $this->oauth->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message'
        ]);
    }

    public function processToken(array $validation): AccessTokenInterface
    {
        $accessToken = $this->oauth->getAccessTokenByCode($validation['code']);

        $this->saveToken($accessToken);

        return $accessToken;
    }

    public function saveToken(AccessTokenInterface $accessToken): void
    {
        Cache::put('amocrm_access_token', $accessToken->getToken(), now()->addSeconds($accessToken->getExpires() - time()));
        Cache::put('amocrm_refresh_token', $accessToken->getRefreshToken(), now()->addDays(30));
        Cache::put('amocrm_expires', $accessToken->getExpires(), now()->addSeconds($accessToken->getExpires() - time()));
    }

    public static function getAccessToken(): AccessToken
    {
        return new AccessToken([
            'access_token'  => Cache::get('amocrm_access_token') ?? 'token',
            'refresh_token' => Cache::get('amocrm_refresh_token'),
            'expires'       => Cache::get('amocrm_expires'),
        ]);
    }

    public static function isValidToken(): bool
    {
        return Cache::get('amocrm_access_token') ? self::getAccessToken()->hasExpired() : false;
    }
}
