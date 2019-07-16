<?php

namespace App\Http\Controllers\OSS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OSS\OssClient;
use Illuminate\Support\Str;
class NoController extends Controller
{
    //上传视频 异步回调
    public function notify()
    {
        $data = file_get_contents("php://input");

        $log_data = date("Y-M-D h:i:s").">>>>>>>>>>>>>>".$data;
        file_put_contents('logs/oss.log',$log_data,FILE_APPEND);

    }
}
