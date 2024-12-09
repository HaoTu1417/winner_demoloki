<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Helper;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class WarningMoney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warning_money:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warning Money';

    protected $arrWallet = ['Hàng ngày', 'Trải nghiệm', 'Hàng tuần', 'Hàng tháng', 'Miễn lãi', 'VIP'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function calculate($stock, $customerId)
    {
        $stockData = DB::table('stocks')->where('stock', $stock)->first();
        // echo "stockData \n";
        // var_dump($stockData);
        // echo $stockData->exchange . '_' . $stockData->stock;
        $redisData = Redis::get($stockData->exchange . '_' . $stockData->stock);
        // echo "\nredisData \n";
        // var_dump($redisData);
        $stockRedis = json_decode($redisData);
        // echo "stock redis\n";
        // var_dump($stockRedis);
        $totalCal = DB::table('stock_tplus')
            ->where('customer_id', $customerId)
            ->where('stock', $stock)
            ->where('status', 1)
            ->where('type', 1)
            ->get();
        $totalAvaiable = DB::table('stock_tplus')
                        ->where('customer_id', $customerId)
                        ->where('stock', $stockData->stock)
                        ->where('status', 1)
                        ->whereRaw('((type = 1 and t >= 3) or type = 2)')
                        ->selectRaw('ifnull(sum(quantity),0) as quantity')
                        ->first();
        $quantity = $totalAvaiable->quantity;
        // echo 'quantity-'.$quantity;
        $totalSub = 0;
        $totalPrice = 0;
        $totalQuan = 0;
        //var_dump($stockRedis);
        if ($totalCal != null && count($totalCal) > 0) {
            foreach ($totalCal as $item) {
                $totalPrice += $item->price;
                $totalQuan += $item->quantity;
                // echo ' $totalPrice '.$totalPrice."\n";
                // echo '$totalQuan '.$totalQuan."\n";

            }
            $totalPriceTB = round($totalPrice / $totalQuan);
            // echo 'totalPriceTB-'.$totalPriceTB."\n";
            if ($totalPriceTB < $stockRedis->lastPrice * 1000) {
                $totalSub = ($totalPriceTB - $stockRedis->lastPrice) * $quantity;
                //  echo 'totalSub-'.$totalSub."\n";
            }
        }
        return $totalSub;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $helper = new Helper();
        while (true) {
            $data = DB::table('customer_debt')
                ->whereRaw('type != 2 and status = 1')
                ->groupBy('customer_id')
                ->selectRaw('customer_id, ifnull(sum(deposit),0) as deposit, ifnull(sum(money),0) as money')
                ->get();
            //echo "echo from db \n";
            //var_dump($data);
            if ($data != null && count($data) > 0) {
                foreach ($data as $item) {
                    $customerData = DB::table('customers')->where('id', $item->customer_id)->first();
                    $listStockAvaiable = DB::table('stock_tplus')
                        ->where('customer_id', $customerData->id)
                        ->where('status', 1)
                        ->where('type', 1)
                        ->select('stock')
                        ->distinct()
                        ->get();
                    $totalPrices = 0;
                    // echo "listStockAvailabel ";
                    // echo count($listStockAvaiable)."\n";
                    // echo $listStockAvaiable;
                    // echo "\n";
                    if ($listStockAvaiable != null && count($listStockAvaiable) > 0) {
                        // echo 'if (listStockAvaiable != null && count(listStockAvaiable) > 0) ';
                        // echo "\n";
                        foreach ($listStockAvaiable as $stockAvailable) {
                            // echo '$item->stock ';
                            // var_dump($stockAvailable->stock);
                            // echo "\n";

                            //  echo '$customerData->id ';
                            // var_dump($customerData->id);
                            // echo "\n";
                            $totalPrices += $this->calculate($stockAvailable->stock, $customerData->id);
                        }
                        //  echo "total price $totalPrices";
                        //  echo "\n";
                        
                    }
                    // echo "item \n";
                    // var_dump($item);
                    // echo "data ";
                    // var_dump($data);
                    if(($item->deposit * 0.85) <= $totalPrices){
                        $content = '';
                        $content .= '<p>Kính chào quý khách</p>';
                        $content .= '<p>Tài khoản của quý khách đang trong diện cảnh báo! Quý khách vui lòng nạp thêm tiền hoặc xử lý tài khoản thoát khỏi diện cảnh báo.</p>';
                        $content .= '<p>Xin chân thành cảm ơn.</p>';
                        Mail::to($email)->send(new SendMail('Gia hạn khoản vay', $content));
                        $helper->send_tele($customerData->email . ' đang trong diện cảnh báo thua lỗ quá 85% số tiền đã vay, vui lòng xử lý');
                    }
                }
            }
            sleep(600);
        }
    }
}
