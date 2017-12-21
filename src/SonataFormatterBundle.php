<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SonataFormatterBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    public function boot()
    {
        $this->registerFormMapping();
    }

    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping([
            'ckeditor' => 'Ivory\CKEditorBundle\Form\Type\CKEditorType',
            'sonata_formatter_type' => 'Sonata\FormatterBundle\Form\Type\FormatterType',
            'sonata_simple_formatter_type' => 'Sonata\FormatterBundle\Form\Type\SimpleFormatterType',
        ]);
    }
}
