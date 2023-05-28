<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ShippingApiException;
use App\Models\Shipping;
use App\Models\Provider;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShippingResource;
use App\Repositories\ShippingRepository;
use App\Services\ShippingApiParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/**
 * Controller for specifying which requests to which API should be passed
 * According to the rules of the provider
 */
class ShippingApiController extends Controller
{
    private string $integrationUrl = '127.0.0.1:8001/api/';
    private array $params;
    private string $uri;
    private array $requestData;
    private bool $errors = false;
    public $response;

    public function __construct(
        ShippingApiParserService $shippingParser,
        private ShippingRepository $shippingRepo
    ) {
    }

    /**
     * General method for specifying shipping request to be sent to a chosen provider
     * @return data of generated shipping item
     */
    public function shippingRequest(
        Request $request,
        ?string $uri = nul,
        bool $dbStructure = false
    ) {
        if (!$uri) {
            throw new ShippingApiException('No uri is given!');
        }
        $request->validate([
            'provider' => ['required', 'string', 'max:30'],
            'method' => ['required', 'string', 'max:6'],
            'request_data' => ['required'],
            'debug' => 'boolean',
        ]);

        $this->requestData = $request->input('request_data');
        $this->integrationUrl .= $uri;

        // Send the request to Shipping integration service with provided information

        $this->params = [
            'provider' => $request->input('provider'),
            'method' => $request->input('method'),
            'request_data' => $this->requestData,
            'user_id' => auth()->user()->id ?? null,
            'debug' => $request->boolean('debug') ?? false,
        ];

        $result = Http::get($this->integrationUrl, $this->params);
        $this->response = json_decode($result, true);

        if (!$dbStructure) {
            return $this->response;
        }

        if ($this->params['provider'] == 'lp_express') {
            if (
                Arr::get($this->response, 'fieldValidationErrors')
                || Arr::get($this->response, 'valueValidationErrors')
            ) {
                $this->errors = true;
                $errorResponse = [
                    'field_errors' => $this->response['fieldValidationErrors'],
                    'value_errors' => $this->response['valueValidationErrors'],
                ];

                return response()->json([
                    'message' => 'Error response from the API',
                    'errors' => $errorResponse,
                ], 400);
            }
            $trackingNumber = $this->response['id'];
            $senderStreet = $this->response['sender']['address']['street'];
            $senderBuilding = $this->response['sender']['address']['building'];
            $senderAddress = $senderStreet . ' ' . $senderBuilding;

            $receiverStreet = $this->response['receiver']['address']['street'] ?? '';
            $receiverBuilding = $this->response['receiver']['address']['building'] ?? '';
            if (!$receiverBuilding && !$receiverStreet) {
                $receiverAddress = 'Not provided';
            } else {
                $receiverAddress = $receiverStreet . ' ' . $receiverBuilding;
            }
            $receiverEmail = $this->response['receiver']['email'];
            $phone = $this->response['receiver']['phone'];
            $item = $this->response['title'];
            $quantity = $this->response['partCount'];
            $provider = Provider::where('name', $this->params['provider'])->first('id');

            /*$shippingData = [
                'tracking_number' => $trackingNumber,
                'provider_id' => $provider->id,
                'user_id' => $this->params['user_id'],
                'sender_address' => $senderAddress,
                'receiver_address' => $receiverAddress,
                'phone' => $phone,
                'receiver_email' => $receiverEmail,
                'item' => $item,
                'quantity' => $quantity,
            ];*/
        } else if ($this->params['provider'] == 'omniva') {
            $trackingNumber = $this->response['barcodes'][0];
            $senderAddress = $this->response['sender']['address'];

            $receiverAddress = $this->response['receiver']['address'];
            $receiverEmail = $this->response['receiver']['email'] ?? 'test@omniva.lt';
            $phone = $this->response['receiver']['phone'];
            $item = $this->response['title'];
            $quantity = $this->response['quantity'];
            $provider = Provider::where('name', $this->params['provider'])->first('id');
        }

        $shippingData = [
            'tracking_number' => $trackingNumber,
            'provider_id' => $provider->id,
            'user_id' => $this->params['user_id'],
            'sender_address' => $senderAddress,
            'receiver_address' => $receiverAddress,
            'phone' => $phone,
            'receiver_email' => $receiverEmail,
            'item' => $item,
            'quantity' => $quantity,
        ];
        return $shippingData;
    }

    /**
     * Creates shipping item in the API provider
     */
    public function createShippingItem(Request $request)
    {
        // Validation happens when processing request
        $data = $this->shippingRequest($request, uri: 'shipping/create', dbStructure: true);
        // Processing errors
        if ($this->errors) {
            $this->errors = false;
            return $data;
        }

        $this->shippingRepo->createShipping($data);

        $response = [
            'message' => 'Shipping created!',
            'details' => $this->response,
        ];

        return response()->json($response, 200);
    }

    public function editShippingItem(Request $request)
    {
        $data = $this->shippingRequest($request, uri: 'shipping/edit', dbStructure: true);
        if ($this->errors) {
            $this->errors = false;
            return $data;
        }

        Shipping::updateOrCreate($data);

        $response = [
            'message' => 'Shipping updated!',
            'details' => $this->response,
        ];

        return response()->json($response, 200);
    }

    public function deleteShippingItem(Request $request)
    {
        $data = $this->shippingRequest($request, uri: 'shipping/delete', dbStructure: false);
        if ($this->errors) {
            $this->errors = false;
            return $data;
        }

        if ($data) { // if true is returned from LP, operation was successful

            $lpShippingId = $request->input('request_data')['lp_shipping_id'];
            Shipping::delete($lpShippingId);

            $response = [
                'message' => 'Shipping deleted!',
                'details' => $this->response,
            ];
        }
        $response = [
            'message' => 'Failed to delete shipping item',
            'details' => $this->response,
        ];

        return response()->json($response);
    }
}
