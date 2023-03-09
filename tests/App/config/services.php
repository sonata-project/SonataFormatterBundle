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
use Sonata\FormatterBundle\Tests\App\Admin\TextEntityAdmin;
use Sonata\FormatterBundle\Tests\App\Controller\TextFormatterController;
use Sonata\FormatterBundle\Tests\App\Entity\TextEntity;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set(TextEntityAdmin::class)
            ->tag('sonata.admin', [
                'model_class' => TextEntity::class,
                'manager_type' => 'orm',
            ])

        ->set(TextFormatterController::class)
            ->public()
            ->tag('container.service_subscriber')
            ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)]);
};
