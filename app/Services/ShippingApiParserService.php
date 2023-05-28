<?php

namespace App\Services;

use App\Models\Shipping;

/**
 * Parses responses from Shipping API clients
 */
class ShippingApiParserService
{
    public function parseResponse(JsonResponse $response)
    {
        $data = $response->json();
        switch ($data['operation']) {
            case Shipping::SHIPPING_CREATED:
                $data['message'] = 'Shipping created!';
                break;
            case Shipping::SHIPPING_REMOVED:
                $data['message'] = 'Shipping item removed!';
                break;
            case Shipping::SHIPPING_UPDATED:
                $data['message'] = 'Shipping item updated!';
                break;
            case Shipping::COURIER_CALLED:
                $data['message'] = 'Courier called!';
                break;
        }
    }
}
