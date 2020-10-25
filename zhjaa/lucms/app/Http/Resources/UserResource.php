<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->is_admin,
            'head_image' => $this->head_image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'role' => $this->role
        ];
    }
}
