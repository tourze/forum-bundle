<?php

namespace ForumBundle\Tests\Controller;

use ForumBundle\Controller\H5Controller;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(H5Controller::class)]
#[RunTestsInSeparateProcesses]
final class H5ControllerTest extends AbstractWebTestCase
{
    #[Test]
    public function testVideoPageWithValidUrl(): void
    {
        $client = self::createClient();
        $videoUrl = 'https://example.com/video.mp4';

        $client->request('GET', '/forum/h5/video', [
            'videoUrl' => urlencode($videoUrl),
        ]);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString($videoUrl, $content);
    }

    #[Test]
    public function testVideoPageWithMissingVideoUrl(): void
    {
        $client = self::createClient();

        $client->request('GET', '/forum/h5/video');

        self::getClient($client);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function testVideoPageWithInvalidVideoUrl(): void
    {
        $client = self::createClient();

        $client->request('GET', '/forum/h5/video', [
            'videoUrl' => ['invalid' => 'array'],
        ]);

        self::getClient($client);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    #[Test]
    public function testVideoPageWithEncodedUrl(): void
    {
        $client = self::createClient();
        $originalUrl = 'https://example.com/path-with-encoded-chars/video.mp4';
        $encodedUrl = urlencode($originalUrl);

        $client->request('GET', '/forum/h5/video', [
            'videoUrl' => $encodedUrl,
        ]);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $content = $client->getResponse()->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString($originalUrl, $content);
    }

    #[Test]
    public function testOnlyGetMethodAllowed(): void
    {
        $client = self::createClient();

        $client->request('POST', '/forum/h5/video', [
            'videoUrl' => 'https://example.com/video.mp4',
        ]);

        self::getClient($client);
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    #[Test]
    public function testRouteIsAccessible(): void
    {
        $client = self::createClient();
        $videoUrl = 'https://example.com/test.mp4';

        $client->request('GET', '/forum/h5/video?videoUrl=' . urlencode($videoUrl));

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $this->assertSame('text/html; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
    }

    #[Test]
    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClient();
        $videoUrl = 'https://example.com/video.mp4';

        $client->request('GET', '/forum/h5/video', [
            'videoUrl' => urlencode($videoUrl),
        ]);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
    }

    #[Test]
    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClient();

        $client->request($method, '/forum/h5/video', [
            'videoUrl' => 'https://example.com/video.mp4',
        ]);

        self::getClient($client);
        $this->assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
