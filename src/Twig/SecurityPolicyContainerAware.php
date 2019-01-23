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

namespace Sonata\FormatterBundle\Twig;

use Sonata\FormatterBundle\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityPolicyInterface;

/**
 * @throws SecurityError
 *
 * Some code are adapted work from the Twig\Sandbox\SecurityPolicy class
 *
 * @author fabien.potencier@symfony.com
 * @author thomas.rabaix@sonata-project.org
 */
final class SecurityPolicyContainerAware implements SecurityPolicyInterface
{
    /**
     * @var string[]
     */
    private $allowedTags;

    /**
     * @var string[]
     */
    private $allowedFilters;

    /**
     * @var string[]
     */
    private $allowedFunctions;

    /**
     * @var string[]
     */
    private $allowedProperties;

    /**
     * @var string[]
     */
    private $allowedMethods;

    /**
     * @var ExtensionInterface[]
     */
    private $extensions = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, array $extensions = [])
    {
        $this->container = $container;
        $this->extensions = $extensions;
    }

    public function checkSecurity($tags, $filters, $functions): void
    {
        $this->buildAllowed();

        foreach ($tags as $tag) {
            if (!\in_array($tag, $this->allowedTags, true)) {
                throw new SecurityError(sprintf('Tag "%s" is not allowed.', $tag));
            }
        }

        foreach ($filters as $filter) {
            if (!\in_array($filter, $this->allowedFilters, true)) {
                throw new SecurityError(sprintf('Filter "%s" is not allowed.', $filter));
            }
        }

        foreach ($functions as $function) {
            if (!\in_array($function, $this->allowedFunctions, true)) {
                throw new SecurityError(sprintf('Function "%s" is not allowed.', $function));
            }
        }
    }

    public function checkMethodAllowed($obj, $method): bool
    {
        $this->buildAllowed();

        if ($obj instanceof Markup) {
            return true;
        }

        $allowed = false;
        $method = strtolower($method);
        foreach ($this->allowedMethods as $class => $methods) {
            if ($obj instanceof $class) {
                $allowed = \in_array($method, $methods, true);

                break;
            }
        }

        if (!$allowed) {
            throw new SecurityError(
                sprintf(
                    'Calling "%s" method on a "%s" object is not allowed.',
                    $method,
                    \get_class($obj)
                )
            );
        }
    }

    public function checkPropertyAllowed($obj, $property): void
    {
        $this->buildAllowed();

        $allowed = false;
        foreach ($this->allowedProperties as $class => $properties) {
            if ($obj instanceof $class) {
                $allowed = \in_array($property, \is_array($properties) ? $properties : [$properties], true);

                break;
            }
        }

        if (!$allowed) {
            throw new SecurityError(
                sprintf(
                    'Calling "%s" property on a "%s" object is not allowed.',
                    $property,
                    \get_class($obj)
                )
            );
        }
    }

    private function buildAllowed(): void
    {
        if (null !== $this->allowedTags) {
            return;
        }

        $this->allowedTags = [];
        $this->allowedFilters = [];
        $this->allowedFunctions = [];
        $this->allowedMethods = [];
        $this->allowedProperties = [];

        foreach ($this->extensions as $id) {
            $extension = $this->container->get($id);

            $this->allowedTags = array_merge($this->allowedTags, $extension->getAllowedTags());
            $this->allowedFilters = array_merge($this->allowedFilters, $extension->getAllowedFilters());
            $this->allowedFunctions = array_merge($this->allowedFunctions, $extension->getAllowedFunctions());

            $this->allowedProperties = array_merge_recursive(
                $this->allowedProperties,
                $extension->getAllowedProperties()
            );

            $this->allowedMethods = array_merge_recursive($this->allowedMethods, $extension->getAllowedMethods());
        }
    }
}
