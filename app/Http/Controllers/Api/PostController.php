<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NewsDataResource;
use App\Models\Comments;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *     schema="CommentUser",
 *     type="object",
 *     properties={
 *         @OA\Property(property="comment", type="string"),
 *         @OA\Property(property="name", type="string"),
 *     }
 * )
 */

class PostController
{
    public $successCode = 200;

    /**
     * @OA\Get(
     *     path="/news/approved",
     *     summary="Retrieve all approved news posts",
     *     tags={"News"},
     *     @OA\Response(
     *         response=200,
     *         description="Success retrieve all the accepted news",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Success retrieve all the accepted news"),
     *             @OA\Property(property="news", type="array", @OA\Items(ref="#/components/schemas/NewsData"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function ApprovePosts()
    {
        try {
            $news = News::with(['comments.user'])->where('is_accepted', true)->get();
            return response()->json([
                'message' => 'Success retrieve all the accepted news',
                'news' => NewsDataResource::collection($news),
            ], $this->successCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/news/unapproved",
     *     summary="Retrieve all unapproved news posts",
     *     tags={"News"},
     *     @OA\Response(
     *         response=200,
     *         description="Success retrieve all the unaccepted news",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Success retrieve all the unaccepted news"),
     *             @OA\Property(property="news", type="array", @OA\Items(ref="#/components/schemas/NewsData"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function unApprovePosts()
    {
        try {
            $news = News::with(['comments.user'])->where('is_accepted', false)->get();
            return response()->json([
                'message' => 'Success retrieve all the unaccepted news',
                'news' => NewsDataResource::collection($news),
            ], $this->successCode);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/news/{news_id}",
     *     summary="Retrieve a specific news post by ID",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the news article"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success retrieve news",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Success retrieve news"),
     *             @OA\Property(property="news", ref="#/components/schemas/NewsData")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function SpecificNews($news_id)
    {
        try {
            $news = News::with(['comments.user'])->where('id', $news_id)->firstOrFail();
            return response()->json([
                'message' => 'Success retrieve news',
                'news' => new NewsDataResource($news)
            ], $this->successCode);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @OA\Post(
     *     path="/news",
     *     summary="Create a new news post",
     *     tags={"News"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"news_title", "news_description", "image"},
     *             @OA\Property(property="news_title", type="string", example="Sample News Title"),
     *             @OA\Property(property="news_description", type="string", example="Detailed news description"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News successfully stored",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="News successfully stored"),
     *             @OA\Property(property="news", ref="#/components/schemas/NewsData")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'news_title' => 'required',
            'news_description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $validatedData = $validator->validated();
        $imagePath = $req->file('image')->store('images', 'public');

        $news = News::create([
            'news_title' => $validatedData['news_title'],
            'image' => $imagePath,
            'news_description' => $validatedData['news_description'],
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => 'News successfully stored',
            'news' => new NewsDataResource($news)
        ], $this->successCode);
    }

    /**
     * @OA\Put(
     *     path="/news/{news_id}",
     *     summary="Update a news post",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the news article"
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="news_title", type="string"),
     *             @OA\Property(property="news_description", type="string"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News successfully updated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="News successfully updated"),
     *             @OA\Property(property="news", ref="#/components/schemas/NewsData")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function update($news_id, Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'news_title' => 'sometimes|required',
                'news_description' => 'sometimes|required',
                'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $user = Auth::user();
            $news = News::where('user_id', $user->id)->where('id', $news_id)->firstOrFail();
            $news->update($validator->validated());

            return response()->json([
                'message' => 'News successfully updated',
                'news' => new NewsDataResource($news)
            ], $this->successCode);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/news/{news_id}",
     *     summary="Delete a news post",
     *     tags={"News"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the news article"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News has been deleted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="string", example="News has been deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function delete($news_id)
    {
        try {
            $user = Auth::user();
            $news = News::where('id', $news_id)->where('user_id', $user->id)->firstOrFail();
            $comments = Comments::where('news_id', $news_id)->first();
            $comments->delete();
            $news->delete();

            return response()->json([
                'success' => 'News has been deleted'
            ], $this->successCode);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/user/news",
     *     summary="Retrieve all news posts by the authenticated user",
     *     tags={"News"},
     *     @OA\Response(
     *         response=200,
     *         description="Success retrieve all user news",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Success retrieve all user news"),
     *             @OA\Property(property="news", type="array", @OA\Items(ref="#/components/schemas/NewsData"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function getUserNews()
    {
        try {
            $user = Auth::user();
            $news = News::with(['comments.user'])->where('user_id', $user->id)->get();

            return response()->json([
                'message' => 'Success retrieve all user news',
                'news' => NewsDataResource::collection($news)
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
