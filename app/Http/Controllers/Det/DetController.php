<?php

namespace App\Http\Controllers\Det;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Up;
class DetController extends Controller
{
    public function show($id)
    {
        $res = Up::where('id',$id)->first()->toArray();
//        dump($res);die;
        $data = [
            'v'=>$res
        ];
        return view('detail.v',$data);
    }
}
