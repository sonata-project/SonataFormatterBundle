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

namespace Sonata\FormatterBundle\Twig\Extension;

use Sonata\FormatterBundle\Formatter\PoolInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class TextFormatterExtension extends AbstractExtension
{
    /**
     * @var PoolInterface
     */
    protected $pool;

    public function __construct(PoolInterface $pool)
    {
        $this->pool = $pool;
    }

    public function getTokenParsers(): array
    {
        return [
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('format_text', [$this, 'transform']),
        ];
    }

    public function transform(string $text, string $type): string
    {
        return $this->pool->transform($type, $text);
    }

    public function getName(): string
    {
        return 'sonata_text_formatter';
    }
}
