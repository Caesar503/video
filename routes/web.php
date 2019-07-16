<?php

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

Route::get('/', function () {
    phpinfo();
});
//Route::get('/','Test\TestController@test');

Route::get('/oss/text','OSS\OSSController@text');

Route::get('/oss/file','OSS\OSSController@uploadFile');
//上传到oss
Route::get('/ofile','Cron\CronController@uploadFile');
//视频详情展示
Route::get('/detail/{id}','Det\DetController@show');

//视频上传 异步回调
Route::post('/notify/oss','OSS\NoController@nofity');


Route::get('/zb', function () {
    return view('detail.zb');
});