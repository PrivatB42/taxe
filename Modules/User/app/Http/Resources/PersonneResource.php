<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            //'id'                => $this->id,
            'nom_complet'       => $this->nom_complet,
            'slug'              => $this->slug,
            'email'             => $this->email,
            'telephone'         => $this->telephone,
            //'email_verified_at' => $this->email_verified_at,
            'photo'             => $this->photo 
                                    ? url($this->photo) 
                                    : asset('assets/images/avatar/user'.rand(1, 4).'.png'),
        ];
    }
}
