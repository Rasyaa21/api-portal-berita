<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NewsDataResource;
use App\Models\Comments;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PostController
{
    public $successCode = 200;


    public function ApprovePosts(){
        try {
            $news = News::with(['comments.user'])->where('is_accepted', true)->get();
            return response()->json([
                'message' => 'Success retrieve all the accepted news',
                'news' => NewsDataResource::collection($news),
            ], $this->successCode);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function unApprovePosts(){
        try {
            $news = News::with(['comments.user'])->where('is_accepted', false)->get();
            return response()->json([
                'message' => 'Success retrieve all the unaccepted news',
                'news' => NewsDataResource::collection($news),
            ], $this->successCode);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function SpecificNews($news_id){
        try {
            $news = News::with(['comments.user'])->where('id', $news_id)->firstOrFail();
            return response()->json([
                'message' => 'Success retrieve news',
                'news' => new NewsDataResource($news)
            ], $this->successCode);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $req){
        $validator = Validator::make($req->all(), [
            'news_title' => 'required',
            'news_description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 401);
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


    public function update($news_id, Request $req){
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
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($news_id){
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
            return response()->json([
                'error' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserNews(){
        try {
            $user = Auth::user();
            $news = News::with(['comments.user'])->where('user_id', $user->id)->get();
            return response()->json([
                'message' => 'Success retrieve all user news',
                'news' => NewsDataResource::collection($news)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
