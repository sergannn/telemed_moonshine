<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $articles = Article::with('category')->dynamicPaginate();

        return $articles;
    }

    public function store(Request $request): JsonResponse
    {
        $article = Article::create($request->validated());

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => 'Article created successfully',
        ], 201);
    }

    public function show(Article $article): JsonResponse
    {
        // Load the category relationship
        $article->load('category');
        
        // Use Laravel's built-in response method
        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => null,
        ]);
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $article->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => 'Article updated Successfully',
        ]);
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json([
            'success' => true,
            'data' => null,
            'message' => 'Article deleted successfully',
        ]);
    }

   
}
