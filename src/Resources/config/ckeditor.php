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

use Sonata\FormatterBundle\Admin\CkeditorAdminExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()

        ->set('sonata.formatter.ckeditor.extension.class', CkeditorAdminExtension::class);

    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('sonata.formatter.ckeditor.extension', '%sonata.formatter.ckeditor.extension.class%')
            ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media']);
};
