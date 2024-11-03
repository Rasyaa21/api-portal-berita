<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use App\Http\Resources\CommentUserResource;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Comments;
use Egulias\EmailValidator\Parser\Comment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentsController{
    public $succesCode = 200;

    public function addComment($news_id, Request $req){
        try{
            $validator = Validator::make($req->all(), [
                'comment' => 'required'
            ]);
            if ($validator->fails()){
                return response()->json([
                    'error' => $validator->errors()
                ], 403);
            }
            $news = News::where('id', $news_id)->where('is_accepted', true)->first();

            if(!$news){
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
        }  catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteComment($news_id, $id){
        try{
            $comment = Comments::where('id', $id)->where('news_id', $news_id)->where('user_id', Auth::id())->firstOrFail();
            if(!$comment){
                return response()->json([
                    'error' => 'comment not found'
                ]);
            }
            $comment->delete();
            return response()->json([
                'message' => 'Comment deleted successfully'
            ], $this->succesCode);
        } catch (ModelNotFoundException $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
