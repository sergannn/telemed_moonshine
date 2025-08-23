<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Recommendation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecommendationController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        $recommendations = Recommendation::dynamicPaginate();

        return $recommendations;
    }

    public function store(Request $request): JsonResponse
    {
        $recommendation = Recommendation::create($request->validated());

        return $this->responseCreated('Recommendation created successfully', $recommendation);
    }

    public function show(Recommendation $recommendation): JsonResponse
    {
        return $this->responseSuccess(null, $recommendation);
    }

    public function update(Request $request, Recommendation $recommendation): JsonResponse
    {
        $recommendation->update($request->validated());

        return $this->responseSuccess('Recommendation updated Successfully', $recommendation);
    }

    public function destroy(Recommendation $recommendation): JsonResponse
    {
        $recommendation->delete();

        return $this->responseDeleted();
    }

   
}
