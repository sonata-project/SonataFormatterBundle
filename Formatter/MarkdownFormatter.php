<?php

/*
 * This file is part of the Sonata project.
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
    protected $parser;

    /**
     * @param MarkdownParserInterface $parser
     */
    public function __construct(MarkdownParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param $text
     *
     * @return string
     */
    public function transform($text)
    {
        return $this->parser->transformMarkdown($text);
    }
}
