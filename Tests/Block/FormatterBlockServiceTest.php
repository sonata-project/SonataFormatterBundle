<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\FormatterBundle\Tests\Block;

use Sonata\BlockBundle\Block\BlockContext;
use Sonata\BlockBundle\Model\Block;
use Sonata\BlockBundle\Test\AbstractBlockServiceTestCase;
use Sonata\FormatterBundle\Block\FormatterBlockService;

class FormatterBlockServiceTest extends AbstractBlockServiceTestCase
{
    public function testExecute()
    {
        $block = new Block();

        $blockContext = new BlockContext($block, array(
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => 'SonataFormatterBundle:Block:block_formatter.html.twig',
        ));

        $blockService = new FormatterBlockService('block.service', $this->templating);
        $blockService->execute($blockContext);

        $this->assertSame('SonataFormatterBundle:Block:block_formatter.html.twig', $this->templating->view);

        $this->assertInternalType('array', $this->templating->parameters['settings']);
        $this->assertInstanceOf('Sonata\BlockBundle\Model\BlockInterface', $this->templating->parameters['block']);
    }

    public function testDefaultSettings()
    {
        $blockService = new FormatterBlockService('block.service', $this->templating);
        $blockContext = $this->getBlockContext($blockService);

        $this->assertSettings(array(
            'format' => 'richhtml',
            'rawContent' => '<b>Insert your custom content here</b>',
            'content' => '<b>Insert your custom content here</b>',
            'template' => 'SonataFormatterBundle:Block:block_formatter.html.twig',
        ), $blockContext);
    }
}
