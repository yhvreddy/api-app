<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];

        $is_deleted = !empty($this->deleted_at)?true:false;
        if($is_deleted){
            $data['is_deleted'] = $is_deleted;
        }

        if(isset($this->accessToken)){
            $data['accessToken'] = $this->accessToken;
            $data['token_type'] = $this->token_type;
        }

        return $data;
    }
}
