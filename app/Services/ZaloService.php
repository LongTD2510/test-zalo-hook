<?php

namespace App\Services;

use App\Enums\TemplateEnums;
use App\Jobs\SendZnsTemplateJob;
use App\Models\MessageLog;
use App\Models\Template;
use App\Models\ZaloToken;
use App\Traits\ApiResponse;
use App\Traits\HasTrackingId;
use App\Traits\PaginateCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ZaloService
{
    use ApiResponse;
    use PaginateCollection;
    use HasTrackingId;
    protected $zalo;
    protected string $znsTemplateApiUrl;
    protected string $znsSendApiUrl;
    protected string $zaloAppId;
    protected const LIMIT = 100;
    protected string $accessToken;

    public function __construct()
    {
        // $this->accessToken = app(ZaloAuthService::class)->getAccessToken();
        $this->znsTemplateApiUrl = env('ZALO_ZNS_TEMPLATE_API_URL', 'https://business.openapi.zalo.me/template');
        $this->znsSendApiUrl = env('ZALO_ZNS_SEND_API_URL', 'https://business.openapi.zalo.me/message/template');
        $this->zaloAppId = env('ZALO_APP_ID');
    }

    public function saveTokenZalo($request)
    {
        $token = ZaloToken::updateOrCreate(
            ['app_id' => $request['app_id'] ?? $this->zaloAppId],
            [
                'access_token'             => $request['access_token'],
                'refresh_token'            => $request['refresh_token'],
                'access_token_expires_at'  => Carbon::now()->addDay(),
                'refresh_token_expires_at' => Carbon::now()->addMonths(3),
            ]
        );

        return $this->successResponse($token, 'Zalo token save success!');
    }

    public function getTokenZalo() {
        $token = ZaloToken::where('app_id', $this->zaloAppId)
            ->latest()
            ->first();

        if (!$token) {
            return $this->errorResponse('Chưa có token', 404);
        }
        
        return $this->successResponse($token);
    }

    public function getZnsTemplates($request)
    {
        try {
            $url = rtrim($this->znsTemplateApiUrl, '/') . '/all';
            $perPage = request()->get('per_page', 10);
            $page    = request()->get('page', 1);
            $limit = $request->query('limit') ?? self::LIMIT;
            $query = Template::query()->orderBy('created_at', 'desc');
            $paginated = $perPage
                ? $query->paginate($perPage, ['*'], 'page')
                : $query->limit($limit)->get();
            if ($request->boolean('refresh')) {
                $response = Http::withHeaders([
                    'access_token' => $this->accessToken,
                    'Content-Type' => 'application/json',
                ])->get($url, [
                    'offset' => ($page - 1) * $perPage,
                    'limit'  => $perPage,
                ]);

                if ($response->failed()) {
                    $error = $response->json();

                    $message = $error['message']
                        ?? $error['errorMsg']
                        ?? 'Error call API ZNS';

                    MessageLog::create([
                        'type'             => TemplateEnums::TEMPLATE_CHANNEL_ZNZ,
                        'template_id'      => null,
                        'status'           => 'FAILED',
                        'request_payload'  => null,
                        'response_payload' => $error,
                        'error_message'    => $message,
                    ]);

                    return $this->errorResponse($message, $response->status());
                }
                $apiData = $response->json();
                if (($apiData['error'] ?? 1) === 0) {
                    foreach ($apiData['data'] ?? [] as $tpl) {
                        Template::updateOrCreate(
                            [
                                'template_id' => $tpl['templateId'],
                                'channel'     => TemplateEnums::TEMPLATE_CHANNEL_ZNZ,
                            ],
                            [
                                'code' => strtoupper(Str::slug($tpl['templateName'] . '_' . $tpl['templateId'], '_')),
                                'name'             => $tpl['templateName'] ?? null,
                                'status'           => $tpl['status'] ?? null,
                                'template_quality' => $tpl['templateQuality'] ?? null,
                                'preview_url'      => $tpl['previewUrl'] ?? null,
                                'template_tag'     => $tpl['templateTag'] ?? null,
                                'price'            => $tpl['price'] ?? null,
                                'params'           => $tpl['listParams'] ?? null,

                            ]
                        );
                    }
                    $paginated = $perPage
                        ? $query->paginate($perPage, ['*'], 'page', $page)
                        : $query->limit($limit)->get();
                }
            }

            return $this->successResponse($paginated, 'Get template success!');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getInfoTemplate($request, $id)
    {
        try {
            $tpl = Template::where('id', $id)->first();

            if (empty($tpl)) {
                return $this->errorResponse('Template not found!', 404);
            }

            if (!empty($tpl->params) && !$request->boolean('refresh') && $tpl->status == TemplateEnums::TEMPLATE_STATUS_ENABLE) {
                return $this->successResponse($tpl);
            }

            $url = rtrim($this->znsTemplateApiUrl, '/') . '/info/v2';
            $response = Http::withHeaders([
                'access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->get($url, [
                'template_id' => $tpl->template_id,
            ]);

            if ($response->failed()) {
                $error = $response->json();

                $message = $error['message']
                    ?? $error['errorMsg']
                    ?? 'Error call API ZNS';

                MessageLog::create([
                    'type'             => TemplateEnums::TEMPLATE_CHANNEL_ZNZ,
                    'template_id'      => $tpl->template_id,
                    'status'           => 'FAILED',
                    'request_payload'  => ['template_id' => $tpl->template_id],
                    'response_payload' => $error,
                    'error_message'    => $message,
                ]);

                return $this->errorResponse($message, $response->status());
            }
            $data = $response->json()['data'] ?? [];
            $tpl->update([
                'params'       => $data['listParams'] ?? null,
                'preview_url'  => $data['previewUrl'] ?? null,
                'template_tag' => $data['templateTag'] ?? null,
                'price'        => $data['price'] ?? null,
                'template_quality' => $data['templateQuality'] ?? null,
            ]);

            return $this->successResponse($tpl->fresh());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function sendZnsTemplate($params)
    {
        try {
            $url = $this->znsSendApiUrl;
            $response = Http::withHeaders([
                'access_token' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $params);
            return $response->json();
        } catch (\Exception $e) {
            return [
                'errCode' => -1,
                'message' => $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    public function processSendZns($data = [])
    {
        $template = Template::where('id', $data['id'])
        ->andWhere('channel', TemplateEnums::TEMPLATE_CHANNEL_ZNZ)
        ->andWhere('status', TemplateEnums::TEMPLATE_STATUS_ENABLE)
        ->first();

        if (!$template) {
            return $this->errorResponse('Template not found!', 404);
        }
        $trackingId = self::generateTrackingId();
        $params = [
            'template_id' => $template->template_id ?? '486065',
            'phone' => $data['phone'] ?? '84357151177',
            'template_data' => [
                'date' => Carbon::now()->format('d/m/Y'),
                'contact' => $data['phone'] ?? '84357151177',
                'full_name' => $data['full_name'] ?? 'Nguyễn Văn A',
            ],
            'tracking_id' =>  $trackingId,
        ];

        if (env('APP_ENV') == 'local') {
            $params['mode'] = 'development';
        }

        $log = MessageLog::create([
            'type'        => TemplateEnums::TEMPLATE_CHANNEL_ZNZ,
            'phone'       => $params['phone'],
            'template_id' => $params['template_id'],
            'tracking_id' => $trackingId,
            'status'      => 'PENDING',
            'request_payload' => $params,
        ]);

        SendZnsTemplateJob::dispatch($params, $log->id)->onQueue('zns');

        $res = [
            'status'      => 'QUEUED',
            'tracking_id' => $trackingId,
        ];

        return $this->successResponse($res);
    }

    public function handleZnsResponse(MessageLog $log, $response)
    {
        $errorCode = $response['error'] ?? $response['error'] ?? 1;
        $message   = $response['message'] ?? $response['message'] ?? null;

        $log->update([
            'status'           => $errorCode === 0 ? 'SUCCESS' : 'FAILED',
            'response_payload' => $response,
            'error_message'    => $message,
        ]);
    }

}