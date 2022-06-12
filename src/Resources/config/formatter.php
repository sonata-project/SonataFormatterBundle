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

use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\RawFormatter;
use Sonata\FormatterBundle\Formatter\TextFormatter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()

        ->set('sonata.formatter.text.text.class', TextFormatter::class)

        ->set('sonata.formatter.text.raw.class', RawFormatter::class);

    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.formatter.pool', Pool::class)
            ->call('setLogger', [
                new ReferenceConfigurator('logger'),
            ])

        ->set('sonata.formatter.text.text', '%sonata.formatter.text.text.class%')
            ->tag('sonata.text.formatter')

        ->set('sonata.formatter.text.raw', '%sonata.formatter.text.raw.class%')
            ->tag('sonata.text.formatter');
};
