<?php

namespace App\Services;

use App\Enums\FileTypeEnum;
use App\Models\ServiceGroup;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use Intervention\Image\ImageManagerStatic as Image;

class StorageService
{

    protected $storage = 'r2';
    public function __construct($storage)
    {
        $this->storage = $storage;
        $this->validateStorage();
    }

    private function validateStorage()
    {
        $fileSystem = config('filesystems.disks');
        if (empty($fileSystem)) {
            throw new \Exception('not found filesystem');
        }
        $storageOptions = array_keys($fileSystem);
        if (!in_array($this->storage, $storageOptions)) {
            throw new \Exception('Storage option not exists in filesystem');
        }
    }

    public function getFile($fileName)
    {
        return Storage::disk($this->storage)->get($fileName);
    }

    public function storeFile($fileName, $content)
    {
        return Storage::disk($this->storage)->put($fileName, $content);
    }

    public function checkFileExists($fileName)
    {
        return Storage::disk($this->storage)->exists($fileName);
    }

    public function moveFile($oldFile, $newFile)
    {
        return Storage::disk($this->storage)->move($oldFile, $newFile);
    }

    public function copyFile($oldFile, $newFile)
    {
        return Storage::disk($this->storage)->copy($oldFile, $newFile);
    }

    public function getFileUrl($fileName)
    {
        $fileSystem = config('filesystems.disks.' . $this->storage);
        $mainUrl = env('APP_URL');
        if (!empty($fileSystem)) {
            $mainUrl =  $fileSystem['url'];
        }
        return $mainUrl . '/' . $fileName;
    }

    public function clearAllFiles()
    {
        $files = Storage::disk($this->storage)->allFiles();
        Storage::disk($this->storage)->delete($files);
    }

    public function uploadTempFiles($files, $fileType, $typeId)
    {
        $imageUrls = [];
        foreach ($files as $file) {
            try {
                // Bắt đầu đo thời gian tạo filename
                $startFileNameTime = microtime(true);

                $newFileName = microtime(true) . '.webp';

                // Kết thúc đo thời gian tạo filename
                $endFileNameTime = microtime(true);
                $fileNameDuration = $endFileNameTime - $startFileNameTime;
                // var_dump('Thời gian tạo filename: ' . number_format($fileNameDuration * 1000, 2) . ' ms');

                // Bắt đầu đo thời gian lưu file
                $startStoreTime = microtime(true);

                $compressedImage = Image::make($file)
                    ->encode('webp', 100);
                $this->storeFile($newFileName, $compressedImage);

                // Kết thúc đo thời gian lưu file
                $endStoreTime = microtime(true);
                $storeDuration = $endStoreTime - $startStoreTime;
                // var_dump('Thời gian lưu file: ' . number_format($storeDuration * 1000, 2) . ' ms');

                // Log tổng thời gian
                // var_dump('Tổng thời gian xử lý file: ' . number_format(($fileNameDuration + $storeDuration) * 1000, 2) . ' ms');

                $imageUrl = $this->getFileUrl($newFileName);
                // var_dump('imageUrl : ', $imageUrl);

                $imageUrls[] = $imageUrl;
            } catch (\Throwable $e) {
                // var_dump('error in save image');
                Log::debug('Error in uploadTempFiles : ' . print_r($e, true));
                continue;
            }
        }
        $tempsData = [];

        $tempsData = array_map(function ($img) use ($typeId, $fileType) {
            $item = [
                'type_id' => $typeId,
                'type' => $fileType,
                'file_url' => $img,
            ];
            return $item;
        }, $imageUrls);

        File::insert($tempsData);
        return $imageUrls;
    }

