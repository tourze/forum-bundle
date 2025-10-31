<?php

namespace ForumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class H5Controller extends AbstractController
{
    #[Route(path: '/forum/h5/video', name: 'forum-h5-video', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $videoUrl = $request->query->get('videoUrl');
        if (!is_string($videoUrl)) {
            throw new BadRequestException('Invalid video URL');
        }

        $decodedUrl = urldecode($videoUrl);

        // 验证URL格式并过滤潜在的XSS攻击
        if (!$this->isValidVideoUrl($decodedUrl)) {
            throw new BadRequestException('Invalid video URL format');
        }

        return $this->render('@Forum/h5/index.html.twig', [
            'videoUrl' => htmlspecialchars($decodedUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        ]);
    }

    private function isValidVideoUrl(string $url): bool
    {
        // 检查URL格式是否合法
        if (false === filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        // 只允许HTTP和HTTPS协议
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], ['http', 'https'], true)) {
            return false;
        }

        // 检查是否包含常见的视频文件扩展名或视频网站
        $allowedDomains = [
            'youtube.com',
            'youtu.be',
            'vimeo.com',
            'bilibili.com',
            'example.com', // 测试域名
        ];

        $allowedExtensions = ['mp4', 'webm', 'ogg', 'm3u8'];

        $host = $parsedUrl['host'] ?? '';
        $path = $parsedUrl['path'] ?? '';

        // 检查域名白名单
        foreach ($allowedDomains as $domain) {
            if (str_ends_with($host, $domain)) {
                return true;
            }
        }

        // 检查文件扩展名
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        if (in_array(strtolower($extension), $allowedExtensions, true)) {
            return true;
        }

        return false;
    }
}
