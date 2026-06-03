<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class CmsController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // CMS0 — Library / Overview
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $tab    = $request->input('tab', 'pages');
        $search = $request->input('search');
        $status = $request->input('status');
        $owner  = $request->input('owner');

        $allItems = [
            ['id' => 1, 'title' => 'How It Works',               'slug' => 'how-it-works',          'type' => 'page', 'status' => 'Published', 'owner' => 'Alice Morgan', 'updated_at' => now()->subDays(2)],
            ['id' => 2, 'title' => 'About Carsmart',              'slug' => 'about',                 'type' => 'page', 'status' => 'Published', 'owner' => 'Alice Morgan', 'updated_at' => now()->subDays(5)],
            ['id' => 3, 'title' => 'Terms & Conditions',          'slug' => 'terms',                 'type' => 'page', 'status' => 'Draft',     'owner' => 'Ben Carter',   'updated_at' => now()->subDays(10)],
            ['id' => 4, 'title' => 'Privacy Policy',              'slug' => 'privacy',               'type' => 'page', 'status' => 'Published', 'owner' => 'Ben Carter',   'updated_at' => now()->subDays(14)],
            ['id' => 5, 'title' => 'Spring Sale Campaign',        'slug' => 'spring-sale-2025',      'type' => 'post', 'status' => 'Scheduled', 'owner' => 'Clara James',  'updated_at' => now()->subDays(1)],
            ['id' => 6, 'title' => 'New Auction Feature Launch',  'slug' => 'auction-feature-launch','type' => 'post', 'status' => 'Published', 'owner' => 'Clara James',  'updated_at' => now()->subDays(3)],
            ['id' => 7, 'title' => 'Vendor Onboarding Guide',     'slug' => 'vendor-onboarding',     'type' => 'post', 'status' => 'Draft',     'owner' => 'David Singh',  'updated_at' => now()->subDays(7)],
            ['id' => 8, 'title' => 'Seller FAQ',                  'slug' => 'seller-faq',            'type' => 'page', 'status' => 'Archived',  'owner' => 'Emma Walsh',   'updated_at' => now()->subDays(30)],
        ];

        // Tab filter
        if ($tab !== 'all') {
            $type = rtrim($tab, 's');
            $allItems = array_filter($allItems, fn ($i) => $i['type'] === $type);
        }

        // Search filter
        if ($search) {
            $allItems = array_filter($allItems, fn ($i) => stripos($i['title'], $search) !== false || stripos($i['slug'], $search) !== false);
        }

        // Status filter
        if ($status) {
            $allItems = array_filter($allItems, fn ($i) => $i['status'] === $status);
        }

        // Owner filter
        if ($owner) {
            $allItems = array_filter($allItems, fn ($i) => $i['owner'] === $owner);
        }

        $allItems   = array_values($allItems);
        $totalItems = count($allItems);

        $page    = $request->input('page', 1);
        $perPage = 20;
        $slice   = array_slice($allItems, ($page - 1) * $perPage, $perPage);
        $items   = new LengthAwarePaginator($slice, $totalItems, $perPage, $page, ['path' => $request->url()]);

        $owners = ['Alice Morgan', 'Ben Carter', 'Clara James', 'David Singh', 'Emma Walsh'];

        return view('cms.index', compact('items', 'totalItems', 'owners', 'tab', 'search', 'status', 'owner'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CMS1 — Page/Post Editor
    // ──────────────────────────────────────────────────────────────────────────

    public function createPage(): View
    {
        $users = collect([
            ['id' => 1, 'name' => 'Alice Morgan'],
            ['id' => 2, 'name' => 'Ben Carter'],
            ['id' => 3, 'name' => 'Clara James'],
        ]);

        return view('cms.edit', ['users' => $users, 'item' => null, 'versions' => collect()]);
    }

    public function storePage(Request $request): RedirectResponse
    {
        return redirect()->route('cms.index')->with('success', 'Page created.');
    }

    public function createPost(): View
    {
        $users = collect([
            ['id' => 1, 'name' => 'Alice Morgan'],
            ['id' => 2, 'name' => 'Ben Carter'],
            ['id' => 3, 'name' => 'Clara James'],
        ]);

        return view('cms.edit', ['users' => $users, 'item' => null, 'versions' => collect()]);
    }

    public function storePost(Request $request): RedirectResponse
    {
        return redirect()->route('cms.index')->with('success', 'Post created.');
    }

    public function edit(int $id = 1): View
    {
        $users = collect([
            ['id' => 1, 'name' => 'Alice Morgan'],
            ['id' => 2, 'name' => 'Ben Carter'],
            ['id' => 3, 'name' => 'Clara James'],
        ]);

        $versions = collect([
            ['id' => 3, 'version' => 3, 'user' => 'Alice Morgan', 'note' => 'Updated CTA copy',          'created_at' => now()->subHours(2)],
            ['id' => 2, 'version' => 2, 'user' => 'Ben Carter',   'note' => 'Fixed broken image link',   'created_at' => now()->subDays(1)],
            ['id' => 1, 'version' => 1, 'user' => 'Alice Morgan', 'note' => 'Initial draft',             'created_at' => now()->subDays(3)],
        ]);

        $item = [
            'id'               => 1,
            'title'            => 'How It Works',
            'slug'             => 'how-it-works',
            'type'             => 'page',
            'status'           => 'Published',
            'owner_id'         => 1,
            'body'             => '<p>Carsmart makes buying and selling classic cars simple.</p>',
            'categories'       => 'Guide',
            'tags'             => 'buying, selling, auctions',
            'seo_title'        => 'How It Works — Carsmart',
            'seo_description'  => 'Learn how to buy and sell classic cars on Carsmart.',
            'homepage_carousel'=> false,
            'editions_feature' => false,
            'published_at'     => now()->subDays(5),
        ];

        return view('cms.edit', compact('item', 'users', 'versions'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Saved.');
    }

    public function destroy(int $id): RedirectResponse
    {
        return redirect()->route('cms.index')->with('success', 'Item deleted.');
    }

    public function publish(int $id): RedirectResponse
    {
        return back()->with('success', 'Published.');
    }

    public function schedule(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Scheduled.');
    }

    public function archive(int $id): RedirectResponse
    {
        return back()->with('success', 'Archived.');
    }

    public function versions(int $id): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            ['id' => 3, 'version' => 3, 'user' => 'Alice Morgan', 'note' => 'Updated CTA copy',        'created_at' => now()->subHours(2)],
            ['id' => 2, 'version' => 2, 'user' => 'Ben Carter',   'note' => 'Fixed broken image link', 'created_at' => now()->subDays(1)],
            ['id' => 1, 'version' => 1, 'user' => 'Alice Morgan', 'note' => 'Initial draft',           'created_at' => now()->subDays(3)],
        ]);
    }

    public function rollback(Request $request, int $id, int $version): RedirectResponse
    {
        return back()->with('success', "Rolled back to version {$version}.");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // CMS2 — Banners & Features
    // ──────────────────────────────────────────────────────────────────────────

    public function banners(Request $request): View
    {
        $featureData = [
            ['id' => 1, 'slot' => 'homepage_hero',   'date' => now()->toDateString(),                    'end_date' => now()->addDays(7)->toDateString(), 'ref_type' => 'post',    'ref_id' => 6, 'channels' => ['web', 'app'], 'content' => ['title' => 'New Auction Feature Launch']],
            ['id' => 2, 'slot' => 'homepage_banner',  'date' => now()->addDays(3)->toDateString(),        'end_date' => now()->addDays(10)->toDateString(),'ref_type' => 'post',    'ref_id' => 5, 'channels' => ['web'],        'content' => ['title' => 'Spring Sale Campaign']],
            ['id' => 3, 'slot' => 'editions_spotlight','date' => now()->subDays(2)->toDateString(),       'end_date' => now()->addDays(5)->toDateString(), 'ref_type' => 'listing', 'ref_id' => 1, 'channels' => ['web', 'email'],'content' => ['title' => 'Featured: Porsche 911']],
        ];

        $page     = $request->input('page', 1);
        $perPage  = 50;
        $slice    = array_slice($featureData, ($page - 1) * $perPage, $perPage);
        $features = new LengthAwarePaginator($slice, count($featureData), $perPage, $page, ['path' => $request->url()]);

        return view('cms.banners', compact('features'));
    }

    public function storeFeature(Request $request): RedirectResponse
    {
        return back()->with('success', 'Feature scheduled.');
    }

    public function updateFeature(Request $request, int $id): RedirectResponse
    {
        return back()->with('success', 'Feature updated.');
    }

    public function destroyFeature(int $id): RedirectResponse
    {
        return back()->with('success', 'Feature removed.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Media Library
    // ──────────────────────────────────────────────────────────────────────────

    public function media(Request $request): View
    {
        $search = $request->input('search');
        $type   = $request->input('type');

        $allMedia = [
            ['id' => 1, 'name' => 'hero-banner.jpg',          'url' => 'https://placehold.co/600x400/1a1a2e/ffffff?text=Hero', 'type' => 'image',    'size' => '142 KB', 'mime' => 'image/jpeg',      'created_at' => now()->subDays(1)],
            ['id' => 2, 'name' => 'auction-explainer.mp4',    'url' => '#',                                                    'type' => 'video',    'size' => '4.2 MB', 'mime' => 'video/mp4',        'created_at' => now()->subDays(3)],
            ['id' => 3, 'name' => 'vendor-guide.pdf',         'url' => '#',                                                    'type' => 'document', 'size' => '380 KB', 'mime' => 'application/pdf',  'created_at' => now()->subDays(5)],
            ['id' => 4, 'name' => 'porsche-spotlight.jpg',    'url' => 'https://placehold.co/600x400/2d2d2d/ffffff?text=Porsche', 'type' => 'image', 'size' => '218 KB', 'mime' => 'image/jpeg',       'created_at' => now()->subDays(6)],
            ['id' => 5, 'name' => 'spring-sale-banner.png',   'url' => 'https://placehold.co/600x400/e63946/ffffff?text=Sale',  'type' => 'image',   'size' => '95 KB',  'mime' => 'image/png',        'created_at' => now()->subDays(7)],
            ['id' => 6, 'name' => 'carsmart-logo.svg',        'url' => '#',                                                    'type' => 'image',    'size' => '12 KB',  'mime' => 'image/svg+xml',    'created_at' => now()->subDays(14)],
        ];

        if ($search) {
            $allMedia = array_filter($allMedia, fn ($m) => stripos($m['name'], $search) !== false);
        }

        if ($type) {
            $allMedia = array_filter($allMedia, fn ($m) => $m['type'] === $type);
        }

        $allMedia   = array_values($allMedia);
        $totalMedia = count($allMedia);

        $page    = $request->input('page', 1);
        $perPage = 48;
        $slice   = array_slice($allMedia, ($page - 1) * $perPage, $perPage);
        $media   = new LengthAwarePaginator($slice, $totalMedia, $perPage, $page, ['path' => $request->url()]);

        return view('cms.media', compact('media', 'totalMedia', 'search', 'type'));
    }

    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(['uploaded' => []]);
    }

    public function destroyMedia(int $id): RedirectResponse
    {
        return back()->with('success', 'Media deleted.');
    }
}