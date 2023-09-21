<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoArtisanRunCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoArtisanRun:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Auto Artisan Cron Start is working fine!");
        \Artisan::call('config:cache');
        \Log::info("config:cache ".date('Y-m-d h:i A'));
        \Artisan::call('cache:clear');
        \Log::info("cache:clear ".date('Y-m-d h:i A'));
        \Artisan::call('route:clear');
        \Log::info("route:clear ".date('Y-m-d h:i A'));
        \Artisan::call('optimize:clear');
        \Log::info("optimize:clear ".date('Y-m-d h:i A'));
        \Log::info("Auto Artisan Cron End is working fine!");
    }
}
