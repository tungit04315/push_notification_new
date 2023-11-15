<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SendNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cwb:runtest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function retry()
    {
        $delay = 5;
        $this->release($delay);
    }

    public function handle()
    {
        try {
            $log = new Logger('SendMail');
            $log->pushHandler(new StreamHandler('logs/SendMail.log', Logger::DEBUG));
            $fakeRequest = new Request([
                // 'name' => 'Áo thun nam cổ tròn màu đen',
                // 'description' => 'Áo thun nam cổ tròn màu đen, chất liệu cotton 100%, mềm mại, thấm hút mồ hôi tốt. Áo có thiết kế đơn giản, dễ phối đồ, phù hợp với nhiều hoàn cảnh.',
                // 'price' => 111.9,
                'image' => 'Notification Image',
                'icon' => 'Notification Icon',
            ]);
            $product_id = 81;
            //app('App\Http\Controllers\NotificationSendController')->sendNotification($fakeRequest);
            app('App\Http\Controllers\NotificationSendController')->sendNotificationProductId($fakeRequest, 81);
            
            // $users = User::all();
            // foreach ($users as $user) {
            //     $data = [
            //         'name' => $user->name,
            //         'product' => $fakeRequest->input('name'),
            //         "description" => $fakeRequest->input('description'),
            //         "price" => $fakeRequest->input('price'),
            //     ];

            //     Mail::send("mail", $data, function ($message) use ($user) {
            //         $message->to($user->email)->subject("Thông báo: Shopee ra mắt sản phẩm mới.");
            //         $this->info("Send Email Successfully");
            //     });
            // }
            //$log->info('Gửi thành công !');
        } catch (\Exception $th) {
            // $log->error('Gửi thất bại');
            // $this->retry();
        }

        // $data = [
        //     'name' => 'Tùng',
        //     'content' => 'Test Code',
        // ];

        // Mail::send("mail", $data, function ($message) {
        //     $message->to("tungit04315@gmail.com")->subject("Test Code Email Queues.");
        //     $this->info("Send Email Successfully");
        // });
    }
}
