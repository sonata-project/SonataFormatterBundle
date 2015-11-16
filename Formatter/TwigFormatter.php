<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Formatter;

use Sonata\FormatterBundle\Extension\ExtensionInterface;

class TwigFormatter implements \Sonata\FormatterBundle\Formatter\FormatterInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($text)
    {
        // Here we temporary changing twig environment loader to Chain loader with Twig_Loader_Array as first loader, which contains only one our template reference
        $oldLoader = $this->twig->getLoader();

        $hash = sha1($text);

        $chainLoader = new \Twig_Loader_Chain();
        $chainLoader->addLoader(new \Twig_Loader_Array(array($hash => $text)));
        $chainLoader->addLoader($oldLoader);

        $this->twig->setLoader($chainLoader);

        $result = $this->twig->render($hash);

        $this->twig->setLoader($oldLoader);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extensionInterface)
    {
        throw new \RuntimeException('\\Sonata\\FormatterBundle\\Formatter\\TwigFormatter cannot have extensions');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensions()
    {
        return array();
    }
}
