<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class AppExtension extends Extension
{
    /**
     * Load configuration for AppBundle.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['twitter'] as $key => $value) {
            $container->setParameter('app.twitter.' . $key, $value);
        }

        $container->setParameter('app.mail.from', $config['mail']['from']);
        $container->setParameter('app.mail.reply_to', $config['mail']['reply_to']);
    }
}
