<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            // 'schedule_date' => (new \DateTime($this->schedule_date))->format('F j, Y'),
            'schedule_date' => (new \DateTime($this->schedule_date))->format('Y-m-d'),
            'start_time' => (new \DateTime($this->start_time))->format('H:i'),
            'end_time' => (new \DateTime($this->end_time))->format('H:i'),
        ];
    }
}
