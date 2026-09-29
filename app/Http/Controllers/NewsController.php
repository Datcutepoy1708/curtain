<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::published();

        if ($cat = $request->input('category')) {
            $query->where('category', $cat);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $newsList = $query->paginate(6)->withQueryString();
        $recentNews = News::published()->take(4)->get();

        $categoryCounts = [
            'all'    => News::published()->count(),
            'guide'  => News::published()->where('category', 'guide')->count(),
            'trends' => News::published()->where('category', 'trends')->count(),
            'news'   => News::published()->where('category', 'news')->count(),
        ];

        return view('shop.news.index', compact('newsList', 'recentNews', 'categoryCounts'));
    }

    public function show($slug)
    {
        $article = News::where('slug', $slug)->firstOrFail();

        // Increment view count
        $article->increment('views');

        $relatedNews = News::published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->take(3)
            ->get();

        if ($relatedNews->isEmpty()) {
            $relatedNews = News::published()
                ->where('id', '!=', $article->id)
                ->take(3)
                ->get();
        }

        return view('shop.news.show', compact('article', 'relatedNews'));
    }
}
