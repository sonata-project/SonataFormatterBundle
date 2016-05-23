<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Controller;

use Sonata\MediaBundle\Controller\MediaAdminController as BaseMediaAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CkeditorAdminController extends BaseMediaAdminController
{
    /**
     * Returns the response object associated with the browser action.
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function browserAction()
    {
        $this->checkIfMediaBundleIsLoaded();

        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();

        $filters = $this->getRequest()->get('filter');

        // set the default context
        if (!$filters) {
            $context = $this->admin->getPersistentParameter('context', $this->get('sonata.media.pool')->getDefaultContext());
        } else {
            $context = $filters['context']['value'];
        }

        $datagrid->setValue('context', null, $context);
        $datagrid->setValue('providerName', null, $this->admin->getPersistentParameter('provider'));

        // retrieve the main category for the tree view
        $category = null;
        if ($this->container->has('sonata.media.manager.category')) {
            $category = $this->container->get('sonata.media.manager.category')->getRootCategory($context);
        }

        if (!$filters && null !== $category) {
            $datagrid->setValue('category', null, $category->getId());
        }

        if ($this->getRequest()->get('category')) {
            $datagrid->setValue('category', null, $this->getRequest()->get('category'));
        }

        $formats = array();

        foreach ($datagrid->getResults() as $media) {
            $formats[$media->getId()] = $this->get('sonata.media.pool')->getFormatNamesByContext($media->getContext());
        }

        $formView = $datagrid->getForm()->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->getTemplate('browser'), array(
            'action' => 'browser',
            'form' => $formView,
            'datagrid' => $datagrid,
            'root_category' => $category,
            'formats' => $formats,
        ));
    }

    /**
     * Returns the response object associated with the upload action.
     *
     * @return Response
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function uploadAction()
    {
        $this->checkIfMediaBundleIsLoaded();

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $mediaManager = $this->get('sonata.media.manager.media');

        $request = $this->getRequest();
        $provider = $request->get('provider');
        $file = $request->files->get('upload');

        if (!$request->isMethod('POST') || !$provider || null === $file) {
            throw $this->createNotFoundException();
        }

        $context = $request->get('context', $this->get('sonata.media.pool')->getDefaultContext());

        $media = $mediaManager->create();
        $media->setBinaryContent($file);

        $mediaManager->save($media, $context, $provider);
        $this->admin->createObjectSecurity($media);

        return $this->render($this->getTemplate('upload'), array(
            'action' => 'list',
            'object' => $media,
        ));
    }

    /**
     * Returns a template.
     *
     * @param string $name
     *
     * @return string
     */
    private function getTemplate($name)
    {
        $templates = $this->container->getParameter('sonata.formatter.ckeditor.configuration.templates');

        if (isset($templates[$name])) {
            return $templates[$name];
        }

        return;
    }

    /**
     * Checks if SonataMediaBundle is loaded otherwise throws an exception.
     *
     * @throws \RuntimeException
     */
    private function checkIfMediaBundleIsLoaded()
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!isset($bundles['SonataMediaBundle'])) {
            throw new \RuntimeException('You cannot use this feature because you have to use SonataMediaBundle');
        }
    }
}
