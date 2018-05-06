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

use Sonata\FormatterBundle\Twig\TokenParser\GistTokenParser;

final class GistExtension extends BaseExtension
{
    public function getAllowedFilters(): array
    {
        return [];
    }

    public function getAllowedTags(): array
    {
        return [
            'gist',
        ];
    }

    public function getAllowedFunctions(): array
    {
        return [];
    }

    public function getTokenParsers(): array
    {
        return [
            new GistTokenParser(),
        ];
    }

    public function getName(): string
    {
        return 'sonata_formatter_extension_gist';
    }
}
