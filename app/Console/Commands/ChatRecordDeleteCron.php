<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;

class ChatRecordDeleteCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ChatRecordDeleteCron:cron';

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
        \Log::info("Start Chat delete is working fine!");
        $chats = Chat::where('type', 'image')->where('created_at', '<=', date("Y-m-d", strtotime("-4 days")))->get();
        foreach($chats as $data)
        {
            $image_path = explode('/', $data->message_text);
            if (file_exists(public_path('images/chat/'.end($image_path)))) {
                @unlink(public_path('images/chat/'.end($image_path)));
            }
        }
        $chats = Chat::where('created_at', '<=', date("Y-m-d", strtotime("-4 days")))->delete();
        \Log::info("End Chat delete is working fine!");
    }
}
