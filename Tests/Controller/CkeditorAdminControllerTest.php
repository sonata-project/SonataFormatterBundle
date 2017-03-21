<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Controller;

use Prophecy\Argument;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;
use Sonata\FormatterBundle\Tests\TestCase;

class EntityWithGetId
{
    public function getId()
    {
    }
}

class CkeditorAdminControllerTest extends TestCase
{
    private $container;
    private $admin;
    private $request;
    private $controller;

    protected function setUp()
    {
        $this->container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->admin = $this->prophesize('Sonata\MediaBundle\Admin\BaseMediaAdmin');
        $this->request = $this->prophesize('Symfony\Component\HttpFoundation\Request');

        $this->configureCRUDController();

        $this->controller = new CkeditorAdminController();
        $this->controller->setContainer($this->container->reveal());
    }

    public function testBrowserAction()
    {
        $datagrid = $this->prophesize('Sonata\AdminBundle\Datagrid\DatagridInterface');
        $pool = $this->prophesize('Sonata\MediaBundle\Provider\Pool');
        $categoryManager = $this->prophesize('Sonata\MediaBundle\Model\CategoryManagerInterface');
        $category = $this->prophesize('Sonata\FormatterBundle\Tests\Controller\EntityWithGetId');
        $form = $this->prophesize('Symfony\Component\Form\Form');
        $formView = $this->prophesize('Symfony\Component\Form\FormView');

        $this->configureSetFormTheme($formView->reveal(), 'filterTheme');
        $this->configureRender('templateList', Argument::type('array'), 'renderResponse');
        $datagrid->setValue('context', null, 'another_context')->shouldBeCalled();
        $datagrid->setValue('category', null, 1)->shouldBeCalled();
        $datagrid->setValue('providerName', null, 'provider')->shouldBeCalled();
        $datagrid->getResults()->willReturn(array());
        $datagrid->getForm()->willReturn($form->reveal());
        $pool->getDefaultContext()->willReturn('context');
        $categoryManager->getRootCategory('another_context')->willReturn($category->reveal());
        $categoryManager->findOneBy(array(
            'id' => 2,
            'context' => 'another_context',
        ))->willReturn($category->reveal());
        $category->getId()->willReturn(1);
        $form->createView()->willReturn($formView->reveal());
        $this->container->get('sonata.media.pool')->willReturn($pool->reveal());
        $this->container->has('sonata.media.manager.category')->willReturn(true);
        $this->container->get('sonata.media.manager.category')->willReturn($categoryManager->reveal());
        $this->container->getParameter('kernel.bundles')->willReturn(array('SonataMediaBundle' => true));
        $this->admin->checkAccess('list')->shouldBeCalled();
        $this->admin->getDatagrid()->willReturn($datagrid->reveal());
        $this->admin->getPersistentParameter('context', 'context')->willReturn('another_context');
        $this->admin->getPersistentParameter('provider')->willReturn('provider');
        $this->admin->getFilterTheme()->willReturn('filterTheme');
        $this->request->get('filter')->willReturn(array());
        $this->request->get('category')->willReturn(2);

        $response = $this->controller->browserAction();

        $this->assertSame('renderResponse', $response);
    }

    public function testUpload()
    {
        $media = $this->prophesize('Sonata\MediaBundle\Model\MediaInterface');
        $mediaManager = $this->prophesize('Sonata\MediaBundle\Model\MediaManagerInterface');
        $filesBag = $this->prophesize('Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface');
        $pool = $this->prophesize('Sonata\MediaBundle\Provider\Pool');

        $this->configureRender('templateList', Argument::type('array'), 'renderResponse');
        $pool->getDefaultContext()->willReturn('context');
        $filesBag->get('upload')->willReturn(new \stdClass());
        $mediaManager->create()->willReturn($media->reveal());
        $mediaManager->save($media->reveal(), 'context', 'provider')->shouldBeCalled();
        $this->admin->checkAccess('create')->shouldBeCalled();
        $this->admin->createObjectSecurity($media->reveal())->shouldBeCalled();
        $this->container->get('sonata.media.manager.media')->willReturn($mediaManager->reveal());
        $this->container->getParameter('kernel.bundles')->willReturn(array('SonataMediaBundle' => true));
        $this->container->get('sonata.media.pool')->willReturn($pool->reveal());
        $this->request->get('provider')->willReturn('provider');
        $this->request->isMethod('POST')->willReturn(true);
        $this->request->files = $filesBag->reveal();
        $this->request->get('context', 'context')->willReturn('context');

        $response = $this->controller->uploadAction();

        $this->assertSame('renderResponse', $response);
    }

