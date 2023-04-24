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
use Sonata\FormatterBundle\Tests\App\Entity\Media;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        $this->prepareData();

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
        yield 'Ckeditor Browser Media' => ['/admin/tests/app/media/browser'];

        yield 'Ckeditor Browser Media with filters' => ['/admin/tests/app/media/browser', [
            'provider' => 'sonata.media.provider.image',
        ]];

        yield 'Ckeditor Upload Media' => ['/admin/tests/app/media/upload', [
            'provider' => 'sonata.media.provider.image',
        ], [
            'upload' => new UploadedFile(__DIR__.'/../../Fixtures/logo.jpg', 'logo.jpg'),
        ]];
    }

    private function prepareData(): void
    {
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');
        \assert($manager instanceof EntityManagerInterface);

        $media = new Media();
        $media->setName('name.jpg');
        $media->setProviderStatus(MediaInterface::STATUS_OK);
        $media->setContext('default');
        $media->setProviderReference('name.jpg');
        $media->setProviderName('sonata.media.provider.image');
        $media->setBinaryContent(realpath(__DIR__.'/../../Fixtures/logo.jpg'));

        $manager->persist($media);
        $manager->flush();
    }
}
