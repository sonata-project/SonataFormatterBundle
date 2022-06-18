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

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Twig\MediaRuntime;
use Twig\TwigFunction;

final class MediaExtension extends BaseExtension
{
    public function getAllowedMethods(): array
    {
        return [
            MediaInterface::class => [
                'getProviderReference',
            ],
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sonata_media', [MediaRuntime::class, 'media'], ['is_safe' => ['html']]),
            new TwigFunction('sonata_thumbnail', [MediaRuntime::class, 'thumbnail'], ['is_safe' => ['html']]),
            new TwigFunction('sonata_path', [MediaRuntime::class, 'path']),
        ];
    }

    public function getAllowedFunctions(): array
    {
        return [
            'sonata_media',
            'sonata_thumbnail',
            'sonata_path',
        ];
    }

    public static function getAllowedRuntimes(): array
    {
        return ['sonata.media.twig.runtime' => MediaRuntime::class];
    }
}
