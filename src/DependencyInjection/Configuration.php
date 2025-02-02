<?php

/**
 * This file is part of the Symfony OAuth2 Bundle.
 * 
 * Configuration class for bundle settings.
 *
 * @package     Symfony\Bundle\OAuth2Bundle\DependencyInjection
 * @author      Bekir ÖZTÜRK <bekirozturk@live.com>
 * @website     https://bekirozturk.com
 * @linkedin    https://www.linkedin.com/in/ozturkbekir/
 * @github      https://github.com/bekirozturk
 * @copyright   2025 Bekir ÖZTÜRK
 * @license     MIT License
 * @version     1.0.0
 * @since       2025-02-02
 */

namespace Symfony\Bundle\OAuth2Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfony_oauth2');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('client_id')->isRequired()->end()
                ->scalarNode('client_secret')->isRequired()->end()
                ->scalarNode('redirect_uri')->isRequired()->end()
                ->scalarNode('authorize_url')->isRequired()->end()
                ->scalarNode('token_url')->isRequired()->end()
                ->scalarNode('userinfo_url')->isRequired()->end()
                ->scalarNode('scope')->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}