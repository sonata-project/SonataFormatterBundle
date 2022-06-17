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

namespace Sonata\FormatterBundle\Tests\Functional\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\FormatterBundle\Tests\App\AppKernel;
use Sonata\FormatterBundle\Tests\App\Entity\TextEntity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

final class TextEntityAdminTest extends WebTestCase
{
    /**
     * @dataProvider provideCrudUrlsCases
     */
    public function testCrudUrls(string $url): void
    {
        $client = self::createClient();

        $this->prepareData();

        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array<string>>
     *
     * @phpstan-return iterable<array{0: string}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        yield 'Create TextEntity' => ['/admin/tests/app/textentity/create'];

        yield 'Edit TextEntity' => ['/admin/tests/app/textentity/1/edit'];
    }

    /**
     * @dataProvider provideFormUrlsCases
     *
     * @param array<string, mixed> $parameters
     * @param array<string, mixed> $fieldValues
     */
    public function testFormsUrls(string $url, array $parameters, string $button, array $fieldValues = []): void
    {
        $client = self::createClient();

        $this->prepareData();

        $client->request('GET', $url, $parameters);
        $client->submitForm($button, $fieldValues);
        $client->followRedirect();

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array<string|array<string, mixed>>>
     *
     * @phpstan-return iterable<array{0: string, 1: array<string, mixed>, 2: string, 3?: array<string, mixed>}>
     */
    public static function provideFormUrlsCases(): iterable
    {
        yield 'Create TextEntity Text' => ['/admin/tests/app/textentity/create', [
            'uniqid' => 'textEntity',
        ], 'btn_create_and_list', [
            'textEntity[simpleText]' => 'simple text',
            'textEntity[text][textFormat]' => 'text',
            'textEntity[text][rawText]' => '',
        ]];

        yield 'Create TextEntity Raw HTML' => ['/admin/tests/app/textentity/create', [
            'uniqid' => 'textEntity',
        ], 'btn_create_and_list', [
            'textEntity[simpleText]' => 'simple text',
            'textEntity[text][textFormat]' => 'rawhtml',
            'textEntity[text][rawText]' => 'Sample Raw HTML',
        ]];

        yield 'Create TextEntity Raw HTML With Gist' => ['/admin/tests/app/textentity/create', [
            'uniqid' => 'textEntity',
        ], 'btn_create_and_list', [
            'textEntity[simpleText]' => 'simple text',
            'textEntity[text][textFormat]' => 'rawhtml',
            'textEntity[text][rawText]' => "Sample Raw HTML with some <% gist '1552362', 'gistfile1.txt' %>",
        ]];

        yield 'Create TextEntity Raw HTML With Control Flow' => ['/admin/tests/app/textentity/create', [
            'uniqid' => 'textEntity',
        ], 'btn_create_and_list', [
            'textEntity[simpleText]' => 'simple text',
            'textEntity[text][textFormat]' => 'rawhtml',
            'textEntity[text][rawText]' => 'Sample Raw HTML with some <% for i in 1..5 %><% if i > 2 %><%= i %><% endif %><% endfor %>',
        ]];

        yield 'Create TextEntity Raw HTML With Media' => ['/admin/tests/app/textentity/create', [
            'uniqid' => 'textEntity',
        ], 'btn_create_and_list', [
            'textEntity[simpleText]' => 'simple text',
            'textEntity[text][textFormat]' => 'rawhtml',
            'textEntity[text][rawText]' => 'Sample Raw HTML with some <%= sonata_media(1, "reference") %>',
        ]];

        yield 'Edit TextEntity' => ['/admin/tests/app/textentity/1/edit', [], 'btn_update_and_list'];
    }

    /**
     * @return class-string<KernelInterface>
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    /**
     * @psalm-suppress UndefinedPropertyFetch
     */
    private function prepareData(): void
    {
        // TODO: Simplify this when dropping support for Symfony 4.
        // @phpstan-ignore-next-line
        $container = method_exists($this, 'getContainer') ? self::getContainer() : self::$container;
        $manager = $container->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $textEntity = new TextEntity();

        $manager->persist($textEntity);
        $manager->flush();
    }
}