    public function copyFileFromOldToNew($files, $typeId, $fileType)
    {
        $imageUrls = [];
        foreach ($files as $file) {
            try {
                $file = trim($file);
                if (!str_contains($file, '_temp')) {
                    $imageUrls[] = $file;
                    continue;
                }
                $fileName = $file;
                $newFileName = str_replace(env('CLOUDFLARE_R2_URL') . '/', '', $fileName);
                $newFileName = str_replace('_temp', '', $newFileName) . '.webp';
                $rawFileName = str_replace(env('CLOUDFLARE_R2_URL') . '/', '', $fileName);
                $fileContent = $this->downloadFileWithCurl($file);
                $compressedImage = Image::make($fileContent)
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->encode('webp', 70); // Mã hóa sang định dạng WebP với chất lượng 70%
                $this->storeFile($newFileName, $compressedImage);
                $imageUrl = $this->getFileUrl($newFileName);
                $imageUrls[] = $imageUrl;
            } catch (\Throwable $e) {
                logException($e, __FUNCTION__);
                continue;
            }
        }
        $tempsData = array_map(function ($img) use ($typeId, $fileType) {
            $item = [
                'type_id' => $typeId,
                'type' => $fileType,
                'file_url' => $img,
            ];
            return $item;
        }, $imageUrls);
        logToDiscord('sync image : ' . print_r($tempsData, true));
        File::query()->where('type_id', $typeId)->where('type', $fileType)->delete();
        File::insert($tempsData);
        return $imageUrls;
    }

    public function downloadImageThenSave($files, $typeId, $fileType)
    {
        $imageUrls = [];
        $productThumbNailUrl = '';
        foreach ($files as $file) {
            try {
                $file = trim($file);
                // $fileContent = file_get_contents($file);
                $fileContent = $this->downloadFileWithCurl($file);
                if(empty($fileContent)){
                    continue;
                }
                $newFileName = microtime(true) . '.webp';
                // Nén ảnh và giảm độ phân giải
                $compressedImage = Image::make($fileContent)
                    // Nếu không muốn resize ảnh thì comment dòng này
                    ->resize(800, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->encode('webp', 80); // Mã hóa sang định dạng WebP với chất lượng 80%
                $this->storeFile($newFileName, $compressedImage);
                $imageUrl = $this->getFileUrl($newFileName);
                if (!empty($productThumbNailUrl) && $productThumbNailUrl == $file) {
                    $productThumbNailUrl = $imageUrl;
                }
                $imageUrls[] = $imageUrl;
            } catch (\Throwable $e) {
                Log::debug('Error in save image : ' .$e->getMessage());
                continue;
            }
        }

        $tempsData = array_map(function ($img) use ($typeId, $fileType) {
            $item = [
                'type_id' => $typeId,
                'type' => $fileType,
                'file_url' => $img,
            ];
            return $item;
        }, $imageUrls);

        File::insert($tempsData);
        return $imageUrls;
    }

    private function downloadFileWithCurl($url)
    {
        $ch = curl_init();

        // Thiết lập các tùy chọn cURL
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false, // Có thể bỏ tùy chọn này trong môi trường production
            CURLOPT_TIMEOUT => 30, // Timeout sau 30 giây
            CURLOPT_CONNECTTIMEOUT => 10, // Timeout kết nối sau 10 giây
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36', // User agent giả
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        // Ghi log lỗi nếu có
        if ($error) {
            Log::debug('Error in downloadFileWithCurl : ' .print_r($error, true));
        }

        // Kiểm tra HTTP status code
        if ($httpCode != 200) {
            Log::debug('Error in downloadFileWithCurl : ' .$httpCode);
            return false;
        }

        return $response;
    }

    public function deleteFilesByUrl(array $fileUrls)
    {
        $fileNames = array_map(function ($fileUrl) {
            return str_replace($this->getFileUrl(''), '', $fileUrl);
        }, $fileUrls);

        try {
            // Filter out non-existent files in a single call
            $existingFiles = array_filter($fileNames, function ($fileName) {
                return $this->checkFileExists($fileName);
            });

            // Delete all existing files in one operation
            Storage::disk($this->storage)->delete($existingFiles);
        } catch (\Throwable $e) {
            logException($e, __FUNCTION__);
        }
    }
}
