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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Sonata\AdminBundle\Admin\Pool as AdminPool;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;
use Sonata\MediaBundle\Admin\BaseMediaAdmin;
use Sonata\MediaBundle\Model\CategoryManagerInterface;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Twig\Environment;

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
    /**
     * @var Container
     */
    private $container;

    /**
     * @var BaseMediaAdmin&MockObject
     */
    private $admin;

    /**
     * @var Request&MockObject
     */
    private $request;

    /**
     * @var Environment&MockObject
     */
    private $twig;

    /**
     * @var CkeditorAdminController
     */
    private $controller;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->admin = $this->createMock(BaseMediaAdmin::class);
        $this->request = $this->createMock(Request::class);
        $this->twig = $this->createMock(Environment::class);

        $this->container->set('twig', $this->twig);

        $this->configureCRUDController();

        $this->controller = new CkeditorAdminController();
        $this->controller->setContainer($this->container);
    }

    public function testBrowserAction(): void
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $mediaPool = $this->createStub(MediaPool::class);
        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $category = $this->createStub(EntityWithGetId::class);
        $form = $this->createStub(Form::class);
        $formView = $this->createStub(FormView::class);

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('templateList', 'renderResponse');

        $datagrid->expects(static::exactly(4))->method('setValue')->withConsecutive(
            ['context', null, 'another_context'],
            ['providerName', null, 'provider'],
            ['category', null, 1]
        );
        $datagrid->method('getResults')->willReturn([]);
        $datagrid->method('getForm')->willReturn($form);
        $mediaPool->method('getDefaultContext')->willReturn('context');
        $categoryManager->method('getRootCategory')->with('another_context')->willReturn($category);
        $categoryManager->method('findOneBy')->with([
            'id' => 2,
            'context' => 'another_context',
        ])->willReturn($category);
        $category->method('getId')->willReturn(1);
        $form->method('createView')->willReturn($formView);
        $this->container->set('sonata.media.pool', $mediaPool);
        $this->container->set('sonata.media.manager.category', $categoryManager);
        $this->container->setParameter('kernel.bundles', ['SonataMediaBundle' => true]);
        $this->admin->expects(static::once())->method('checkAccess')->with('list');
        $this->admin->method('getDatagrid')->willReturn($datagrid);
        $this->admin->method('getPersistentParameter')->willReturnMap([
            ['context', 'context', 'another_context'],
            ['provider', 'provider'],
        ]);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        static::assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        static::assertSame('renderResponse', $response->getContent());
    }

    public function testUpload(): void
    {
        $media = $this->createStub(MediaInterface::class);
        $mediaManager = $this->createMock(MediaManagerInterface::class);
        $mediaPool = $this->createMock(MediaPool::class);

        $this->configureRender('templateList', 'renderResponse');
        $mediaPool->method('getDefaultContext')->willReturn('context');
        $mediaPool->method('getProvider')->with('provider')
            ->willReturn($this->createStub(MediaProviderInterface::class));
        $mediaManager->method('create')->willReturn($media);
        $mediaManager->expects(static::once())->method('save')->with($media, 'context', 'provider');
        $this->admin->expects(static::once())->method('checkAccess')->with('create');
        $this->admin->expects(static::once())->method('createObjectSecurity')->with($media);
        $this->container->set('sonata.media.manager.media', $mediaManager);
        $this->container->setParameter('kernel.bundles', ['SonataMediaBundle' => true]);
        $this->container->set('sonata.media.pool', $mediaPool);

        $response = $this->controller->uploadAction($this->request);

        static::assertInstanceOf(Response::class, $response);
        static::assertSame('renderResponse', $response->getContent());
    }

    private function configureCRUDController(): void
    {
        $adminPool = $this->createMock(AdminPool::class);

        $this->configureGetCurrentRequest();

        $adminPool->method('getAdminByAdminCode')->with('admin_code')->willReturn($this->admin);
        $this->container->set('sonata.admin.pool.do-not-use', $adminPool);
        $this->container->set(
            'sonata.admin.breadcrumbs_builder.do-not-use',
            $this->createStub(BreadcrumbsBuilderInterface::class)
        );
        $this->container->set('admin_code.template_registry', new TemplateRegistry());
        $this->admin->method('getTemplate')->with('layout')->willReturn('layout.html.twig');
        $this->admin->method('isChild')->willReturn(false);
        $this->admin->method('getCode')->willReturn('admin_code');
    }

    private function configureGetCurrentRequest(): void
    {
        $filesBag = $this->createMock(AttributeBagInterface::class);

        $filesBag->method('get')->with('upload')->willReturn(new \stdClass());

        $this->request->files = $filesBag;
        $this->request->method('isMethod')->with('POST')->willReturn(true);
        $this->request->method('isXmlHttpRequest')->with()->willReturn(false);
        $this->request->method('get')->willReturnMap([
            ['provider', null, 'provider'],
            ['context', 'context', 'context'],
            ['format', 'reference', 'reference'],
            ['_xml_http_request', null, false],
            ['_sonata_admin', null, 'admin_code'],
            ['filter', null, []],
            ['category', null, 2],
        ]);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->admin->expects(static::once())->method('setRequest')->with($this->request);
        $this->container->set('request_stack', $requestStack);
    }

    private function configureSetFormTheme($formView, array $formTheme): void
    {
        $twigRenderer = $this->createMock(FormRenderer::class);

        $this->twig->method('getRuntime')->with(FormRenderer::class)->willReturn($twigRenderer);
        $twigRenderer->expects(static::once())->method('setTheme')->with($formView, $formTheme);
    }

    private function configureRender($template, $rendered): void
    {
        $this->admin->method('getPersistentParameters')->willReturn(['param' => 'param']);
        $this->container->set('sonata.media.pool', $this->createStub(MediaPool::class));
        $this->container->setParameter(
            'sonata.formatter.ckeditor.configuration.templates',
            ['browser' => $template, 'upload' => $template]
        );

        $this->twig->method('render')->with($template, static::isType('array'))->willReturn($rendered);
    }
}
