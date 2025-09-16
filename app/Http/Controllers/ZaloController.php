<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveZaloTokenRequest;
use App\Services\ZaloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZaloController extends Controller
{
    protected $zaloService;

    public function __construct(ZaloService $zaloService)
    {
        $this->zaloService = $zaloService;
    }

    public function getTemplates(Request $request)
    {
        return $this->zaloService->getZnsTemplates($request);
    }

    public function getInfo(Request $request, $id)
    {
        return $this->zaloService->getInfoTemplate($request, $id);
    }

    public function send(Request $request)
    {
        return $this->zaloService->processSendZns();
    }

    public function saveToken(SaveZaloTokenRequest $request)
    {
        return $this->zaloService->saveTokenZalo($request);
    }

    public function getToken()
    {
        return $this->zaloService->getTokenZalo();
    }

    public function handle(Request $request)
    {
        try {
            Log::info('Zalo Webhook payload', $request->all());
        } catch (\Throwable $e) {
            Log::error('Webhook error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }
}