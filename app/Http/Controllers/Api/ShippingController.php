<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\ShippingResource;
use App\Models\Shipping;
use Illuminate\Http\Response;

class ShippingController extends Controller
{
    public function index()
    {
        return ShippingResource::collection(Shipping::all());
    }

    public function store(Request $request)
    {
        $Shipping = Shipping::create($request->validated());

        return ShippingResource::make($Shipping);
    }

    public function show(Shipping $Shipping)
    {
        return ShippingResource::make($Shipping);
    }

    public function update(Request $request, Shipping $Shipping)
    {
        $Shipping->update($request->validated());

        return response()->json(ShippingResource::make($Shipping), Response::HTTP_ACCEPTED);
    }

    public function destroy(Shipping $Shipping)
    {
        $Shipping->delete();

        return response()->json('deleted');
    }
}
