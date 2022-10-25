<?php

/*
 * Copyright (c) 2022 Rhyme Digital LLC (https://rhyme.digital)
 *
 * @license LGPL-3.0-or-later
 */

namespace Rhyme\CalendarWidget\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;

class RhymeCalendarBundleExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'rhyme_calendar_widget';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('listener.yml');
        $loader->load('services.yml');
        $loader->load('routing.yml');
    }
}
