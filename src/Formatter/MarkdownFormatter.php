<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Formatter;

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownFormatter extends BaseFormatter
{
    /**
     * @var MarkdownParserInterface
     */
    protected $parser;

    /**
     * @param MarkdownParserInterface $parser
     */
    public function __construct(MarkdownParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($text)
    {
        return $this->parser->transformMarkdown($text);
    }
}
