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
    private LoaderInterface $stringLoader;

    private LoaderInterface $fileLoader;

    public function __construct(LoaderInterface $stringLoader, LoaderInterface $fileLoader)
    {
        $this->stringLoader = $stringLoader;
        $this->fileLoader = $fileLoader;
    }

    /**
     * @param string $name
     */
    public function getSourceContext($name): Source
    {
        $source = $this->getLoader($name)->getSourceContext($name);

        if ($this->isFile($name)) {
            $from = ['{#', '{{', '{%', '%}', '}}', '#}'];
            $to = ['<#', '<%=', '<%', '%>', '%>', '#>'];

            return new Source(
                str_replace($from, $to, $source->getCode()),
                $source->getName(),
                $source->getPath()
            );
        }

        return $source;
    }

    /**
     * @param string $name
     */
    public function exists($name): bool
    {
        return $this->getLoader($name)->exists($name);
    }

    /**
     * @param string $name
     */
    public function getCacheKey($name): string
    {
        return $this->getLoader($name)->getCacheKey($name);
    }

    /**
     * @param string $name
     * @param int    $time
     */
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
