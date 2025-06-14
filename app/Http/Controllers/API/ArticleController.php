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
        $articles = Article::dynamicPaginate();

        return $articles;
    }

    public function store(Request $request): JsonResponse
    {
        $article = Article::create($request->validated());

        return $this->responseCreated('Article created successfully', $article);
    }

    public function show(Article $article): JsonResponse
    {
        return $this->responseSuccess(null, $article);
    }

    public function update(Request $request, Article $article): JsonResponse
    {
        $article->update($request->validated());

        return $this->responseSuccess('Article updated Successfully', $article);
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return $this->responseDeleted();
    }

   
}
