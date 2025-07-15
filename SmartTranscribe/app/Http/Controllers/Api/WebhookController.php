<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Webhooks\WebhookDeleteByExternalRequest;
use App\Services\WebhookService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected WebhookService $service
    ) {}

    /** @group AdminWebhooks */
    public function list()
    {
        $webhooks = $this->service->list();

        return response()->json([
            'webhooks' => $webhooks
        ]);
    }

    /** @group AdminWebhooks */
    public function create()
    {
        $client_id = currentClient()->id;

        $webhook = $this->service->createForClient($client_id);

        return response()->json([
            'webhook' => $webhook
        ]);
    }

    /** @group AdminWebhooks */
    public function deleteByExternal(WebhookDeleteByExternalRequest $request)
    {
        $this->service->deleteByExternal($request->external_id);

        return response()->noContent();
    }

    /** @group ClientWebhooks */
    public function watchTranscription(Request $request)
    {
        $this->service->handleTranscription($request->all());

        return response($request->query('validationToken'), 200);
    }
}
