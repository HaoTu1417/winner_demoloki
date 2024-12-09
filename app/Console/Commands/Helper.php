 <?php

use App\Customer;
use Illuminate\Support\Facades\Http;

class Helper
{
    public function random_number()
    {
        $characterSet = 'abcdefghij';
        $randomString = $characterSet[rand(0, strlen($characterSet) - 1)];
        return $randomString;
    }
    public function level_partner(){
        $data = [];
        array_push($data, ['level' => 0, 'total_customer' => 0, 'total_betting' => 0, 'total_recharge' => 0]);
        array_push($data, ['level' => 1, 'total_customer' => 10, 'total_betting' => 250000000, 'total_recharge' => 50000000]);
        array_push($data, ['level' => 2, 'total_customer' => 20, 'total_betting' => 500000000, 'total_recharge' => 100000000]);
        array_push($data, ['level' => 3, 'total_customer' => 40, 'total_betting' => 1000000000, 'total_recharge' => 200000000]);
        array_push($data, ['level' => 4, 'total_customer' => 60, 'total_betting' => 1500000000, 'total_recharge' => 300000000]);
        array_push($data, ['level' => 5, 'total_customer' => 80, 'total_betting' => 2000000000, 'total_recharge' => 400000000]);
        array_push($data, ['level' => 6, 'total_customer' => 100, 'total_betting' => 2500000000, 'total_recharge' => 500000000]);
        array_push($data, ['level' => 7, 'total_customer' => 150, 'total_betting' => 5000000000, 'total_recharge' => 1000000000]);
        array_push($data, ['level' => 8, 'total_customer' => 200, 'total_betting' => 15000000000, 'total_recharge' => 3000000000]);
        array_push($data, ['level' => 9, 'total_customer' => 300, 'total_betting' => 40000000000, 'total_recharge' => 8000000000]);
        array_push($data, ['level' => 10, 'total_customer' => 500, 'total_betting' => 75000000000, 'total_recharge' => 15000000000]);
        return $data;
    }
    
    public function ref_partner(){
        $data = [];
        array_push($data, ['0' => 1, '1' => 0.5, '2' => 0.25, '3' => 0.12, '4' => 0.062, '5' => 0.031]);
        array_push($data, ['0' => 1.1, '1' => 0.6, '2' => 0.33, '3' => 0.18, '4' => 0.1, '5' => 0.055]);
        array_push($data, ['0' => 1.2, '1' => 0.72, '2' => 0.43, '3' => 0.25, '4' => 0.15, '5' => 0.093]);
        array_push($data, ['0' => 1.3, '1' => 0.84, '2' => 0.54, '3' => 0.35, '4' => 0.23, '5' => 0.15]);
        array_push($data, ['0' => 1.4, '1' => 0.98, '2' => 0.68, '3' => 0.48, '4' => 0.33, '5' => 0.23]);
        array_push($data, ['0' => 1.5, '1' => 0.99, '2' => 0.7, '3' => 0.5, '4' => 0.35, '5' => 0.26]);
        array_push($data, ['0' => 1.6, '1' => 1, '2' => 0.73, '3' => 0.55, '4' => 0.39, '5' => 0.3]);
        array_push($data, ['0' => 1.7, '1' => 1.01, '2' => 0.75, '3' => 0.57, '4' => 0.41, '5' => 0.32]);
        array_push($data, ['0' => 1.8, '1' => 1.02, '2' => 0.77, '3' => 0.59, '4' => 0.43, '5' => 0.34]);
        array_push($data, ['0' => 1.9, '1' => 1.03, '2' => 0.79, '3' => 0.61, '4' => 0.45, '5' => 0.36]);
        array_push($data, ['0' => 2, '1' => 1.05, '2' => 0.82, '3' => 0.63, '4' => 0.48, '5' => 0.39]);
        return $data;
    }
    public function send_tele($content){
        $url = 'https://api.telegram.org/bot7223403342:AAHtiDMLBs_oJR5ly2ejJaSaSadGUN7w97I/sendMessage?chat_id=-1002187061643&text='.$content;
        Http::get($url);
    }
}