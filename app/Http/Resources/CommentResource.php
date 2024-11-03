<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
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
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'news_id' => $this->news_id,
            'user_id' => $this->user_id,
            'comment' => $this->comment
        ];
    }
}
