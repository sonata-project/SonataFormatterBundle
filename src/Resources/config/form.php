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

use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    $containerConfigurator->services()

        ->set('sonata.formatter.form.type.selector', FormatterType::class)
            ->tag('form.type', ['alias' => 'sonata_formatter_type'])
            ->args([
                new ReferenceConfigurator('sonata.formatter.pool'),
                new ReferenceConfigurator('translator'),
                new ReferenceConfigurator('fos_ck_editor.configuration'),
            ])

        ->set('sonata.formatter.form.type.simple', SimpleFormatterType::class)
            ->tag('form.type', ['alias' => 'sonata_simple_formatter_type'])
            ->args([
                new ReferenceConfigurator('fos_ck_editor.configuration'),
            ]);
};
