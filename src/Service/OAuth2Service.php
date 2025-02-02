<?php

/**
 * This file is part of the Symfony OAuth2 Bundle.
 * 
 * OAuth2 service for handling authorization, token and user info requests.
 *
 * @package     Symfony\Bundle\OAuth2Bundle
 * @author      Bekir ÖZTÜRK <bekirozturk@live.com>
 * @website     https://bekirozturk.com
 * @linkedin    https://www.linkedin.com/in/ozturkbekir/
 * @github      https://github.com/bekirozturk
 * @copyright   2025 Bekir ÖZTÜRK
 * @license     MIT License
 * @version     1.0.0
 * @since       2025-02-02
 */

namespace Symfony\Bundle\OAuth2Bundle\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OAuth2Service
{
    private $client;
    private $params;

    public function __construct(
        HttpClientInterface $client,
        ParameterBagInterface $params
    ) {
        $this->client = $client;
        $this->params = $params;
    }

    public function getAuthorizationUrl(string $state, string $codeVerifier): string
    {
        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $query = http_build_query([
            'response_type' => $_ENV['OAUTH_RESPONSE_TYPE'] ?? 'code',
            'client_id' => $_ENV['OAUTH_CLIENT_ID'],
            'redirect_uri' => $_ENV['OAUTH_REDIRECT_URI'],
            'scope' => $_ENV['OAUTH_SCOPE'],
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => $_ENV['OAUTH_CODE_CHALLENGE_METHOD'] ?? 'S256'
        ]);

        return $_ENV['OAUTH_AUTHORIZE_URL'] . '?' . $query;
    }

    public function requestAccessToken(string $authorizationCode, string $codeVerifier): array
    {
        $response = $this->client->request('POST', $_ENV['OAUTH_TOKEN_URL'], [
            'body' => [
                'client_id' => $_ENV['OAUTH_CLIENT_ID'],
                'client_secret' => $_ENV['OAUTH_CLIENT_SECRET'],
                'grant_type' => $_ENV['OAUTH_GRANT_TYPE'] ?? 'authorization_code',
                'code' => $authorizationCode,
                'redirect_uri' => $_ENV['OAUTH_REDIRECT_URI'],
                'code_verifier' => $codeVerifier,
            ],
        ]);

        return $response->toArray();
    }

    public function getUserInfo(string $accessToken): array
    {
        $response = $this->client->request('GET', $_ENV['OAUTH_USERINFO_URL'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);

        return $response->toArray();
    }
} 