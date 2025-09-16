<?php

namespace App\Console\Commands;

use App\Enums\TemplateEnums;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Template;
use Illuminate\Support\Str;

class SyncZnsTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zalo:sync-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ tất cả template ZNS từ Zalo về DB';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $url     = rtrim(env('ZALO_ZNS_TEMPLATE_API_URL', 'https://business.openapi.zalo.me/template'), '/') . '/all';
        $token   = env('ZALO_OA_ACCESS_TOKEN');
        $limit   = 100;
        $offset  = 0;
        $total   = 0;
        $synced  = 0;

        $this->info("🔄 Bắt đầu đồng bộ template ZNS từ Zalo...");

        while (true) {
            $response = Http::withHeaders([
                'access_token' => $token,
                'Content-Type' => 'application/json',
            ])->get($url, [
                'offset' => $offset,
                'limit'  => $limit,
            ])->json();

            $data = $response['data'] ?? [];

            if (empty($data)) {
                break;
            }

            foreach ($data as $tpl) {
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

                $synced++;
            }

            $total = $response['metadata']['total'] ?? $total;
            $offset += $limit;

            $this->info("✅ Đồng bộ được {$synced}/{$total} template...");
        }

        $this->info("🎉 Hoàn tất! Tổng số template đã sync: {$synced}");

        return self::SUCCESS;
    }
}