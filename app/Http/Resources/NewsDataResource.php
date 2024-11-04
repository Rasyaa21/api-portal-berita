<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

/**
 * @OA\Schema(
 *     schema="NewsData",
 *     type="object",
 *     properties={
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="news_title", type="string"),
 *         @OA\Property(property="news_description", type="string"),
 *         @OA\Property(property="image", type="string"),
 *         @OA\Property(property="user", type="integer"),
 *         @OA\Property(property="author", type="object",
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string")
 *         ),
 *         @OA\Property(property="is_accepted", type="boolean"),
 *         @OA\Property(property="created_at", type="string", format="date"),
 *         @OA\Property(property="comment", type="array", @OA\Items(ref="#/components/schemas/CommentUser"))
 *     }
 * )
 */
class NewsDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'news_title' => $this->news_title,
            'news_description' => $this->news_description,
            'image' => $this->image,
            'user' => $this->user_id,
            'author' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'is_accepted' => $this->is_accepted,
            'created_at' => $this->created_at->format('Y-m-d'),
            'comment' => CommentUserResource::collection($this->whenLoaded('comments'))
        ];
    }
}
