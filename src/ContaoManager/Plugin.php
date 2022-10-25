<?php

declare(strict_types=1);

/**
 * Rhyme Calendar Widget Bundle
 *
 * Copyright (c) 2022 Rhyme.Digital
 *
 * @license LGPL-3.0+
 */

namespace Rhyme\CalendarWidget\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Rhyme\CalendarWidget\RhymeCalendarWidgetBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

final class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        $loadAfter = [
            ContaoCoreBundle::class
        ];

        return [
            BundleConfig::create(RhymeCalendarWidgetBundle::class)
                ->setLoadAfter($loadAfter),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        return $resolver
            ->resolve(__DIR__ . '/../Resources/config/routing.yml')
            ->load(__DIR__ . '/../Resources/config/routing.yml')
            ;
    }
}