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

namespace Sonata\FormatterBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

/**
 * Adds browser and upload routes to the Admin.
 *
 * @author Kévin Dunglas <kevin@les-tilleuls.coop>
 *
 * @phpstan-extends AbstractAdminExtension<object>
 */
final class CkeditorAdminExtension extends AbstractAdminExtension
{
    public function configure(AdminInterface $admin): void
    {
        $admin->setTemplate('outer_list_rows_browser', '@SonataFormatter/Ckeditor/list_outer_rows_browser.html.twig');
    }

    public function configurePersistentParameters(AdminInterface $admin, array $parameters): array
    {
        if ($admin->hasRequest()) {
            $request = $admin->getRequest();

            $parameters['CKEditor'] = $request->query->get('CKEditor');
            $parameters['CKEditorFuncNum'] = $request->query->get('CKEditorFuncNum');
        }

        return $parameters;
    }

    public function configureBatchActions(AdminInterface $admin, array $actions): array
    {
        if ('browser' === $admin->getListMode()) {
            return [];
        }

        return $actions;
    }

    public function configureRoutes(AdminInterface $admin, RouteCollectionInterface $collection): void
    {
        $collection->add('browser', null, [
            '_controller' => 'sonata.formatter.ckeditor.controller::browserAction',
        ]);

        $collection->add('upload', null, [
            '_controller' => 'sonata.formatter.ckeditor.controller::uploadAction',
        ]);
    }
}
