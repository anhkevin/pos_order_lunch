<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Crontab;
use DB;
use Carbon\Carbon;
use App\Services\NotificationsService;

class NotificationBrowser extends Command
{
    protected $client;
    protected $headers = [];
    protected $notificationsService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:browser';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(NotificationsService $notificationsService)
    {
        parent::__construct();
        $this->notificationsService = $notificationsService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $list_crontab = Crontab::select('name', 'set_content')
        ->where('disabled', 0)
        ->where('set_group_laka', 0)
        ->where('set_hour', '>=', Carbon::now()->subMinute(3)->toTimeString())
        ->where('set_hour', '<=', Carbon::now()->addMinute(3)->toTimeString())
        ->where('set_day', 'like', '%'.Carbon::now()->format('N').'%')
        ->get();

        if ($list_crontab->isEmpty()) {
            return 0;
        }

        foreach ($list_crontab as $key => $value) {
            $this->notificationsService->sendNotifications($value->name, $value->set_content);
        }

        return 0;
    }
}
