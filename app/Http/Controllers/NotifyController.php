<?php

namespace App\Http\Controllers;
use App\Traits\ApiResponse;

use Illuminate\Http\Request;
use App\Services\NotifyService;
class NotifyController extends Controller
{
    use ApiResponse;

    public function index (Request $request) {
        try{
            $response = (new NotifyService())->getNotify($request);
            return $this->successResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function store (Request $request) {
        try{
            $response = (new NotifyService())->storeNewNotify($request);
            if (empty($response)) {
                return $this->successResponse();
            }
            return $this->errorResponse(implode('. ', $response));
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function detail (Request $request) {
        try{
            $response = (new NotifyService())->publicGetNotify($request);
            return $this->successResponse($response);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
