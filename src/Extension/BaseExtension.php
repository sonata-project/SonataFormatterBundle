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

namespace Sonata\FormatterBundle\Extension;

use Twig\Extension\AbstractExtension;

abstract class BaseExtension extends AbstractExtension implements ExtensionInterface
{
    public function getAllowedFilters(): array
    {
        return [];
    }

    public function getAllowedTags(): array
    {
        return [];
    }

    public function getAllowedFunctions(): array
    {
        return [];
    }

    public function getAllowedProperties(): array
    {
        return [];
    }

    public function getAllowedMethods(): array
    {
        return [];
    }

    public static function getAllowedRuntimes(): array
    {
        return [];
    }
}
