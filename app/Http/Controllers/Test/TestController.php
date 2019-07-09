<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Test;
use MongoDB\Client;
class TestController extends Controller
{
    public function test()
    {
        //UA
        $uagent = $_SERVER['HTTP_USER_AGENT'];
        //时间
        $time = time();
        //客户端ip
        $ip = $_SERVER['HTTP_X_REAL_IP'];
        //url
        $uri = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

        $arr = ['uagent'=>$uagent,'acc_time'=>$time,'hip'=>$ip,'url'=>$uri];
        $res = Test::insert($arr);

        $mongo = new Client("mongodb://192.168.253.240:27017");
        $db = $mongo->a1809->test1;
//        dd($db);
        $res1 = $db->insertOne($arr);
        if($res1 && $res){
            return view('welcome');
        }else{
            echo "添加mysql或者mongodb出错！";
        }
    }
}
