<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Bridge to keep BC between 3.x and 4.x, using the bridge between 2.x and 3.x.
 *
 * @author Jérémy Marodon <marodon.jeremy@gmail.com>
 *
 * @deprecated to remove in SonataFormatterBundle 3.x version, which will drop EgeloenCKEditorBundle 2.x and 3.x support
 */
class CKEditorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('templating.engine.php') &&
            $container->hasDefinition('ivory_ck_editor.renderer')) {
            $definition = $container->getDefinition('sonata.formatter.twig.ck_editor');

            $definition->replaceArgument(0, 'ivory_ck_editor.renderer');
        }
    }
}
