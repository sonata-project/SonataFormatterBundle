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

namespace Sonata\FormatterBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;
use Sonata\MediaBundle\Admin\BaseMediaAdmin;
use Sonata\MediaBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

class EntityWithGetId
{
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}

class CkeditorAdminControllerTest extends TestCase
{
    private $container;
    private $admin;
    private $request;
    private $controller;

    protected function setUp(): void
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->admin = $this->prophesize(BaseMediaAdmin::class);
        $this->request = $this->prophesize(Request::class);

        $this->configureCRUDController();

        $this->controller = new CkeditorAdminController();
        $this->controller->setContainer($this->container->reveal());
    }

    public function testBrowserAction(): void
    {
        $datagrid = $this->prophesize(DatagridInterface::class);
        $pool = $this->prophesize(MediaPool::class);
        $categoryManager = $this->prophesize(CategoryManagerInterface::class);
        $category = $this->prophesize(EntityWithGetId::class);
        $form = $this->prophesize(Form::class);
        $formView = $this->prophesize(FormView::class);

        $this->configureSetFormTheme($formView->reveal(), 'filterTheme');
        $this->configureRender('templateList', Argument::type('array'), 'renderResponse');
        $datagrid->setValue('context', null, 'another_context')->shouldBeCalled();
        $datagrid->setValue('category', null, 1)->shouldBeCalled();
        $datagrid->setValue('providerName', null, 'provider')->shouldBeCalled();
        $datagrid->getResults()->willReturn([]);
        $datagrid->getForm()->willReturn($form->reveal());
        $pool->getDefaultContext()->willReturn('context');
        $categoryManager->getRootCategory('another_context')->willReturn($category->reveal());
        $categoryManager->findOneBy([
            'id' => 2,
            'context' => 'another_context',
        ])->willReturn($category->reveal());
        $category->getId()->willReturn(1);
        $form->createView()->willReturn($formView->reveal());
        $this->container->get('sonata.media.pool')->willReturn($pool->reveal());
        $this->container->has('sonata.media.manager.category')->willReturn(true);
        $this->container->get('sonata.media.manager.category')->willReturn($categoryManager->reveal());
        $this->container->getParameter('kernel.bundles')->willReturn(['SonataMediaBundle' => true]);
        $this->admin->checkAccess('list')->shouldBeCalled();
        $this->admin->getDatagrid()->willReturn($datagrid->reveal());
        $this->admin->getPersistentParameter('context', 'context')->willReturn('another_context');
        $this->admin->getPersistentParameter('provider')->willReturn('provider');
        $this->admin->getFilterTheme()->willReturn('filterTheme');
        $this->request->get('filter')->willReturn([]);
        $this->request->get('category')->willReturn(2);

        $response = $this->controller->browserAction($this->request->reveal());

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame('renderResponse', $response->getContent());
    }

    public function testUpload(): void
    {
        $media = $this->prophesize(MediaInterface::class);
        $mediaManager = $this->prophesize(MediaManagerInterface::class);
        $filesBag = $this->prophesize(AttributeBagInterface::class);
        $pool = $this->prophesize(MediaPool::class);
        $provider = $this->prophesize(MediaProviderInterface::class);

        $this->configureRender('templateList', Argument::type('array'), 'renderResponse');
        $pool->getDefaultContext()->willReturn('context');
        $pool->getProvider('provider')->willReturn($provider->reveal());
        $filesBag->get('upload')->willReturn(new \stdClass());
        $mediaManager->create()->willReturn($media->reveal());
        $mediaManager->save($media->reveal(), 'context', 'provider')->shouldBeCalled();
        $this->admin->checkAccess('create')->shouldBeCalled();
        $this->admin->createObjectSecurity($media->reveal())->shouldBeCalled();
        $this->container->get('sonata.media.manager.media')->willReturn($mediaManager->reveal());
        $this->container->getParameter('kernel.bundles')->willReturn(['SonataMediaBundle' => true]);
        $this->container->get('sonata.media.pool')->willReturn($pool->reveal());
        $this->request->get('provider')->willReturn('provider');
        $this->request->isMethod('POST')->willReturn(true);
        $this->request->files = $filesBag->reveal();
        $this->request->get('context', 'context')->willReturn('context');
        $this->request->get('format', 'reference')->willReturn('reference');

        $response = $this->controller->uploadAction($this->request->reveal());

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('renderResponse', $response->getContent());
    }

    private function configureCRUDController(): void
    {
        $pool = $this->prophesize(AdminPool::class);
        $breadcrumbsBuilder = $this->prophesize(BreadcrumbsBuilderInterface::class);

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

        if (interface_exists(TemplateRegistryInterface::class)) {
            $this->container->get('admin_code.template_registry')->willReturn(new TemplateRegistry());
            $this->admin->getCode()->willReturn('admin_code');
        }
    }

    private function configureGetCurrentRequest($request): void
    {
        $requestStack = $this->prophesize(RequestStack::class);

        $this->container->has('request_stack')->willReturn(true);
        $this->container->get('request_stack')->willReturn($requestStack->reveal());
        $requestStack->getCurrentRequest()->willReturn($request);
    }

    private function configureSetFormTheme($formView, $formTheme): void
    {
        $twig = $this->prophesize(\Twig_Environment::class);

        $twigRenderer = $this->prophesize(FormRenderer::class);

        $this->container->get('twig')->willReturn($twig->reveal());

        $twig->getRuntime(FormRenderer::class)->willReturn($twigRenderer->reveal());

        $twigRenderer->setTheme($formView, $formTheme)->shouldBeCalled();
    }

    private function configureRender($template, $data, $rendered): void
    {
        $templating = $this->prophesize(EngineInterface::class);
        $response = $this->prophesize(Response::class);
        $pool = $this->prophesize(MediaPool::class);

        $this->admin->getPersistentParameters()->willReturn(['param' => 'param']);
        $this->container->has('templating')->willReturn(true);
        $this->container->get('templating')->willReturn($templating->reveal());
        $this->container->get('sonata.media.pool')->willReturn($pool->reveal());
        $this->container->getParameter('sonata.formatter.ckeditor.configuration.templates')
            ->willReturn(['browser' => $template, 'upload' => $template]);
        $response->getContent()->willReturn($rendered);
        $templating->renderResponse($template, $data, null)->willReturn($response->reveal());
        $templating->render($template, $data)->willReturn($rendered);
    }
}
