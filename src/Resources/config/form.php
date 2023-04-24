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

use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('sonata.formatter.form.type.selector', FormatterType::class)
            ->tag('form.type', ['alias' => 'sonata_formatter_type'])
            ->args([
                service('sonata.formatter.pool'),
                service('fos_ck_editor.configuration'),
            ])

        ->set('sonata.formatter.form.type.simple', SimpleFormatterType::class)
            ->tag('form.type', ['alias' => 'sonata_simple_formatter_type'])
            ->args([
                service('fos_ck_editor.configuration'),
            ]);
};
