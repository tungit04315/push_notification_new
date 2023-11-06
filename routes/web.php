<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationSendController;
use App\Http\Controllers\ProductController;
use Laravel\Lumen\Routing\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();


$router->get('/home', function () {
    return view('home');
});

$router->get('/list-product', 'App\Http\Controllers\NotificationSendController@listProduct');
$router->get('/find-product', 'App\Http\Controllers\NotificationSendController@findByProductId');
$router->get('/list-product-page-filter', 'App\Http\Controllers\NotificationSendController@indexPage');

$router->post('/create-product', 'App\Http\Controllers\NotificationSendController@Save');
$router->put('/update-product', 'App\Http\Controllers\NotificationSendController@Update');
$router->delete('/delete-product', 'App\Http\Controllers\NotificationSendController@deleteSoft');
$router->post('/restore-product', 'App\Http\Controllers\NotificationSendController@restore');

//user
$router->put('/update-user', 'App\Http\Controllers\NotificationSendController@UpdateUser');

$router->post('/contact', ['uses' => 'ContactController@sendWebNotification']);

// Define a group of routes with 'auth' middleware
// $router->group(['middleware' => 'auth'], function () use ($router) {

$router->post('/store-token', 'App\Http\Controllers\NotificationSendController@updateDeviceToken')->name('store.token');

$router->post('/send-web-notification', 'App\Http\Controllers\NotificationSendController@sendNotification')->name('send.web-notification');
// });
