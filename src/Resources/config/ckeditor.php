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

use Psr\Container\ContainerInterface;
use Sonata\FormatterBundle\Admin\CkeditorAdminExtension;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->parameters()

        ->set('sonata.formatter.ckeditor.extension.class', CkeditorAdminExtension::class);

    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->alias(MediaManagerInterface::class, 'sonata.media.manager.media')

        ->set('sonata.formatter.ckeditor.extension', '%sonata.formatter.ckeditor.extension.class%')
            ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media'])

        ->set('sonata.formatter.ckeditor.controller', CkeditorAdminController::class)
            ->public()
            ->tag('container.service_subscriber')
            ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);
};
