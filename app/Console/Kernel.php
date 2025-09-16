<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('zalo:sync-templates')
        ->daily()
        ->onOneServer()
        ->appendOutputTo(storage_path('logs/zalo_sync.log'))
        ->after(function () {
            Artisan::call('zalo:sync-template-detail', [
                '--only-active' => true,
            ]);

            file_put_contents(
                storage_path('logs/zalo_sync_detail.log'),
                Artisan::output(),
                FILE_APPEND
            );
        });

        $schedule->command('zalo:refresh-token')
        ->cron('0 */23 * * *')
        ->onOneServer()
        ->appendOutputTo(storage_path('logs/zalo_refresh.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
