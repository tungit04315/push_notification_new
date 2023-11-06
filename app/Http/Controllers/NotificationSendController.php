<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PushNotification;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationSendController extends Controller
{
    public function updateDeviceToken(Request $request)
    {
        Auth::user()->device_token = $request->token;

        Auth::user()->save();

        return response()->json(['Token successfully stored.']);
    }

    public function sendNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = User::whereNotNull('device_token')->pluck('device_token')->all();

        $serverKey = 'AAAAiEIvS38:APA91bG698tMOSMYlzfwnJxTcYfau3wLfOLpQkd41kgs8UQXhTtzG99fC5dSKHqeXIIRjrGj5-rFiqJJoK3QlfkM0kI9He5xmwESHhMXiYwsk0DWxtIs68AqfXSlUYIKw68j_U-BA74X'; // ADD SERVER KEY HERE PROVIDED BY FCM

        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->name,
                "body" => $request->description,
                "price" => $request->price,
                "image" => $request->image,
                "icon" => $request->icon,
            ],
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        $result = curl_exec($ch);

        if ($result === false) {
            die('Curl failed: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON decoding error: ' . json_last_error_msg());
        } else {

            $this->saveProduct($request);

            $this->savePushNotification($result);

            error_log(json_encode($result));
        }

        //return dd($result);
        return view('home');
    }
    
    public function saveProduct(Request $require)
    {

        $require->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|between:30,155',
            'price' => 'required|numeric|between:0,99999999.99',
        ]);

        $product = new Product();
        $product->name = $require->name;
        $product->description = $require->description;
        $product->price = $require->price;
        $product->save();
       
        $users = User::all();
        foreach ($users as $user) {
            $data = [
                'name' => $user->name,
                'product' => $require->input('name'),
                "description" => $require->input('description'),
                "price" => $require->input('price'),
            ];

            Mail::send("mail", $data, function ($message) use ($user) {
                $message->to($user->email)->subject("Thông báo: Shopee ra mắt sản phẩm mới.");
                //$this->info("Send Email Successfully");
            });
        } 
    }

    public function savePushNotification($result)
    {
        $pushNotification = new PushNotification();

        $pushNotification->multicast_id = $result['multicast_id'];
        $pushNotification->success = $result['success'];
        $pushNotification->failure = $result['failure'];
        $pushNotification->canonical_ids = $result['canonical_ids'];
        $pushNotification->message_id = $result['results'][0]['message_id'];

        $pushNotification->save();
    }

    public function Save(Request $require)
    {

        $require->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|between:30,155',
            'price' => 'required|numeric|between:0,99999999.99',
        ]);

        $product = new Product();
        $product->name = $require->input('name');
        $product->description = $require->input('description');
        $product->price = $require->input('price');
        $product->save();

        return redirect('/list-product');
    }

    public function Update(Request $request)
    {
        $id = $request->input('id');
        $product = Product::findOrFail($id);

        if (!$product) {
            return response()->json(['message' => 'Thông báo không tồn tại'], 404);
        } else {

            $request->validate([
                'name' => 'required|string|max:50',
                'description' => 'required|string|between:30,155',
                'price' => 'required|numeric|between:0,99999999.99',
            ]);

            $product->name = $request->input('name');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->save();
            return redirect('/list-product');
        }
    }

    public function listProduct()
    {
        return response()->json(Product::all());
    }

    public function findByProductId(Request $request)
    {
        try {
            $id = $request->input('id');

            $product = Product::Where('id', $id)->get();

            if ($product == null) {
                return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
            }

            return response()->json($product);
        } catch (\Throwable $th) {
            return response()->json($th);
        }
    }

    public function deleteSoft(Request $request)
    {
        $id = $request->input('id');

        $product = Product::findOrFail($id);

        if ($product == null) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Xóa mềm sản phẩm thành công'], 200);
    }

    public function restore(Request $request, $id)
    {
        $id = $request->input('id');

        $product = Product::findOrFail($id);
        $product->save();

        return response()->json([
            'success' => "Khôi phục dữ liệu sản phẩm thành công",
        ], 200);
    }

    public function indexPage(Request $request)
    {

        $perPage = $request->input('per_page', 5);
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', $request->input('name'));
        }

        if ($request->has('price')) {
            $priceFilter = $request->input('price');
            switch ($priceFilter) {
                case 'highest':
                    $query->orderBy('price', 'desc');
                    break;
                case 'lowest':
                    $query->orderBy('price', 'asc');
                    break;
            }
        }

        // if ($request->has('newest')) {
        //     $query->orderBy('created_at', 'desc');
        // }

        // if ($request->has('oldest')) {
        //     $query->orderBy('created_at', 'asc');
        // }

        $product = $query->paginate($perPage);

        return response()->json($product);
    }

    public function UpdateUser(Request $request)
    {
        $id = $request->input('id');
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Thông báo không tồn tại'], 404);
        } else {

            $rules = [
                'name' => 'required|string|min:6|max:30',
                'email' => 'required|email|unique:users,email,' . $id,
            ];
        
            if ($request->has('password')) {
                $rules['password'] = 'required|string|min:8';
            }
        
            $request->validate($rules);

            $user->name = $request->input('name');
            $user->email = $request->input('email');

            
            if ($request->has('password')) {
                $request->validate([
                    'password' => 'required|min:8',
                ]);

                $user->password = Hash::make($request->password);
            }

            $user->save();
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        }
    }
}