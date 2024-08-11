<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('order') === 'desc' ? 'desc' : 'asc';

        return Post::query()
            ->orderBy($sortColumn, $sortDirection)
            ->when($request->filled('title'), function (Builder $query) use ($request) {
                $query->where('title', 'like', '%'.$request->input('title').'%');
            })
            ->when($request->filled('body'), function (Builder $query) use ($request) {
                $query->where('body', 'like', '%'.$request->input('body').'%');
            })
            ->when($request->filled('author') && is_numeric($request->input('author')), function (Builder $query) use ($request) {
                $query->where('author_id', $request->input('author'));
            })
            ->when($request->filled('created_at'), function (Builder $query) use ($request) {
                $query->whereDate('created_at', $request->input('created_at'));
            })
            ->paginate(10);
    }
}
