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

use Psr\Container\ContainerInterface;
use Sonata\FormatterBundle\Admin\CkeditorAdminExtension;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;
use Sonata\FormatterBundle\Extension\MediaExtension;
use Sonata\MediaBundle\Model\MediaManagerInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->alias(MediaManagerInterface::class, 'sonata.media.manager.media')

        ->set('sonata.formatter.ckeditor.extension', CkeditorAdminExtension::class)
            ->tag('sonata.admin.extension', ['target' => 'sonata.media.admin.media'])

        ->set('sonata.formatter.ckeditor.controller', CkeditorAdminController::class)
            ->public()
            ->tag('container.service_subscriber')
            ->call('setContainer', [service(ContainerInterface::class)])

        ->set('sonata.formatter.twig.media', MediaExtension::class)
            ->public();
};
