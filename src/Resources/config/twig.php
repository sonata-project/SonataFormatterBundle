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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sonata\FormatterBundle\Extension\ControlFlowExtension;
use Sonata\FormatterBundle\Extension\GistExtension;
use Sonata\FormatterBundle\Twig\Extension\TextFormatterExtension;
use Sonata\FormatterBundle\Twig\TextFormatterRuntime;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.formatter.text.twig', TextFormatterExtension::class)
            ->tag('twig.extension')

        ->set('sonata.formatter.twig.text_formatter_runtime', TextFormatterRuntime::class)
            ->tag('twig.runtime')
            ->args([
                service('sonata.formatter.pool'),
            ])

        ->set('sonata.formatter.twig.gist', GistExtension::class)
            ->public()

        ->set('sonata.formatter.twig.control_flow', ControlFlowExtension::class)
            ->public();
};
