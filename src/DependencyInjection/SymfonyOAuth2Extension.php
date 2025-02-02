<?php

/**
 * This file is part of the Symfony OAuth2 Bundle.
 * 
 * Extension class for loading and managing bundle configuration.
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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

class SymfonyOAuth2Extension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');

        foreach ($config as $key => $value) {
            $container->setParameter('symfony_oauth2.' . $key, $value);
        }

        $this->createConfigFiles();
        $this->createControllerFiles();
    }

    public function getAlias(): string
    {
        return 'symfony_oauth2';
    }

    public function getNamespace(): string
    {
        return 'http://example.org/schema/dic/symfony_oauth2';
    }

    public function getXsdValidationBasePath(): string
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    private function createConfigFiles()
    {
        $filesystem = new Filesystem();
        $projectDir = getcwd();

        // Konfigürasyon dosyalarını bundle'ın kendi dizininden kopyala
        $bundleConfigDir = __DIR__ . '/../Resources/config';

        // Hedef dizinleri oluştur
        $filesystem->mkdir($projectDir . '/config/packages');
        $filesystem->mkdir($projectDir . '/config/routes');

        // Dosyaları kopyala
        $filesystem->copy(
            $bundleConfigDir . '/symfony_oauth2.yaml',
            $projectDir . '/config/packages/symfony_oauth2.yaml',
            true
        );
        $filesystem->copy(
            $bundleConfigDir . '/routes.yaml',
            $projectDir . '/config/routes/symfony_oauth2.yaml',
            true
        );
    }

    private function createControllerFiles()
    {
        $filesystem = new Filesystem();
        $projectDir = getcwd();

        // Controller dizinini oluştur
        $controllerDir = $projectDir . '/src/Controller';
        if (!$filesystem->exists($controllerDir)) {
            $filesystem->mkdir($controllerDir);
        }

        // Controller dosyasını kopyala
        $controllerFile = 'SymfonyOauth2Controller.php';
        $sourcePath = __DIR__ . '/../Resources/skeleton/controller/' . $controllerFile;
        $targetPath = $controllerDir . '/' . $controllerFile;

        if (!$filesystem->exists($targetPath)) {
            $filesystem->copy($sourcePath, $targetPath, true);
        }
    }
}