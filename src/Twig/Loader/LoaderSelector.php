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

namespace Sonata\FormatterBundle\Twig\Loader;

use Twig\Loader\LoaderInterface;
use Twig\Source;

final class LoaderSelector implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $stringLoader;

    /**
     * @var LoaderInterface
     */
    private $fileLoader;

    public function __construct(LoaderInterface $stringLoader, LoaderInterface $fileLoader)
    {
        $this->stringLoader = $stringLoader;
        $this->fileLoader = $fileLoader;
    }

    public function getSource(string $name): string
    {
        $source = $this->getLoader($name)->getSource($name);

        if ($this->isFile($name)) {
            $from = ['{#', '{{', '{%', '%}', '}}', '#}'];
            $to = ['<#', '<%=', '<%', '%>', '%>', '#>'];

            $source = str_replace($from, $to, $source);
        }

        return $source;
    }

    public function getSourceContext($name): Source
    {
        return $this->getLoader($name)->getSourceContext($name);
    }

    public function exists($name): bool
    {
        return $this->getLoader($name)->exists($name);
    }

    public function getCacheKey($name): string
    {
        return $this->getLoader($name)->getCacheKey($name);
    }

    public function isFresh($name, $time): bool
    {
        return false;
    }

    private function getLoader(string $name): LoaderInterface
    {
        if ($this->isFile($name)) {
            return $this->fileLoader;
        }

        return $this->stringLoader;
    }

    private function isFile(string $name): bool
    {
        if ('.html.twig' === substr($name, -10)) {
            return true;
        }

        return false;
    }
}
