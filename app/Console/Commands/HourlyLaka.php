<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\Crontab;
use DB;
use Carbon\Carbon;

class HourlyLaka extends Command
{
    protected $client;
    protected $headers = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hour:laka';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $list_crontab = Crontab::select('set_group_laka', 'set_content')
        ->where('disabled', 0)
        ->where('set_group_laka', '>', 0)
        ->where('set_hour', '>=', Carbon::now()->subMinute(5)->toTimeString())
        ->where('set_hour', '<=', Carbon::now()->addMinute(5)->toTimeString())
        ->where('set_day', 'like', '%'.Carbon::now()->format('N').'%')
        ->get();

        if ($list_crontab->isEmpty()) {
            return 0;
        }

        $this->headers = [         
            "accept" => "application/json",
            "accept-language" => "en-US,en;q=0.9,ja;q=0.8,vi;q=0.7",
            "content-type" => "application/json;charset=UTF-8",
            "sec-ch-ua" => "\"Google Chrome\";v=\"105\", \"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"105\"",
            "sec-ch-ua-mobile" => "?0",
            "sec-ch-ua-platform" => "\"Windows\"",
            "sec-fetch-dest" => "empty",
            "sec-fetch-mode" => "cors",
            "sec-fetch-site" => "same-site",
            "Referer" => "https://laka.lampart-vn.com/",
            "Referrer-Policy" => "strict-origin-when-cross-origin"
        ];

        $this->client = new Client([
            'base_uri'  => 'https://laka.lampart-vn.com/',
            'headers' => $this->headers
        ]);

        $response_login = $this->client->request('POST', 'https://laka.lampart-vn.com:9443/api/v1/user/login', [
            'form_params' => [
                'email' => env('LAKA_USER_EMAIL'),
                'password' => env('LAKA_USER_PASS'),
            ]
        ])->getBody(); 

        $json_login = json_decode($response_login, true);
        if (!empty($json_login["data"]["token"])) {
            foreach ($list_crontab as $key => $value) {
                $this->sent_laka($json_login["data"]["token"], $value->set_group_laka, $value->set_content);
            }
        }

        return 0;
    }

    private function sent_laka($token, $room, $message)
    {
        if (!empty($token) && !empty($room) && !empty($message)) {
            $this->headers = [         
                "accept" => "application/json",
                "authorization" => "Bearer " . $token,
                "accept-language" => "en-US,en;q=0.9,ja;q=0.8,vi;q=0.7",
                "content-type" => "application/json;charset=UTF-8",
                "sec-ch-ua" => "\"Google Chrome\";v=\"105\", \"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"105\"",
                "sec-ch-ua-mobile" => "?0",
                "sec-ch-ua-platform" => "\"Windows\"",
                "sec-fetch-dest" => "empty",
                "sec-fetch-mode" => "cors",
                "sec-fetch-site" => "same-site",
                "Referer" => "https://laka.lampart-vn.com/",
                "Referrer-Policy" => "strict-origin-when-cross-origin"
            ];
    
            $this->client = new Client([
                'base_uri'  => 'https://laka.lampart-vn.com/',
                'headers' => $this->headers
            ]);

            $this->client->request('POST', 'https://laka.lampart-vn.com:9443/api/v2/message/sent', [
                'form_params' => [
                    'message_id' => null,
                    'message' => $message,
                    'status' => 0,
                    'room_id' => $room,
                    'timestamp' => time(),
                    'reaction' => [],
                    'user_info'  => [
                        'id' => 69,
                        'name' => 'Phan Tien Anh [PG]',
                        'nick_name' => null,
                        'cover_img' => null,
                        'company' => 'LAMPART Co., Ltd.',
                        'phone' => '',
                        'address' => '',
                        'email' => 'tien_anh@lampart-vn.com',
                        'icon_img' => 'https://laka-pub.s3-ap-southeast-1.amazonaws.com/public_file/f62edf6e70d0a1df0e29490b3d3a4afd',
                        'email_verified_at' => '2020-06-26 09:42:49',
                        'token_file' => '1879833545632039bf069808.39632211',
                        'permission' => 0,
                        'is_bot' => 0,
                        'user_type' => 0,
                        'disabled' => 0,
                        'deleted_at' => null,
                        'company_id' => 1
                    ],
                    'key_replace' => '69_69_1',
                    'new_day' => false
                ]
            ]);
        }
    }
}
