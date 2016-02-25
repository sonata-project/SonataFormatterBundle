<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Twig\Extension;

use Ivory\CKEditorBundle\Templating\CKEditorHelper;

/**
 * @author Marodon Jérémy <marodon.jeremy@gmail.com>
 */
class CKEditorExtension extends \Twig_Extension
{
    /**
     * @var CKEditorHelper
     */
    private $helper;

    /**
     * Creates a CKEditor extension bridge for BC.
     *
     * @param CKEditorHelper $helper The CKEditor helper.
     */
    public function __construct(CKEditorHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = array('is_safe' => array('html'));

        return array(
            new \Twig_SimpleFunction('ckeditor_replace', array($this, 'renderReplace'), $options),
        );
    }

    /**
     * @param string $identifier The identifier.
     * @param array  $config     The config.
     *
     * @return string The rendered replace.
     */
    public function renderReplace($identifier, array $config)
    {
        @trigger_error('The ckeditor_replace twig function is now deprecated
        and will be removed in 3.0. Use ckeditor_widget instead.', E_USER_DEPRECATED);

        return $this->helper->renderWidget($identifier, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ck_editor';
    }
}
