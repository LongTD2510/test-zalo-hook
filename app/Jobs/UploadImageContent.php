<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class UploadImageContent implements ShouldQueue
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
            $storageService->uploadTempFiles($this->files, $this->type, $this->typeId);
        } catch (\Exception $e) {
            Log::debug('Error in upload image content job : ' .$e->getMessage());
        }
    }
}
