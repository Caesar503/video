<?php

namespace App\Http\Controllers\OSS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OSS\OssClient;
use Illuminate\Support\Str;
class OSSController extends Controller
{
    //上传文本文件
    public function text()
    {
        $accesskey_id = "LTAIgsZcf4gTfAUS";
        $accessley_secret = "MIfCVhjdCWIbuiPpxaWX2syybGmOEv";
        $end_point = "oss-cn-beijing.aliyuncs.com";
        $ossClient = new OssClient($accesskey_id,$accessley_secret,$end_point);
//        dump($ossClient);
        $res = $ossClient->putObject('a1809','first.txt','hello everybody!!!');
        dump($res);
    }
    //上传本地文件
    public function uploadFile()
    {
        $ossClient = new OssClient(env('ACCESSKEYID'),env('ACCESSKEYSECRET'),env('ENDPOINT'));
//        dump($ossClient);
        $filename = substr(rand(1,99999).Str::random(9),3,8).'.jpg';
        $path = '2A31A3DA00F9619175D1264CA932B4D1.jpg';
        $res = $ossClient->uploadFile('a1809',$filename,$path);
        dump($res);
    }
}
