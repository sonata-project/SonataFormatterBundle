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

namespace Sonata\FormatterBundle\Tests\Twig;

use Phpunit\Framework\TestCase;
use Sonata\FormatterBundle\Twig\SecurityPolicyContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class SecurityPolicyContainerAwareTest extends TestCase
{
    public function testItCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            SecurityPolicyContainerAware::class,
            new SecurityPolicyContainerAware($this->createMock(ContainerInterface::class))
        );
    }
}
