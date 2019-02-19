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

namespace Sonata\FormatterBundle\Controller;

use Sonata\MediaBundle\Controller\MediaAdminController;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class CkeditorAdminController extends MediaAdminController
{
    /**
     * @throws AccessDeniedException
     */
    public function browserAction(Request $request): Response
    {
        $this->checkIfMediaBundleIsLoaded();

        $this->admin->checkAccess('list');

        $datagrid = $this->admin->getDatagrid();

        $filters = $request->get('filter');

        // set the default context
        if (!$filters || !\array_key_exists('context', $filters)) {
            $context = $this->admin->getPersistentParameter('context', $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        $datagrid->setValue('context', null, $context);
        $datagrid->setValue('providerName', null, $this->admin->getPersistentParameter('provider'));

        $rootCategory = null;
        if ($this->has('sonata.media.manager.category')) {
            $rootCategory = $this->get('sonata.media.manager.category')->getRootCategory($context);
        }

        if (null !== $rootCategory && !$filters) {
            $datagrid->setValue('category', null, $rootCategory->getId());
        }
        if ($this->has('sonata.media.manager.category') && $request->get('category')) {
            $category = $this->get('sonata.media.manager.category')->findOneBy([
                'id' => (int) $request->get('category'),
                'context' => $context,
            ]);

            if (!empty($category)) {
                $datagrid->setValue('category', null, $category->getId());
            } else {
                $datagrid->setValue('category', null, $rootCategory->getId());
            }
        }

        $formats = [];
        foreach ($datagrid->getResults() as $media) {
            $formats[$media->getId()] = $this->get('sonata.media.pool')->getFormatNamesByContext($media->getContext());
        }

        $formView = $datagrid->getForm()->createView();

        $this->setFormTheme($formView, $this->admin->getFilterTheme());

        return $this->renderWithExtraParams($this->getTemplate('browser'), [
            'action' => 'browser',
            'form' => $formView,
            'datagrid' => $datagrid,
            'root_category' => $rootCategory,
            'formats' => $formats,
        ]);
    }

    /**
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function uploadAction(Request $request): Response
    {
        $this->checkIfMediaBundleIsLoaded();

        $this->admin->checkAccess('create');

        $mediaManager = $this->get('sonata.media.manager.media');

        $provider = $request->get('provider');
        $file = $request->files->get('upload');

        if (!$request->isMethod('POST') || !$provider || null === $file) {
            throw $this->createNotFoundException();
        }

        $pool = $this->get('sonata.media.pool');
        $context = $request->get('context', $pool->getDefaultContext());

        $media = $mediaManager->create();
        $media->setBinaryContent($file);

        $mediaManager->save($media, $context, $provider);
        $this->admin->createObjectSecurity($media);

        $format = $pool->getProvider($provider)->getFormatName(
            $media,
            $request->get('format', MediaProviderInterface::FORMAT_REFERENCE)
        );

        return $this->renderWithExtraParams($this->getTemplate('upload'), [
            'action' => 'list',
            'object' => $media,
            'format' => $format,
        ]);
    }

    private function getTemplate(string $name): string
    {
        $templates = $this->container->getParameter('sonata.formatter.ckeditor.configuration.templates');

        if (isset($templates[$name])) {
            return $templates[$name];
        }
    }

    /**
     * Checks if SonataMediaBundle is loaded otherwise throws an exception.
     *
     * @throws \RuntimeException
     */
    private function checkIfMediaBundleIsLoaded(): void
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!isset($bundles['SonataMediaBundle'])) {
            throw new \RuntimeException('You cannot use this feature because you have to use SonataMediaBundle');
        }
    }

    /**
     * Sets the admin form theme to form view. Used for compatibility between Symfony versions.
     */
    private function setFormTheme(FormView $formView, array $theme): void
    {
        $this->get('twig')->getRuntime(FormRenderer::class)->setTheme($formView, $theme);
    }
}
