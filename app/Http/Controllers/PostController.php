<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = $request->input('order') === 'desc' ? 'desc' : 'asc';

        $query = Post::query()
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
            });

        return PostResource::collection(
            $query->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        return new PostResource(
            Post::create($request->validated())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->noContent();
    }
}
