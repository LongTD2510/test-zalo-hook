<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{

    public function listingTemplate(Request $request)
    {
        return app(TemplateService::class)->getTemplate($request);
    }

    public function store(TemplateRequest $request)
    {
        return app(TemplateService::class)->storeTemplate($request->validated());
    }

    public function update(UpdateTemplateRequest $request, $id)
    {
        return app(TemplateService::class)->updateTemplate($request->validated(), $id);
    }

    public function destroy(Request $request)
    {
        return app(TemplateService::class)->deleteTemplate($request);
    }

    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        return app(TemplateService::class)->uploadImage($validator->validated());
    }
}
