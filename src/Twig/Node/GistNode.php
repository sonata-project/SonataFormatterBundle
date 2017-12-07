<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

class GistNode extends Node
{
    /**
     * @param \Twig_Node_Expression $gist
     * @param \Twig_Node_Expression $file
     * @param int                   $lineno
     * @param string|null           $tag
     */
    public function __construct(AbstractExpression $gist, AbstractExpression $file, $lineno, $tag = null)
    {
        parent::__construct(['gist' => $gist, 'file' => $file], [], $lineno, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Compiler $compiler)
    {
        $compiler
            ->write(sprintf("echo '<div class=\"sonata-gist\"><script src=\"https://gist.github.com/%s.js?file=%s\"></script></div>';\n",
                $this->getNode('gist')->getAttribute('value'),
                $this->getNode('file')->getAttribute('value')
            ))
        ;
    }
}
