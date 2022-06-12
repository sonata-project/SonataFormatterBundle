<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\App;

use FOS\CKEditorBundle\FOSCKEditorBundle;
use Sonata\BlockBundle\SonataBlockBundle;
use Sonata\FormatterBundle\SonataFormatterBundle;
use Sonata\MediaBundle\SonataMediaBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @see https://github.com/psalm/psalm-plugin-symfony/pull/220
 */
final class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new FOSCKEditorBundle(),
            new SonataBlockBundle(),
            new SonataMediaBundle(),
            new SonataFormatterBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return sprintf('%scache', $this->getBaseDir());
    }

    public function getLogDir(): string
    {
        return sprintf('%slog', $this->getBaseDir());
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    /**
     * TODO: Add typehint when support for Symfony < 5.1 is dropped.
     *
     * @param RoutingConfigurator $routes
     */
    protected function configureRoutes($routes): void
    {
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'MySecret',
        ]);

        $containerBuilder->loadFromExtension('sonata_media', [
            'default_context' => 'default',
            'contexts' => [
                'default' => [
                    'download' => [
                        'strategy' => 'sonata.media.security.public_strategy',
                    ],
                ],
            ],
            'cdn' => [
                'server' => [
                    'path' => '/uploads/media',
                ],
            ],
            'filesystem' => [
                'local' => [
                    'directory' => '%kernel.project_dir%/uploads',
                    'create' => true,
                ],
            ],
        ]);

        $containerBuilder->loadFromExtension('sonata_formatter', [
            'default_formatter' => 'rawhtml',
            'formatters' => [
                'rawhtml' => [
                    'service' => 'sonata.formatter.text.raw',
                ],
            ],
        ]);
    }

    private function getBaseDir(): string
    {
        return sprintf('%s/sonata-formatter-bundle/var/', sys_get_temp_dir());
    }
}
