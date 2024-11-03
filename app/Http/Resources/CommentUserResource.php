<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentUserResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="CommentUser",
     *     type="object",
     *     title="Comment User Resource",
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="comment", type="string", example="This is a user comment.")
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
            'name' => $this->user->name,
            'comment' => $this->comment
        ];
    }
}
