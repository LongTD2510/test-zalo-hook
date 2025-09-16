<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StorageService;
use Illuminate\Support\Facades\Log;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $files = [
                'https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/PNG_transparency_demonstration_1.png/640px-PNG_transparency_demonstration_1.png'
            ];
            $typeId = 2;
            $type = 'test';
            $storageService = new StorageService('s3');
            $storageService->downloadImageThenSave($files, $typeId, $type);
        } catch (\Exception $e) {
            dd($e);
            Log::debug('Error in download image job : ' .$e->getMessage());
        }
    }
}
