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
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Request\AdminFetcherInterface;
use Sonata\AdminBundle\Templating\MutableTemplateRegistryInterface;
use Sonata\ClassificationBundle\Model\CategoryManagerInterface;
use Sonata\ClassificationBundle\Model\ContextManagerInterface;
use Sonata\FormatterBundle\Controller\CkeditorAdminController;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Model\MediaManagerInterface;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Provider\Pool as MediaPool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class EntityWithGetId
{
    /**
     * @var string|int|null
     */
    private $id;

    /**
     * @param int|string $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|string|null
     */
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
     * @var ParameterBag
     */
    private $parameterBag;

    /**
     * @var AdminInterface<object>&MockObject
     */
    private $admin;

    /**
     * @var Request
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

    /**
     * @var MutableTemplateRegistryInterface&MockObject
     */
    private MutableTemplateRegistryInterface $templateRegistry;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->request = new Request();
        $this->admin = $this->createMock(AdminInterface::class);
        $this->twig = $this->createMock(Environment::class);
        $this->templateRegistry = $this->createMock(MutableTemplateRegistryInterface::class);

        $this->admin->method('hasTemplateRegistry')->willReturn(true);
        $this->admin->method('getTemplateRegistry')->willReturn($this->templateRegistry);

        $this->parameterBag = new ParameterBag([
            'kernel.bundles' => [
                'SonataMediaBundle' => true,
            ],
            'sonata.formatter.ckeditor.configuration.templates' => [
                'browser' => 'browser.html.twig',
                'upload' => 'upload.html.twig',
            ],
        ]);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->container->set('request_stack', $requestStack);

        $adminFetcher = $this->createStub(AdminFetcherInterface::class);
        $adminFetcher->method('get')->willReturn($this->admin);

        $categoryManagerInterface = $this->createStub(CategoryManagerInterface::class);
        $contextManagerInterface = $this->createStub(ContextManagerInterface::class);

        $this->container->set('sonata.admin.request.fetcher', $adminFetcher);
        $this->container->set('sonata.media.manager.category', $categoryManagerInterface);
        $this->container->set('sonata.media.manager.context', $contextManagerInterface);
        $this->container->set('twig', $this->twig);
        $this->container->set('admin_code', $this->admin);
        $this->container->set('parameter_bag', $this->parameterBag);

        $this->admin->method('isChild')->willReturn(false);
        $this->admin->method('getCode')->willReturn('admin_code');

        $this->controller = new CkeditorAdminController();
        $this->controller->setContainer($this->container);
        $this->controller->configureAdmin($this->request);
    }

    public function testBrowserAction(): void
    {
        $this->request->query->set('provider', 'provider');
        $this->request->query->set('category', 2);
        $this->request->query->set('filter', []);
        $this->request->query->set('context', 'context');

        $datagrid = $this->createMock(DatagridInterface::class);
        $mediaPool = new MediaPool('context');
        $categoryManager = $this->createMock(CategoryManagerInterface::class);
        $category = $this->createStub(EntityWithGetId::class);
        $form = $this->createStub(Form::class);
        $formView = $this->createStub(FormView::class);

        $this->configureSetFormTheme($formView, ['filterTheme']);
        $this->configureRender('browser.html.twig', 'renderResponse');

        $datagrid->expects(static::exactly(4))->method('setValue')->withConsecutive(
            ['context', null, 'another_context'],
            ['providerName', null, 'provider'],
            ['category', null, 1]
        );
        $datagrid->method('getResults')->willReturn([]);
        $datagrid->method('getForm')->willReturn($form);
        $categoryManager->method('getRootCategoriesForContext')->willReturn([$category]);
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
            ['provider', null, 'provider'],
        ]);
        $this->admin->method('getFilterTheme')->willReturn(['filterTheme']);

        $response = $this->controller->browserAction($this->request);

        static::assertInstanceOf(Response::class, $response);
        static::assertSame('renderResponse', $response->getContent());
    }

    public function testUpload(): void
    {
        $filesBag = $this->createMock(FileBag::class);

        $filesBag->method('get')->with('upload')->willReturn(new \stdClass());

        $this->request->files = $filesBag;
        $this->request->request->set('provider', 'provider');
        $this->request->request->set('context', 'context');
        $this->request->request->set('format', 'reference');

        $media = $this->createStub(MediaInterface::class);
        $mediaManager = $this->createMock(MediaManagerInterface::class);
        $mediaPool = new MediaPool('context');
        $mediaPool->addProvider('provider', $this->createStub(MediaProviderInterface::class));

        $this->configureRender('upload.html.twig', 'renderResponse');
        $mediaManager->method('create')->willReturn($media);
        $mediaManager->expects(static::once())->method('save')->with($media);
        $this->admin->expects(static::once())->method('checkAccess')->with('create');
        $this->admin->expects(static::once())->method('createObjectSecurity')->with($media);
        $this->container->set('sonata.media.manager.media', $mediaManager);
        $this->container->setParameter('kernel.bundles', ['SonataMediaBundle' => true]);
        $this->container->set('sonata.media.pool', $mediaPool);

        $response = $this->controller->uploadAction($this->request);

        static::assertInstanceOf(Response::class, $response);
        static::assertSame('renderResponse', $response->getContent());
    }

    /**
     * @param string[] $formTheme
     */
    private function configureSetFormTheme(FormView $formView, array $formTheme): void
    {
        $twigRenderer = $this->createMock(FormRenderer::class);

        $this->twig->method('getRuntime')->with(FormRenderer::class)->willReturn($twigRenderer);
        $twigRenderer->expects(static::once())->method('setTheme')->with($formView, $formTheme);
    }

    private function configureRender(string $template, string $rendered): void
    {
        $this->admin->method('getPersistentParameters')->willReturn(['param' => 'param']);
        $this->container->set('sonata.media.pool', new MediaPool('context'));
        $this->container->setParameter(
            'sonata.formatter.ckeditor.configuration.templates',
            ['browser' => $template, 'upload' => $template]
        );

        $this->twig->method('render')->with($template, static::isType('array'))->willReturn($rendered);
    }
}
