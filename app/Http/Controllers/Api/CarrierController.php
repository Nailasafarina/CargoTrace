<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carrier\StoreCarrierRequest;
use App\Http\Resources\CarrierResource;
use App\Models\Carrier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CarrierController extends Controller
{
    /**
     * List all carriers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Carrier::class);

        return CarrierResource::collection(Carrier::all());
    }

    /**
     * Store a new carrier (Admin only).
     */
    public function store(StoreCarrierRequest $request): JsonResponse
    {
        $carrier = Carrier::create($request->validated());

        return (new CarrierResource($carrier))
            ->response()
            ->setStatusCode(201);
    }
}
