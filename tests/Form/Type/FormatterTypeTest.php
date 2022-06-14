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

namespace Sonata\FormatterBundle\Tests\Form\Type;

use FOS\CKEditorBundle\Config\CKEditorConfigurationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Sonata\FormatterBundle\Form\EventListener\FormatterListener;
use Sonata\FormatterBundle\Form\Type\FormatterType;
use Sonata\FormatterBundle\Formatter\FormatterInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Tests\App\Entity\TextEntity;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
final class FormatterTypeTest extends TypeTestCase
{
    private Pool $pool;

    /**
     * @var CKEditorConfigurationInterface&MockObject
     */
    private CKEditorConfigurationInterface $ckEditorConfiguration;

    protected function setUp(): void
    {
        $this->pool = new Pool('text');
        $this->ckEditorConfiguration = $this->createMock(CKEditorConfigurationInterface::class);

        parent::setUp();
    }

    public function testRequiredOptions(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('An error has occurred resolving the options of the form "Sonata\FormatterBundle\Form\Type\FormatterType": The required options "format_field", "source_field" are missing.');

        $this->factory->create(FormatterType::class);
    }

    public function testWithoutListener(): void
    {
        $formData = ['text' => [
            'rawText' => 'test source',
        ]];
        $model = new TextEntity();

        $form = $this->factory->createBuilder(FormType::class, $model)
            ->add('text', FormatterType::class, [
                'format_field' => 'textFormat',
                'source_field' => 'rawText',
                'listener' => false,
            ])
            ->getForm();

        static::assertTrue($form->get('text')->has('textFormat'));
        static::assertTrue($form->get('text')->has('rawText'));

        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame('test source', $model->getRawText());
    }

    public function testWithOneFormat(): void
    {
        $this->pool->add('text', $this->createStub(FormatterInterface::class));

        $formData = ['text' => [
            'rawText' => 'test source',
        ]];
        $model = new TextEntity();

        $form = $this->factory->createBuilder(FormType::class, $model)
            ->add('text', FormatterType::class, [
                'format_field' => 'textFormat',
                'source_field' => 'rawText',
                'listener' => false,
            ])
            ->getForm();

        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame('test source', $model->getRawText());
        static::assertSame('text', $model->getTextFormat());
    }

    public function testWithMultipleFormat(): void
    {
        $this->pool->add('text', $this->createStub(FormatterInterface::class));
        $this->pool->add('rawhtml', $this->createStub(FormatterInterface::class));

        $formData = ['text' => [
            'rawText' => 'test source',
            'textFormat' => 'rawhtml',
        ]];
        $model = new TextEntity();

        $form = $this->factory->createBuilder(FormType::class, $model)
            ->add('text', FormatterType::class, [
                'format_field' => 'textFormat',
                'source_field' => 'rawText',
                'listener' => false,
            ])
            ->getForm();

        static::assertCount(2, $form->get('text')->get('textFormat')->getConfig()->getOption('choices'));

        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame('test source', $model->getRawText());
        static::assertSame('rawhtml', $model->getTextFormat());
    }

    public function testWithEventListener(): void
    {
        $formBuilder = $this->factory->createBuilder(FormType::class, null)
            ->add('text', FormatterType::class, [
                'format_field' => 'textFormat',
                'source_field' => 'rawText',
                'target_field' => 'text',
            ]);

        $listeners = $formBuilder->get('text')->getEventDispatcher()->getListeners(FormEvents::SUBMIT);

        static::assertCount(1, $listeners);
        static::assertIsArray($listeners[0]);
        static::assertCount(2, $listeners[0]);
        static::assertInstanceOf(FormatterListener::class, $listeners[0][0]);
        static::assertSame('postSubmit', $listeners[0][1]);
    }

    public function testWithPropertyPaths(): void
    {
        $this->pool->add('text', $this->createStub(FormatterInterface::class));

        $formData = ['targetText' => [
            'textRaw' => 'test source',
        ]];
        $model = new TextEntity();

        $form = $this->factory->createBuilder(FormType::class, $model)
            ->add('targetText', FormatterType::class, [
                'format_field' => 'formatText',
                'format_field_options' => [
                    'property_path' => 'textFormat',
                ],
                'source_field' => 'textRaw',
                'source_field_options' => [
                    'property_path' => 'rawText',
                ],
                'target_field' => 'text',
            ])
            ->getForm();

        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame('text', $model->getTextFormat());
        static::assertSame('test source', $model->getRawText());
    }

    public function testFormView(): void
    {
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn('default');

        $this->pool->add('text', $this->createStub(FormatterInterface::class));
        $this->pool->add('rawhtml', $this->createStub(FormatterInterface::class));

        $view = $this->factory->createBuilder(FormType::class, null)
            ->add('text', FormatterType::class, [
                'format_field' => 'textFormat',
                'source_field' => 'rawText',
                'listener' => false,
            ])
            ->getForm()
            ->createView();

        static::assertSame('rawText', $view->children['text']->vars['source_field']);
        static::assertSame('textFormat', $view->children['text']->vars['format_field']);
        static::assertIsArray($view->children['text']->vars['ckeditor_configuration']);
        static::assertSame([], $view->children['text']->vars['ckeditor_plugins']);
        static::assertSame([], $view->children['text']->vars['ckeditor_templates']);
        static::assertSame([], $view->children['text']->vars['ckeditor_style_sets']);
    }

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                new FormatterType($this->pool, $this->ckEditorConfiguration),
            ], []),
        ];
    }
}
