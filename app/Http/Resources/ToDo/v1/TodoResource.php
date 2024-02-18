<?php

namespace App\Http\Resources\ToDo\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\v1\UserResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $todo = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user' => new UserResource($this->user),
        ];

        if(!empty($this->deleted_at)) $todo['deleted_at'] = $this->deleted_at;
        return $todo;
    }
}
