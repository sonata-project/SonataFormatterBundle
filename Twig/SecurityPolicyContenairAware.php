<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use \Twig_Sandbox_SecurityError;

class SecurityPolicyContenairAware implements \Twig_Sandbox_SecurityPolicyInterface
{
    protected $allowedTags;

    protected $allowedFilters;

    protected $allowedFunctions;

    protected $extensions = array();

    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $extensions
     */
    public function __construct(ContainerInterface $container, array $extensions = array())
    {
        $this->container  = $container;
        $this->extensions = $extensions;
    }

    /**
     * @param $tags
     * @param $filters
     * @param $functions
     * @return void
     */
    function checkSecurity($tags, $filters, $functions)
    {
        $this->buildAllowed();

        foreach ($tags as $tag) {
            if (!in_array($tag, $this->allowedTags)) {
                throw new Twig_Sandbox_SecurityError(sprintf('Tag "%s" is not allowed.', $tag));
            }
        }

        foreach ($filters as $filter) {
            if (!in_array($filter, $this->allowedFilters)) {
                throw new Twig_Sandbox_SecurityError(sprintf('Filter "%s" is not allowed.', $filter));
            }
        }

        foreach ($functions as $function) {
            if (!in_array($function, $this->allowedFunctions)) {
                throw new Twig_Sandbox_SecurityError(sprintf('Function "%s" is not allowed.', $function));
            }
        }
    }

    private function buildAllowed()
    {
        if ($this->allowedTags !== null) {
            return;
        }

        $this->allowedTags = array();
        $this->allowedFilters = array();
        $this->allowedFunctions = array();

        foreach($this->extensions as $id) {
            $extension = $this->container->get($id);

            $this->allowedTags = array_merge($this->allowedTags, $extension->getAllowedTags());
            $this->allowedFilters = array_merge($this->allowedFilters, $extension->getAllowedFilters());
            $this->allowedFunctions = array_merge($this->allowedFunctions, $extension->getAllowedFunctions());
        }
    }

    /**
     * @param $obj
     * @param $method
     * @return bool
     */
    function checkMethodAllowed($obj, $method)
    {
        return false;
    }

    /**
     * @param $obj
     * @param $method
     * @return bool
     */
    function checkPropertyAllowed($obj, $method)
    {
        return false;
    }
}