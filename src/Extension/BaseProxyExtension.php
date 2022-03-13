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

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Extension\GlobalsInterface;
use Twig\Extension\InitRuntimeInterface;

abstract class BaseProxyExtension extends AbstractExtension implements ExtensionInterface
{
    abstract public function getTwigExtension(): ExtensionInterface;

    public function getAllowedFilters(): array
    {
        return [];
    }

    public function getAllowedTags(): array
    {
        return [];
    }

    public function getAllowedFunctions(): array
    {
        return [];
    }

    public function getAllowedProperties(): array
    {
        return [];
    }

    public function getAllowedMethods(): array
    {
        return [];
    }

    /**
     * @psalm-suppress UndefinedClass, UndefinedInterfaceMethod
     */
    public function initRuntime(Environment $environment): void
    {
        $extension = $this->getTwigExtension();

        if ($extension instanceof InitRuntimeInterface) {
            $extension->initRuntime($environment);
        }
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

    public function getOperators(): array
    {
        return $this->getTwigExtension()->getOperators();
    }

    public function getGlobals(): array
    {
        $extension = $this->getTwigExtension();

        if ($extension instanceof GlobalsInterface) {
            return $extension->getGlobals();
        }

        return [];
    }
}
