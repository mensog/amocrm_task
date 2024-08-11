<?php

namespace App\Services\AmoCrm;

use Illuminate\Support\Facades\Cache;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\AccessToken;
use App\Adapters\AmoCrmClient;
use App\Crm\AmoCrm;

class OAuthService
{
    private AmoCrmClient $apiClient;

    public function __construct() {
        // dump(app(AmoCrmClient::class)->setAccountBaseDomain(AmoCrm::getConfig('domain')));
        $this->apiClient = app(AmoCrmClient::class)->setAccountBaseDomain(AmoCrm::getConfig('domain'));
    }

    public function getAuthorizationUrl(): string
    {
        $state = bin2hex(random_bytes(16));

        session(['oauth2state' => $state]);

        return $this->apiClient->getOAuthClient()->getAuthorizeUrl([
            'state' => $state,
            'mode' => 'post_message'
        ]);
    }

    public function processToken($validation): AccessTokenInterface
    {
        // dd($this->apiClient, $validation);
        $accessToken = $this->apiClient->getOAuthClient()->getAccessTokenByCode($validation['code']);
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
            'access_token'  => Cache::get('amocrm_access_token'),
            'refresh_token' => Cache::get('amocrm_refresh_token'),
            'expires'       => Cache::get('amocrm_expires'),
            'baseDomain'    => AmoCrm::getConfig('domain')
        ]);
    }

    public static function isValidToken(): bool
    {
        return Cache::get('amocrm_access_token') ? !self::getAccessToken()->hasExpired() : false;
    }
}
