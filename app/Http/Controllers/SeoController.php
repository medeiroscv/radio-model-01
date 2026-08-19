<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Podcast;
use App\Models\Program;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => '/', 'freq' => 'hourly', 'priority' => '1.0'],
            ['loc' => '/noticias', 'freq' => 'hourly', 'priority' => '0.9'],
            ['loc' => '/programacao', 'freq' => 'daily', 'priority' => '0.8'],
            ['loc' => '/podcasts', 'freq' => 'daily', 'priority' => '0.8'],
            ['loc' => '/rankings', 'freq' => 'weekly', 'priority' => '0.7'],
            ['loc' => '/musicas', 'freq' => 'daily', 'priority' => '0.7'],
            ['loc' => '/promocoes', 'freq' => 'daily', 'priority' => '0.6'],
            ['loc' => '/a-radio', 'freq' => 'monthly', 'priority' => '0.4'],
            ['loc' => '/contato', 'freq' => 'monthly', 'priority' => '0.4'],
        ]);

        $news = News::published()
            ->orderByDesc('published_at')
            ->limit(500)
            ->get()
            ->map(fn ($n) => [
                'loc' => '/noticias/'.$n->slug,
                'lastmod' => $n->updated_at?->toAtomString(),
                'freq' => 'daily',
                'priority' => '0.7',
            ]);

        $podcasts = Podcast::where('is_active', true)
            ->get()
            ->map(fn ($p) => [
                'loc' => '/podcasts/'.$p->slug,
                'lastmod' => $p->updated_at?->toAtomString(),
                'freq' => 'weekly',
                'priority' => '0.6',
            ]);

        $programs = Program::where('is_active', true)
            ->get()
            ->map(fn ($p) => [
                'loc' => '/programas/'.$p->slug,
                'freq' => 'weekly',
                'priority' => '0.6',
            ]);

        $all = $urls->concat($news)->concat($podcasts)->concat($programs);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($all as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e(url($u['loc']))."</loc>\n";
            if (! empty($u['lastmod'])) {
                $xml .= '    <lastmod>'.$u['lastmod']."</lastmod>\n";
            }
            $xml .= '    <changefreq>'.($u['freq'] ?? 'weekly')."</changefreq>\n";
            $xml .= '    <priority>'.($u['priority'] ?? '0.5')."</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "Disallow: /profile\n";
        $content .= 'Sitemap: '.url('/sitemap.xml')."\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}