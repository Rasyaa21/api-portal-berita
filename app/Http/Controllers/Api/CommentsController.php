<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Comments;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
    * @OA\Schema(
     *     schema="Comment",
     *     type="object",
     *     title="Comment Resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="news_id", type="integer", example=1),
     *     @OA\Property(property="user_id", type="integer", example=1),
     *     @OA\Property(property="comment", type="string", example="This is a comment.")
     * )
 */
class CommentsController
{
    public $succesCode = 200;

    /**
     * @OA\Post(
     *     path="/news/{news_id}/comments",
     *     summary="Add a comment to a news article",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the news article"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"comment"},
     *             @OA\Property(property="comment", type="string", example="This is a great article!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment added successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="upload comment success"),
     *             @OA\Property(property="comment", ref="#/components/schemas/Comment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News not found or not accepted",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="News not found or not accepted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function addComment($news_id, Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'comment' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 403);
            }
            $news = News::where('id', $news_id)->where('is_accepted', true)->first();
            if (!$news) {
                return response()->json([
                    'error' => 'News not found or not accepted'
                ], 404);
            }
            $validatedComments = $validator->validated();
            $comment = Comments::create([
                'news_id' => $news->id,
                'user_id' => Auth::id(),
                'comment' => $validatedComments['comment']
            ]);
            return response()->json([
                'message' => 'upload comment success',
                'comment' => new CommentResource($comment)
            ], $this->succesCode);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/news/{news_id}/comments/{id}",
     *     summary="Delete a comment from a news article",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="news_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the news article"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the comment to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Comment deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="comment not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function deleteComment($news_id, $id)
    {
        try {
            $comment = Comments::where('id', $id)->where('news_id', $news_id)->where('user_id', Auth::id())->firstOrFail();
            $comment->delete();
            return response()->json([
                'message' => 'Comment deleted successfully'
            ], $this->succesCode);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
