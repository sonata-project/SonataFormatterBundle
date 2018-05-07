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

namespace Sonata\FormatterBundle\Formatter;

use Sonata\FormatterBundle\Extension\ExtensionInterface;

abstract class BaseFormatter implements FormatterInterface
{
    /**
     * @var ExtensionInterface[]
     */
    protected $extensions = [];

    public function addExtension(ExtensionInterface $extensionInterface): void
    {
        $this->extensions[] = $extensionInterface;
    }

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}
