<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Handlers\Handler;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function(){
	return 'test works';
});


Route::post('/923486134:AAFdGgmm5zv3iDJC5rGvj_okOALT_HcLTUA/webhook', function(){
    try {
        $update = Telegram::commandsHandler(true);
        $updates = Telegram::getWebhookUpdates();

        // error_log($updates);

        $updates = json_decode($updates, true);

        if(array_key_exists('callback_query', $updates)){
            //handle callbacks;
            Handler::handleCallBack($updates);
        }else if(array_key_exists('message', $updates)){
            //handle message;
            Handler::handleMessage($updates);
        }
        return 'ok';
    } catch (\Throwable $th) {
        error_log($th);
    }
    return 'ok';
});
