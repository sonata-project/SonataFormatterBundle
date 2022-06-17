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
use Twig\Extension\ExtensionInterface;

abstract class BaseProxyExtension extends AbstractExtension implements ExtensionInterface
{
    abstract public function getTwigExtension(): ExtensionInterface;

    /**
     * @return string[]
     */
    public function getAllowedFilters(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAllowedTags(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAllowedFunctions(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAllowedProperties(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return [];
    }

    public function getTokenParsers(): array
    {
        return $this->getTwigExtension()->getTokenParsers();
    }

    public function getNodeVisitors(): array
    {
        return $this->getTwigExtension()->getNodeVisitors();
    }

    public function getFilters(): array
    {
        return $this->getTwigExtension()->getFilters();
    }

    public function getTests(): array
    {
        return $this->getTwigExtension()->getTests();
    }

    public function getFunctions(): array
    {
        return $this->getTwigExtension()->getFunctions();
    }

    /**
     * @return array{0?: array<string, array{precedence: int, class: class-string}>, 1?: array<string, array{precedence: int, class: class-string, associativity: int}>}
     */
    public function getOperators(): array
    {
        return $this->getTwigExtension()->getOperators();
    }
}
