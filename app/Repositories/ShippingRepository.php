<?php

namespace App\Repositories;

use App\Models\Shipping;

class ShippingRepository
{
    public function __construct(private Shipping $shipping)
    {
    }

    public function getShippingRecordById($id)
    {
        return $this->shipping->findOrFail($id);
    }

    public function createShipping(array $shippingData)
    {
        return $this->shipping->create($shippingData);
    }

    public function updateShipping($id, array $shippingData)
    {
        $shipping = $this->getShippingRecordById($id);
        $shipping->fill($shippingData);
        $shipping->save();

        return $shipping;
    }

    public function deleteById($id)
    {
        $shipping = $this->getShippingRecordById($id);
        $shipping->delete();
    }

    public function getAllShippings()
    {
        return $this->shipping->all();
    }
}
