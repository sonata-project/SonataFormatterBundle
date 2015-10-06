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

class GistNode extends \Twig_Node
{
    public function __construct(\Twig_Node_Expression $gist, \Twig_Node_Expression $file, $lineno, $tag = null)
    {
        parent::__construct(array('gist' => $gist, 'file' => $file), array(), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler A Twig_Compiler instance
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler
            ->write(sprintf("echo '<div class=\"sonata-gist\"><script src=\"https://gist.github.com/%s.js?file=%s\"></script></div>';\n",
                $this->getNode('gist')->getAttribute('value'),
                $this->getNode('file')->getAttribute('value')
            ))
        ;
    }
}
