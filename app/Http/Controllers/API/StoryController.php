<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StoryController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $stories = Story::dynamicPaginate();

        return $stories;
    }

    public function store(Request $request): JsonResponse
    {
        $story = Story::create($request->validated());

        return $this->responseCreated('Story created successfully', $story);
    }

    public function show(Story $story): JsonResponse
    {
        return $this->responseSuccess(null, $story);
    }

    public function update(Request $request, Story $story): JsonResponse
    {
        $story->update($request->validated());

        return $this->responseSuccess('Story updated Successfully', $story);
    }

    public function destroy(Story $story): JsonResponse
    {
        $story->delete();

        return $this->responseDeleted();
    }

   
}
