<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class DownloadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $files;
    protected $typeId;
    protected $type;
    protected $storageService;

    /**
     * Create a new job instance.
     */
    public function __construct(array $files, $typeId, $type, string $storageService)
    {
        $this->files = $files;
        $this->typeId = $typeId;
        $this->type = $type;
        $this->storageService = $storageService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $storageService = new StorageService($this->storageService);
            $storageService->downloadImageThenSave($this->files, $this->typeId, $this->type);
        } catch (\Exception $e) {
            Log::debug('Error in download image job : ' .$e->getMessage());
        }
    }
}
