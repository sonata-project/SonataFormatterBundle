<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Form\Widget;

use Sonata\FormatterBundle\Form\Type\FormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Tests\Fixtures\TestExtension;

class FormatterTypeWidgetTest extends BaseWidgetTest
{
    public function testFormatterTypedIsRendering()
    {
        $form = $this->factory->create(
            'sonata_formatter_type',
            null,
            $this->getDefaultOption()
        );

        $html = $this->cleanHtmlWhitespace($this->renderWidget($form->createView()));
        $html = $this->cleanHtmlAttributeWhitespace($html);

        $this->assertContains(
            '<option value="foo">[trans]foo[/trans]</option>',
            $html
        );

        $this->assertContains(
            '<option value="bar">[trans]bar[/trans]</option>',
            $html
        );
    }

    protected function getExtensions()
    {
        $pool = $this->getMockBuilder('Sonata\FormatterBundle\Formatter\Pool')
                ->disableOriginalConstructor()
                ->getMock();

        $formatters = array(
            'foo' => 'foo',
            'bar' => 'bar',
        );

        $pool->expects($this->once())
            ->method('getFormatters')
            ->will($this->returnValue($formatters));

        $translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');

        $translator->expects($this->exactly(2))
            ->method('trans')
            ->will($this->returnCallback(function ($arg) { return $arg;})
            );

        $configManager = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');

        $formatType = new FormatterType($pool, $translator, $configManager);

        $extensions = parent::getExtensions();
        $extensions[] = $this->registerExtension($formatType, 'sonata_formatter_type');
        $extensions[] = $this->registerExtension(new ChoiceType(), 'choice');
        $extensions[] = $this->registerExtension(new TextareaType(), 'textarea');

        return $extensions;
    }

    protected function registerExtension($objectType, $name)
    {
        $guesser = $this->getMock('Symfony\Component\Form\FormTypeGuesserInterface');

        $extension = new TestExtension($guesser);
        $extension->addType($objectType);

        if (!$extension->hasType($name)) {
            $reflection = new \ReflectionClass($extension);
            $property = $reflection->getProperty('types');
            $property->setAccessible(true);
            $property->setValue($extension, array($name => current($property->getValue($extension))));
        }

        return $extension;
    }

    protected function getDefaultOption()
    {
        return array(
            'format_field'     => 'foo',
            'source_field'     => 'source',
            'target_field'     => 'target',
            'event_dispatcher' => $this->dispatcher,
            'inherit_data'     => false,
        );
    }
}
