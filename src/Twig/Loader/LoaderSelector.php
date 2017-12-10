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

class LoaderSelector implements LoaderInterface
{
    /**
     * @var \Twig_LoaderInterface
     */
    protected $stringLoader;

    /**
     * @var \Twig_LoaderInterface
     */
    protected $fileLoader;

    /**
     * @param \Twig_LoaderInterface $stringLoader
     * @param \Twig_LoaderInterface $fileLoader
     */
    public function __construct(LoaderInterface $stringLoader, LoaderInterface $fileLoader)
    {
        $this->stringLoader = $stringLoader;
        $this->fileLoader = $fileLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        $source = $this->getLoader($name)->getSource($name);

        if ($this->isFile($name)) {
            $from = ['{#', '{{', '{%', '%}', '}}', '#}'];
            $to = ['<#', '<%=', '<%', '%>', '%>', '#>'];

            $source = str_replace($from, $to, $source);
        }

        return $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceContext($name)
    {
        return $this->getLoader($name)->getSourceContext($name);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return $this->getLoader($name)->exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        return $this->getLoader($name)->getCacheKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        return false;
    }

    /**
     * Finds out the correct loader.
     *
     * @param string $name
     *
     * @return \Twig_LoaderInterface
     */
    private function getLoader($name)
    {
        if ($this->isFile($name)) {
            return $this->fileLoader;
        }

        return $this->stringLoader;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isFile($name)
    {
        if ('.html.twig' == substr($name, -10)) {
            return true;
        }

        return false;
    }
}
