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

namespace Sonata\FormatterBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TextFormatterControllerTest extends WebTestCase
{
    /**
     * @requires extension gd
     */
    public function testTruncate(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/text_format');

        static::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        static::assertSame('<h1>Test</h1>345<div class="sonata-gist"><script src="https://gist.github.com/1552362.js?file=gistfile1.txt"></script></div>', $client->getResponse()->getContent());
    }
}
