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
use PHPUnit\Framework\TestCase;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleFormatterTypeTest extends TestCase
{
    private SimpleFormatterType $formType;

    /**
     * @var CKEditorConfigurationInterface&MockObject
     */
    private CKEditorConfigurationInterface $ckEditorConfiguration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ckEditorConfiguration = $this->createMock(CKEditorConfigurationInterface::class);
        $this->formType = new SimpleFormatterType($this->ckEditorConfiguration);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testBuildForm(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);

        $options = ['format' => 'format'];

        $this->formType->buildForm($formBuilder, $options);
    }
}
