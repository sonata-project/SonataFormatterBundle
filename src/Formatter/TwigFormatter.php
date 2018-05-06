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

namespace Sonata\FormatterBundle\Formatter;

use Sonata\FormatterBundle\Extension\ExtensionInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;

final class TwigFormatter implements FormatterInterface
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function transform(string $text): string
    {
        // Here we temporary changing twig environment loader to Chain loader with Twig_Loader_Array as first loader,
        // which contains only one our template reference
        $oldLoader = $this->twig->getLoader();

        $hash = sha1($text);

        $chainLoader = new ChainLoader();
        $chainLoader->addLoader(new ArrayLoader([$hash => $text]));
        $chainLoader->addLoader($oldLoader);

        $this->twig->setLoader($chainLoader);

        $result = $this->twig->render($hash);

        $this->twig->setLoader($oldLoader);

        return $result;
    }

    public function addExtension(ExtensionInterface $extensionInterface): void
    {
        throw new \RuntimeException('\\Sonata\\FormatterBundle\\Formatter\\TwigFormatter cannot have extensions');
    }

    public function getExtensions(): array
    {
        return [];
    }
}
