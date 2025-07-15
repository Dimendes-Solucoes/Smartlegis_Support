<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clients\ClientRegisterRequest;
use App\Services\ClientService;

class ClientController extends Controller
{
    public function __construct(
        protected ClientService $service
    ) {}

    /** @group AdminClients */
    public function list()
    {
        return response()->json([
            'clients' => $this->service->list()
        ]);
    }

    /** @group AdminClients */
    public function register(ClientRegisterRequest $request)
    {
        $client = $this->service->register($request->validated());

        return response()->json([
            'client' => $client
        ]);
    }

    /** @group AdminClients */
    public function profile()
    {
        return response()->json([
            'client' => $this->service->profile()
        ]);
    }

    /** @group AdminClients */
    public function delete($id)
    {
        $this->service->deleteById($id);

        return response()->noContent();
    }
}