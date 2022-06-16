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

use Sonata\FormatterBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

final class CkeditorAdminTest extends WebTestCase
{
    /**
     * @dataProvider provideCrudUrlsCases
     *
     * @param array<string, mixed>        $parameters
     * @param array<string, UploadedFile> $files
     */
    public function testCrudUrls(string $url, array $parameters = [], array $files = []): void
    {
        $client = self::createClient();

        $client->request('GET', $url, $parameters, $files);

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<array<string|array<string, mixed>>>
     *
     * @phpstan-return iterable<array{0: string, 1?: array<string, mixed>, 2?: array<string, UploadedFile>}>
     */
    public static function provideCrudUrlsCases(): iterable
    {
        yield 'Ckeditor Browser Media' => ['/admin/tests/app/media/ckeditor_browser'];

        yield 'Ckeditor Upload Media' => ['/admin/tests/app/media/ckeditor_upload', [
            'provider' => 'sonata.media.provider.image',
        ], [
            'upload' => new UploadedFile(__DIR__.'/../../Fixtures/logo.jpg', 'logo.jpg'),
        ]];
    }

    /**
     * @return class-string<KernelInterface>
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