    private function configureCRUDController()
    {
        $pool = $this->prophesize('Sonata\AdminBundle\Admin\Pool');
        $breadcrumbsBuilder = $this->prophesize('Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface');

        $this->configureGetCurrentRequest($this->request->reveal());
        $pool->getAdminByAdminCode('admin_code')->willReturn($this->admin->reveal());
        $this->request->isXmlHttpRequest()->willReturn(false);
        $this->request->get('_xml_http_request')->willReturn(false);
        $this->request->get('_sonata_admin')->willReturn('admin_code');
        $this->request->get('uniqid')->shouldBeCalled();
        $this->container->get('sonata.admin.pool')->willReturn($pool->reveal());
        $this->container->get('sonata.admin.breadcrumbs_builder')->willReturn($breadcrumbsBuilder->reveal());
        $this->admin->getTemplate('layout')->willReturn('layout.html.twig');
        $this->admin->isChild()->willReturn(false);
        $this->admin->setRequest($this->request->reveal())->shouldBeCalled();
    }

    private function configureGetCurrentRequest($request)
    {
        // NEXT_MAJOR: Remove this trick when bumping Symfony requirement to 2.8+.
        if (class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            $requestStack = $this->prophesize('Symfony\Component\HttpFoundation\RequestStack');

            $this->container->has('request_stack')->willReturn(true);
            $this->container->get('request_stack')->willReturn($requestStack->reveal());
            $requestStack->getCurrentRequest()->willReturn($request);
        } else {
            $this->container->has('request_stack')->willReturn(false);
            $this->container->get('request')->willReturn($request);
        }
    }

    private function configureSetFormTheme($formView, $formTheme)
    {
        $twig = $this->prophesize('\Twig_Environment');
        $twigRenderer = $this->prophesize('Symfony\Bridge\Twig\Form\TwigRenderer');

        $this->container->get('twig')->willReturn($twig->reveal());

        // Remove this trick when bumping Symfony requirement to 3.2+.
        if (method_exists('Symfony\Bridge\Twig\AppVariable', 'getToken')) {
            $twig->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->willReturn($twigRenderer->reveal());
        } else {
            $formExtension = $this->prophesize('Symfony\Bridge\Twig\Extension\FormExtension');
            $formExtension->renderer = $twigRenderer->reveal();

            // This Throw is for the CRUDController::setFormTheme()
            $twig->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->willThrow('\Twig_Error_Runtime');
            $twig->getExtension('Symfony\Bridge\Twig\Extension\FormExtension')->willReturn($formExtension->reveal());
        }
        $twigRenderer->setTheme($formView, $formTheme)->shouldBeCalled();
    }

    private function configureRender($template, $data, $rendered)
    {
        $templating = $this->prophesize('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');
        $pool = $this->prophesize('Sonata\MediaBundle\Provider\Pool');

        $this->admin->getPersistentParameters()->willReturn(array('param' => 'param'));
        $this->container->has('templating')->willReturn(true);
        $this->container->get('templating')->willReturn($templating->reveal());
        $this->container->get('sonata.media.pool')->willReturn($pool->reveal());
        $this->container->getParameter('sonata.formatter.ckeditor.configuration.templates')
            ->willReturn(array('browser' => $template, 'upload' => $template));
        $templating->renderResponse($template, $data, null)->willReturn($rendered);
    }
}
