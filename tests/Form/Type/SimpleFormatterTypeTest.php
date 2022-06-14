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
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

final class SimpleFormatterTypeTest extends TypeTestCase
{
    /**
     * @var CKEditorConfigurationInterface&MockObject
     */
    private CKEditorConfigurationInterface $ckEditorConfiguration;

    protected function setUp(): void
    {
        $this->ckEditorConfiguration = $this->createMock(CKEditorConfigurationInterface::class);

        parent::setUp();
    }

    public function testRequiredOptions(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('An error has occurred resolving the options of the form "Sonata\FormatterBundle\Form\Type\SimpleFormatterType": The required option "format" is missing.');

        $this->factory->create(SimpleFormatterType::class);
    }

    public function testSubmitForm(): void
    {
        $formData = 'data for ckeditor';

        $form = $this->factory->create(SimpleFormatterType::class, null, [
            'format' => 'default',
        ]);

        $form->submit($formData);

        static::assertTrue($form->isSynchronized());
        static::assertSame($formData, $form->getData());
    }

    public function testFormView(): void
    {
        $this->ckEditorConfiguration->expects(static::once())->method('getDefaultConfig')->willReturn('default');

        $view = $this->factory->create(SimpleFormatterType::class, null, [
            'format' => 'default',
        ])->createView();

        static::assertIsArray($view->vars['ckeditor_configuration']);
        static::assertSame([], $view->vars['ckeditor_plugins']);
        static::assertSame([], $view->vars['ckeditor_templates']);
        static::assertSame([], $view->vars['ckeditor_style_sets']);
    }

    /**
     * @return FormExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new PreloadedExtension([
                new SimpleFormatterType($this->ckEditorConfiguration),
            ], []),
        ];
    }
}
