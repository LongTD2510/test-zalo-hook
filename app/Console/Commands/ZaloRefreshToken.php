<?php

namespace App\Console\Commands;

use App\Services\ZaloAuthService;
use Illuminate\Console\Command;

class ZaloRefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zalo:refresh-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ZaloAuthService $zaloAuthService)
    {
        $accessToken = $zaloAuthService->getAccessToken();
        if ($accessToken) {
            $this->info("✅ Access token hiện tại: " . substr($accessToken, 0, 20) . '...');
            return self::SUCCESS;
        }
        $this->error("❌ Không thể lấy access token. Có thể refresh token đã hết hạn, cần re-auth OA.");
        return self::FAILURE;
    }
}
