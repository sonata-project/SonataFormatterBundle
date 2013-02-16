<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Twig\Loader;

class LoaderSelector implements \Twig_LoaderInterface
{
    protected $stringLoader;

    protected $fileLoader;

    /**
     * @param  \Twig_LoaderInterface $stringLoader
     * @param  \Twig_LoaderInterface $fileLoader
     * @return void
     */
    public function __construct(\Twig_LoaderInterface $stringLoader, \Twig_LoaderInterface $fileLoader)
    {
        $this->stringLoader = $stringLoader;
        $this->fileLoader = $fileLoader;
    }

    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
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
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     */
    public function getCacheKey($name)
    {
        return $this->getLoader($name)->getCacheKey($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     */
    public function isFresh($name, $time)
    {
        return false;
    }

    /**
     * Finds out the correct loader
     *
     * @param $name
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
     * @param $name
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
