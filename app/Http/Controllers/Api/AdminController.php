<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Resources\NewsDataResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class AdminController
{
    public $successCode = 200;

    /**
     * @OA\Get(
     *     path="/api/admin/news",
     *     summary="Admin Section for Approving News",
     *     tags={"Admin Section"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved all news.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="success retrieve all the news"),
     *             @OA\Property(
     *                 property="news",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/NewsData")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function index(){
        try {
            $news = News::all();
            return response()->json([
                'message' => 'success retrieve all the news',
                'news' => NewsDataResource::collection($news)
            ], $this->successCode);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/news/{news_id}/approve",
     *     summary="Accept or reject a news item",
     *     tags={"Admin Section"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         description="ID of the news item",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"is_accepted"},
     *             @OA\Property(property="is_accepted", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News item approval status updated.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="news approved"),
     *             @OA\Property(property="news", ref="#/components/schemas/NewsData")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News item not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function acceptNews(Request $req, $news_id){
        try {
            $validator = Validator::make($req->all(), [
                'is_accepted' => 'boolean|required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 401);
            }
            $news = News::findOrFail($news_id);
            $validatedRequest = $validator->validate();
            $news->update(['is_accepted' => $validatedRequest['is_accepted']]);

            return response()->json([
                'message' => 'news approved',
                'news' => new NewsDataResource($news)
            ], $this->successCode);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'data not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
