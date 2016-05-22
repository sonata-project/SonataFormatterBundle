<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Twig\Loader;

class LoaderSelector implements \Twig_LoaderInterface
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
    public function __construct(\Twig_LoaderInterface $stringLoader, \Twig_LoaderInterface $fileLoader)
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
            $from = array('{#', '{{', '{%', '%}', '}}', '#}');
            $to = array('<#', '<%=', '<%', '%>', '%>', '#>');

            $source = str_replace($from, $to, $source);
        }

        return $source;
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
        if (substr($name, -10) == '.html.twig') {
            return true;
        }

        return false;
    }
}
