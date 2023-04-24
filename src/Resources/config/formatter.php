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

use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\RawFormatter;
use Sonata\FormatterBundle\Formatter\TextFormatter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.formatter.pool', Pool::class)
            ->call('setLogger', [
                service('logger'),
            ])

        ->set('sonata.formatter.text.text', TextFormatter::class)
            ->tag('sonata.text.formatter')

        ->set('sonata.formatter.text.raw', RawFormatter::class)
            ->tag('sonata.text.formatter');
};
