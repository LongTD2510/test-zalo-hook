<?php

namespace App\Jobs;

use App\Models\MessageLog;
use App\Services\ZaloService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendZnsTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $params;
    protected int $logId;
    /**
     * Create a new job instance.
     */
    public function __construct($params, $logId)
    {
        $this->params = $params;
        $this->logId = $logId;
    }

    /**
     * Execute the job.
     */
    public function handle(ZaloService $zaloService): void
    {
        $log = MessageLog::find($this->logId);
        if (!$log) return;

        $response = $zaloService->sendZnsTemplate($this->params);

        $zaloService->handleZnsResponse($log, $response);
    }
}
