<?php

/**
 * This file is part of the Symfony OAuth2 Bundle.
 * 
 * Main bundle class for Symfony OAuth2 integration.
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

namespace Symfony\Bundle\OAuth2Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\OAuth2Bundle\DependencyInjection\SymfonyOAuth2Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class SymfonyOAuth2Bundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new SymfonyOAuth2Extension();
        }
        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // OAuth2 konfigürasyonunu yükle
        $config = [
            'client_id' => '%env(OAUTH_CLIENT_ID)%',
            'client_secret' => '%env(OAUTH_CLIENT_SECRET)%',
            'redirect_uri' => '%env(OAUTH_REDIRECT_URI)%',
            'authorize_url' => '%env(OAUTH_AUTHORIZE_URL)%',
            'token_url' => '%env(OAUTH_TOKEN_URL)%',
            'userinfo_url' => '%env(OAUTH_USERINFO_URL)%',
            'scope' => '%env(OAUTH_SCOPE)%',
        ];
        
        $this->extension->load([$config], $container);
    }
} 