<?php

namespace App\Services;

use App\Models\ZaloToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ZaloAuthService
{
    protected string $appId;
    protected string $appSecret;
    protected string $accessTokenUrl;

    public function __construct()
    {
        $this->appId = env('ZALO_APP_ID');
        $this->appSecret = env('ZALO_APP_SECRET');
        $this->accessTokenUrl = env('ZALO_OA_ACCESS_TOKEN_URL');
    }

    public function getAccessToken(): ?string
    {
        $token = ZaloToken::where('app_id', $this->appId)->latest()->first();

        if (!$token) {
            return '';
        }

        if ($token->access_token_expires_at && $token->access_token_expires_at->isFuture()) {
            return $token->access_token;
        }

        return $this->refreshAccessToken($token->refresh_token ?? '') ?? '';
    }

    public function refreshAccessToken(string $refreshToken): ?string
    {
        $url = $this->accessTokenUrl;

        $response = Http::asForm()->post($url, [
            'app_id'        => $this->appId,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json();

        if (!empty($data['access_token'])) {
            $token = ZaloToken::updateOrCreate(
                ['app_id' => $this->appId],
                [
                    'access_token'             => $data['access_token'],
                    'refresh_token'            => $data['refresh_token'] ?? $refreshToken,
                    'access_token_expires_at'  => Carbon::now()->addSeconds((int)($data['expires_in'] ?? 86400)),
                    'refresh_token_expires_at' => Carbon::now()->addMonths(3),
                ]
            );

            return $token->access_token;
        }

        return null;
    }
}
