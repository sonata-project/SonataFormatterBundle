<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Form\Widget;

use Ivory\CKEditorBundle\Twig\CKEditorExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Tests\Extension\Fixtures\StubFilesystemLoader;
use Symfony\Bundle\FrameworkBundle\Tests\Templating\Helper\Fixtures\StubTranslator;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class BaseWidgetTest.
 */
abstract class BaseWidgetTest extends TypeTestCase
{
    /**
     * @var FormExtension
     */
    protected $extension;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $rendererEngine = new TwigRendererEngine(array(
            'formatter.html.twig',
            'form_div_layout.html.twig',
        ));

        if (class_exists('Symfony\Component\Form\Extension\Core\Type\RangeType')) {
            $csrfManagerClass = 'Symfony\Component\Security\Csrf\CsrfTokenManagerInterface';
        } else {
            $csrfManagerClass = 'Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface';
        }

        $renderer = new TwigRenderer($rendererEngine, $this->getMock($csrfManagerClass));

        $this->extension = new FormExtension($renderer);

        $twigPaths = array(__DIR__.'/../../../Resources/views/Form');

        //this is ugly workaround for different build strategies and, possibly,
        //different TwigBridge installation directories
        if (is_dir(__DIR__.'/../../../vendor/symfony/twig-bridge/Resources/views/Form')) {
            $twigPaths[] = __DIR__.'/../../../vendor/symfony/twig-bridge/Resources/views/Form';
        } elseif (is_dir(__DIR__.'/../../../vendor/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views/Form')) {
            $twigPaths[] = __DIR__.'/../../../vendor/symfony/twig-bridge/Symfony/Bridge/Twig/Resources/views/Form';
        } elseif (is_dir(__DIR__.'/../../../vendor/symfony/symfony/src/Symfony/Bridge/Twig/Resources/views/Form')) {
            $twigPaths[] = __DIR__.'/../../../vendor/symfony/symfony/src/Symfony/Bridge/Twig/Resources/views/Form';
        } else {
            $twigPaths[] = __DIR__.'/../../../../../symfony/symfony/src/Symfony/Bridge/Twig/Resources/views/Form';
        }

        $loader = new StubFilesystemLoader($twigPaths);

        $environment = new \Twig_Environment($loader, array('strict_variables' => true));
        $environment->addExtension(new TranslationExtension(new StubTranslator()));

        if (class_exists('Ivory\CKEditorBundle\Templating\CKEditorHelper')) {
            $helperClass = 'Ivory\CKEditorBundle\Templating\CKEditorHelper';
        } else {
            $helperClass = 'Ivory\CKEditorBundle\Helper\CKEditorHelper';
        }

        $helper = $this->getMockBuilder($helperClass)
                ->disableOriginalConstructor()
                ->getMock();

        $environment->addExtension(new CKEditorExtension($helper));
        $environment->addExtension($this->extension);

        $this->extension->initRuntime($environment);
    }

    /**
     * Renders widget from FormView, in SonataAdmin context, with optional view variables $vars. Returns plain HTML.
     *
     * @param FormView $view
     * @param array    $vars
     *
     * @return string
     */
    protected function renderWidget(FormView $view, array $vars = array())
    {
        $sonataAdmin = array(
            'name'              => null,
            'admin'             => null,
            'value'             => null,
            'edit'              => 'standard',
            'inline'            => 'natural',
            'field_description' => null,
            'block_name'        => false,
            'options'           => array(),
        );

        $vars = array_merge(array(
            'sonata_admin' => $sonataAdmin,
        ), $vars);

        return (string) $this->extension->renderer->searchAndRenderBlock($view, 'widget', $vars);
    }

    /**
     * Helper method to strip newline and space characters from html string to make comparing easier.
     *
     * @param string $html
     *
     * @return string
     */
    protected function cleanHtmlWhitespace($html)
    {
        $html = preg_replace_callback('/>([^<]+)</', function ($value) {
            return '>'.trim($value[1]).'<';
        }, $html);

        return $html;
    }

    /**
     * @param $html
     *
     * @return mixed
     */
    protected function cleanHtmlAttributeWhitespace($html)
    {
        $html = preg_replace_callback('~<([A-Z0-9]+) \K(.*?)>~i', function ($m) {
            $replacement = preg_replace('~\s*~', '', $m[0]);

            return $replacement;
        }, $html);

        return $html;
    }
}
