<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ZymfonyExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        if ($config['routing']['enabled'] === true) {
            $loader->load('routing.xml');

            if (array_key_exists('custom_routes', $config['routing'])) {
                $container->setParameter('zymfony.routing.custom_routes', $config['routing']['custom_routes']);
            }
        }
    }
}
