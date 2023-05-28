<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
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
            'id'           => $this->id,
            'tracking_number' => $this->tracking_number,
            'provider_id'  => $this->provider_id,
            'user_id' => $this->user_id,
            'sender_address' => $this->sender_address,
            'receiver_address' => $this->receiver_address,
            'phone' => $this->phone,
            'receiver_email' => $this->receiver_email,
            'item' => $this->item,
            'quantity' => $this->quantity,
            'status' => $this->status,
        ];
    }
}
