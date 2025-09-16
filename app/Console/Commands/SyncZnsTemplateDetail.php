<?php

namespace App\Console\Commands;

use App\Enums\TemplateEnums;
use App\Models\Template;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncZnsTemplateDetail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zalo:sync-template-detail {--only-active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ chi tiết template ZNS từ Zalo về DB';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $url   = rtrim(env('ZALO_ZNS_TEMPLATE_API_URL', 'https://business.openapi.zalo.me/template'), '/') . '/info/v2';
        $token = env('ZALO_OA_ACCESS_TOKEN');

        $query = Template::where('channel', TemplateEnums::TEMPLATE_CHANNEL_ZNZ);

        if ($this->option('only-active')) {
            $query->where('status', TemplateEnums::TEMPLATE_STATUS_ENABLE);
        }

        $templates = $query->get();
        $count     = $templates->count();

        $this->info("🔄 Bắt đầu đồng bộ detail cho {$count} template...");

        foreach ($templates as $tpl) {
            $response = Http::withHeaders([
                'access_token' => $token,
                'Content-Type' => 'application/json',
            ])->get($url, [
                'template_id' => $tpl->template_id,
            ])->json();

            if (($response['error'] ?? 1) === 0 && !empty($response['data'])) {
                $detail = $response['data'];

                $tpl->update([
                    'content'          => $detail['content'] ?? null,
                    'preview_url'      => $detail['previewUrl'] ?? null,
                    'template_tag'     => $detail['templateTag'] ?? null,
                    'price'            => $detail['price'] ?? null,
                    'params'           => $detail['listParams'] ?? null,
                    'template_quality' => $detail['templateQuality'] ?? null,
                ]);

                $this->info("✅ Cập nhật template {$tpl->template_id} ({$tpl->name})");
            } else {
                $this->warn("⚠️ Không lấy được detail cho template {$tpl->template_id}");
            }
        }

        $this->info("🎉 Hoàn tất sync detail!");

        return self::SUCCESS;
    }
}