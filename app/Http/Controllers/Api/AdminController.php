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


    public function index(){
        try{
            $news = News::all();
            return response()->json([
                'message' => 'success retrieve all the news',
                'news' => NewsDataResource::collection($news)
            ], $this->successCode);
        } catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function acceptNews(Request $req, $news_id){
        try{
            $validator = Validator::make($req->all(), [
                'is_accepted' => 'boolean|required'
            ]);
            if($validator->fails()){
                return response()->json([
                    'error' => $validator->errors()
                ], 401);
            }
            $news = News::findOrFail($news_id);
            if(!$news){
                return response()->json([
                    'error' => 'data not found'
                ], 404);
            }
            $validatedRequest = $validator->validate();
            $news->update(['is_accepted' => $validatedRequest['is_accepted']]);
            return response()->json([
                'message' => 'news approved',
                'news' => new NewsDataResource($news)
            ], $this->successCode);
        } catch (ModelNotFoundException $e){
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
