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

use Sonata\FormatterBundle\Formatter\Pool;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class TextFormatterExtension extends AbstractExtension
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * @param Pool $pool
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('format_text', [$this, 'transform']),
        ];
    }

    /**
     * @param string $text
     * @param string $type
     *
     * @return string
     */
    public function transform($text, $type)
    {
        return $this->pool->transform($type, $text);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_text_formatter';
    }
}
