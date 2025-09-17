<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveZaloTokenRequest;
use App\Services\ZaloService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            $event = $request->input('event_name');
            switch ($event) {
                case 'follow':
                    $this->follow($request);
                    break;
                case 'user_submit_info':
                    $this->userSubmitInfo($request);
                    break;
                case 'user_send_text':
                    $this->userSendText($request);
                    break;

                default:
                    # code...
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('Webhook error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    public function follow($request)
    {
        $flowerId = $request->input('follower.id');
        // Tạo record tạm trong DB
        // ZaloUser::firstOrCreate(['zalo_user_id' => $userId]);

        // Gửi message yêu cầu chia sẻ số điện thoại
        // $this->sendRequestShareInfo($userId);
        $this->sendRequestUserInfo($flowerId);
    }

    public function sendRequestUserInfo($zaloUserId)
    {
        $accessToken = env('ZALO_OA_ACCESS_TOKEN');

        $payload = [
            "recipient" => [
                "user_id" => $zaloUserId
            ],
            "message" => [
                "attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "request_user_info",
                        "elements" => [[
                            "title" => "Xác thực học sinh",
                            "subtitle" => "Vui lòng chia sẻ thông tin để liên kết với tài khoản học sinh",
                            "image_url" => "https://cover-talk.zadn.vn/b/6/7/6/2/850d4ee80d9fb77904b916fe6006d882.jpg"
                        ]]
                    ]
                ]
            ]
        ];

        $res = Http::withHeaders([
            'access_token' => $accessToken,
            'Content-Type' => 'application/json'
        ])->post('https://openapi.zalo.me/v3.0/oa/message/cs', $payload);

        return $res->json();
    }

    public function userSubmitInfo($request)
    {
        $zaloUserId = $request->input('sender.id');
        $info = $request->input('info');
        $phone = $info['phone'] ?? null;
        $name  = $info['name'] ?? null;

        // Ghi log để debug
        Log::info("User submit info", [
            'zalo_user_id' => $zaloUserId,
            'info' => $info,
            'raw_request' => $request->all(),
            'phone' => $phone,
            'name' => $name,
        ]);

        // Tìm học sinh theo phone trong DB
        // $student = \App\Models\Student::where('phone', $phone)->first();

        //if ($student) {
            // \App\Models\ZaloUser::updateOrCreate(
            //     ['zalo_user_id' => $zaloUserId],
            //     [
            //         'student_id'   => $student->id,
            //         'phone'        => $phone,
            //         'display_name' => $name,
            //         'extra_info'   => json_encode($info)
            //     ]
            // );

            //Log::info("Mapped zalo_user_id {$zaloUserId} to student {$student->id}");
        //} else {
            // Nếu không tìm thấy học sinh thì lưu tạm
            // \App\Models\ZaloUser::updateOrCreate(
            //     ['zalo_user_id' => $zaloUserId],
            //     [
            //         'phone'        => $phone,
            //         'display_name' => $name,
            //         'extra_info'   => json_encode($info)
            //     ]
            // );

            //Log::warning("Không tìm thấy học sinh có phone {$phone}, lưu tạm zalo_user_id={$zaloUserId}");
        //}
    }

    public function userSendText($request)
    {
        $zaloUserId = $request->input('sender.id');
        $text = $request->input('message.text');
        $message = $request->input('message');

        // Ghi log để debug
        Log::info("User send text", [
            'zalo_user_id' => $zaloUserId,
            'text' => $text,
            'raw_request' => $request->all(),
            'message' => $message,
        ]);
    }
}