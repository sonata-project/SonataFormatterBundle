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

namespace Sonata\FormatterBundle\Twig\TokenParser;

use Sonata\FormatterBundle\Twig\Node\GistNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

final class GistTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): GistNode
    {
        $gist = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->next();

        $file = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new GistNode($gist, $file, $token->getLine(), $this->getTag());
    }

    public function getTag(): string
    {
        return 'gist';
    }
}
