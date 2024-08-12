<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PostService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get a paginated list of posts, applying filters and sorting as needed.
     */
    public function index(Request $request)
    {
        $cacheKey = 'posts_'.md5(implode('|', $request->except('page')));

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            $query = Post::query();

            if ($request->filled('title')) {
                $query->where('title', 'like', '%'.$request->input('title').'%');
            }

            if ($request->filled('body')) {
                $query->where('body', 'like', '%'.$request->input('body').'%');
            }

            if ($request->filled('author') && is_numeric($request->input('author'))) {
                $query->where('author_id', $request->input('author'));
            }

            if ($request->filled('created_at')) {
                $query->whereDate('created_at', $request->input('created_at'));
            }

            $query->orderBy($request->input('sort', 'id'), $request->input('order', 'asc'))
                ->when($request->filled('title') || $request->filled('body') || $request->filled('author') || $request->filled('created_at'), function ($query) {
                    $query->orderBy('id');
                });

            $posts = $query->paginate(10);

            return $posts;
        });
    }
}
