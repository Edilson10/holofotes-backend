<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class InnovationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url' => $this->image ? URL::to($this->image) : null,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => (new \DateTime($this->created_at))->format('d-m-Y H:i:s'),
            'updated_at' => (new \DateTime($this->updated_at))->format('d-m-Y H:i:s'),
        ];
    }
}
