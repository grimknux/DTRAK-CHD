<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class SitemapController extends Controller
{
    public function index()
    {
        // Example: dynamic pages (you can fetch from DB)
        $pages = [
            [
                'loc' => base_url('/'),
                'lastmod' => date('Y-m-d'),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ],
            [
                'loc' => base_url('/about'),
                'lastmod' => '2025-08-28',
                'changefreq' => 'monthly',
                'priority' => '0.8'
            ],
            [
                'loc' => base_url('/contact'),
                'lastmod' => '2025-08-20',
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ],
        ];

        // Build XML
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
        $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($pages as $page) {
            $url = $xml->addChild('url');
            $url->addChild('loc', htmlspecialchars($page['loc']));
            $url->addChild('lastmod', $page['lastmod']);
            $url->addChild('changefreq', $page['changefreq']);
            $url->addChild('priority', $page['priority']);
        }

        // Set headers & output
        return $this->response
            ->setHeader('Content-Type', 'application/xml')
            ->setBody($xml->asXML());
    }
}
