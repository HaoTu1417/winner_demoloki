<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncOpen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncopen:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get data hnx1';
    
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
        echo 'hanle sync open';
        while (true) {
            echo 'while true handle sync open';
            if ((date('D') != 'Sat' && date('D') != 'Sun')||true) {
                if ((date('H') == 9 && date('i') >= 0 && date('i') <= 1)||true) {
                    $data = DB::table('stocks')->get();
                    $arrayInfor = [];
                    foreach ($data as $item){
                        $dataStock = Http::get('https://bgapidatafeed.vps.com.vn/getliststocktrade/'.$item->stock);
                        $dataOpen = $dataStock->json();
                        if($dataOpen != null && $dataOpen[0] != null)
                        {
                            //  DB::table('stocks')->where('id', $item->id)->update([
                            //     'o' => $dataOpen[0]['lastPrice'],
                            //     'oupdate_at' => Carbon::now()
                            // ]);
                            array_push($arrayInfor, [
                                'sym' => $item->stock,
                                'stock_info' => $item->stock_info,
                                'stock' => $item->exchange,
                                'o' => $dataOpen[0]['lastPrice']
                            ]);
                        }
                    }
                    Redis::set('STOCK_INFO', json_encode($arrayInfor));
                    echo json_encode($arrayInfor);
                }
            }
            sleep(45);
        }
    }
}
