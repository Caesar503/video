<?php

namespace App\Http\Controllers\Cron;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use OSS\OssClient;
use OSS\Core\OSSException;
class CronController extends Controller
{
    public function uploadFile()
    {
        $file_d = storage_path('app/public/files');
        $filename = scandir($file_d);
//        print_r($filename);die;
        foreach($filename as $k=>$v){
            if($v=='.'||$v=='..'){
                continue;
            }
            //获取文件后缀
            $suffix =  pathinfo($v,PATHINFO_EXTENSION);
            //实例化oss
            $ossClient = new OssClient(env('ACCESSKEYID'),env('ACCESSKEYSECRET'),env('ENDPOINT'));

            //项目中文件地址
            $address = storage_path('app/public/files').'/'.$v;

            //上传后的文件名字
            $filename = substr(rand(1,99999).Str::random(9),3,8).'.'.$suffix;

            try {
                //上传
                $res = $ossClient->uploadFile(env('BUCKET'),'img/'.$filename,$address);
            } catch (OssException $e) {
                //抛出异常
                print $e->getMessage();
            }
            echo "文件上传成功：".$address;
            unlink($address);
            die;
        }
    }
}
