<?php

namespace App\Http\Controllers\OSS;

use http\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
include_once '/wwwroot/video/aliyun-openapi-php-sdk/aliyun-php-sdk-core/Config.php';
use Mts\Request\V20140618 as Mts;
use Illuminate\Support\Str;

class NoController extends Controller
{
    //上传视频 异步回调
    public function notify()
    {
        $data = file_get_contents("php://input");

        $log_data = date("Y-M-D h:i:s").">>>>>>>>>>>>>>".$data."\n\n";
        file_put_contents("logs/oss.log",$log_data,FILE_APPEND);

        $arr = json_decode($data,true);
        $fileData = base64_decode($arr['Message'],true);
        $fileDataDe = json_decode($fileData,true);
        $fileName = $fileDataDe['events'][0]['oss']['object']['key'];
//        dump($fileName);
        //创建AcsClient实例 (貌似好像是转码的客户端)
        $clientProfile = \DefaultProfile::getProfile('cn-beijing',env("ACCESSKEYID"),env("ACCESSKEYSECRET"));
        $client = new \DefaultAcsClient($clientProfile);
//        dump($client);
        //创建request，并设置参数

        $pipeline_id = '020afe1d4eb34e8fa974341b19053fdd';
        $template_id = 'S00000001-200010';
        $oss_location = 'oss-cn-beijing';
        $oss_bucket = $fileDataDe['events'][0]['oss']['bucket']['name'];
        $oss_input_object = $fileName;
        $oss_output_object = Str::random(8,14).'.hls';


        $request = new Mts\SubmitJobsRequest();
        $request->setAcceptFormat('JSON');
        //转码
        #Input
        $input = array('Location' => $oss_location,
            'Bucket' => $oss_bucket,
            'Object' => urlencode($oss_input_object)
        );
        $request->setInput(json_encode($input));
        #Output
        $output = array('OutputObject' => urlencode($oss_output_object));
        //格式
        $output['Container'] = array('Format' => 'hls');
        //视频
        $output['Video'] = array('Codec' =>'H.264',
            'Bitrate' => 1500,
            'Width' => 1280,
            'Fps' => 25);
        //音频
        $output['Audio'] = array('Codec' => 'AAC',
            'Bitrate' => 128,
            'Channels' => 2,
            'Samplerate' => 44100);
        //模板
        $output['TemplateId'] = $template_id;
        $outputs = array($output);
        $request->setOUtputs(json_encode($outputs));
        $request->setOutputBucket($oss_bucket);
        $request->setOutputLocation($oss_location);
        //管道
        $request->setPipelineId($pipeline_id);


        //发起请求处理请求
        try {
            $response = $client->getAcsResponse($request);
            print 'RequestId is:' . $response->{'RequestId'} . "\n";;
            if ($response->{'JobResultList'}->{'JobResult'}[0]->{'Success'}) {
                print 'JobId is:' .
                    $response->{'JobResultList'}->{'JobResult'}[0]->{'Job'}->{'JobId'} . "\n";
            } else {
                print 'SubmitJobs Failed code:' .
                    $response->{'JobResultList'}->{'JobResult'}[0]->{'Code'} .
                    ' message:' .
                    $response->{'JobResultList'}->{'JobResult'}[0]->{'Message'} . "\n";
            }
        } catch(ServerException $e) {
            print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
        } catch(ClientException $e) {
            print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
        }
    }
    public function test()
    {
        //获取上传文件名
        $data = "{\"TopicOwner\":\"1451350899628234\",\"Message\":\"eyJldmVudHMiOiBbewogICAgICAgICAgICAiZXZlbnROYW1lIjogIk9iamVjdENyZWF0ZWQ6UHV0T2JqZWN0IiwKICAgICAgICAgICAgImV2ZW50U291cmNlIjogImFjczpvc3MiLAogICAgICAgICAgICAiZXZlbnRUaW1lIjogIjIwMTktMDctMTZUMDk6MDY6MDEuMDAwWiIsCiAgICAgICAgICAgICJldmVudFZlcnNpb24iOiAiMS4wIiwKICAgICAgICAgICAgIm9zcyI6IHsKICAgICAgICAgICAgICAgICJidWNrZXQiOiB7CiAgICAgICAgICAgICAgICAgICAgImFybiI6ICJhY3M6b3NzOmNuLWJlaWppbmc6MTQ1MTM1MDg5OTYyODIzNDphMTgwOSIsCiAgICAgICAgICAgICAgICAgICAgIm5hbWUiOiAiYTE4MDkiLAogICAgICAgICAgICAgICAgICAgICJvd25lcklkZW50aXR5IjogIjE0NTEzNTA4OTk2MjgyMzQiLAogICAgICAgICAgICAgICAgICAgICJ2aXJ0dWFsQnVja2V0IjogIiJ9LAogICAgICAgICAgICAgICAgIm9iamVjdCI6IHsKICAgICAgICAgICAgICAgICAgICAiZGVsdGFTaXplIjogNjc5NDMxNSwKICAgICAgICAgICAgICAgICAgICAiZVRhZyI6ICJGNTdBMTg3OTJCODEzMzg4NDRBQkJCRjVCMTE4RjgwNSIsCiAgICAgICAgICAgICAgICAgICAgImtleSI6ICJmaWxlcy9lY2ExMjJjOTJmODIzNDM5NTY1NzA0Zjc4NDI2OTMyNi5tcDQiLAogICAgICAgICAgICAgICAgICAgICJzaXplIjogNjc5NDMxNX0sCiAgICAgICAgICAgICAgICAib3NzU2NoZW1hVmVyc2lvbiI6ICIxLjAiLAogICAgICAgICAgICAgICAgInJ1bGVJZCI6ICJvc3MxODA5In0sCiAgICAgICAgICAgICJyZWdpb24iOiAiY24tYmVpamluZyIsCiAgICAgICAgICAgICJyZXF1ZXN0UGFyYW1ldGVycyI6IHsic291cmNlSVBBZGRyZXNzIjogIjE3Mi4xNy4xMi4xMzYifSwKICAgICAgICAgICAgInJlc3BvbnNlRWxlbWVudHMiOiB7InJlcXVlc3RJZCI6ICI1RDJEOTM3OTg1N0RDREU3MDI4NjNEOEIifSwKICAgICAgICAgICAgInVzZXJJZGVudGl0eSI6IHsicHJpbmNpcGFsSWQiOiAiMjU1ODY3OTYyNjYyNDk2MzM3In19XX0=\",\"Subscriber\":\"1451350899628234\",\"PublishTime\":\"1563267961295\",\"SubscriptionName\":\"oss1809\",\"MessageMD5\":\"58110F876E495BA27F37DDBD3A06FC27\",\"TopicName\":\"oss1809\",\"MessageId\":\"586D90922A207FF47FC350EABDCF1949\"}";
        $arr = json_decode($data,true);
        $fileData = base64_decode($arr['Message'],true);
        $fileDataDe = json_decode($fileData,true);
        $fileName = $fileDataDe['events'][0]['oss']['object']['key'];
        dump($fileDataDe);
        //创建AcsClient实例 (貌似好像是转码的客户端)
        $clientProfile = \DefaultProfile::getProfile('cn-beijing',env("ACCESSKEYID"),env("ACCESSKEYSECRET"));
        $client = new \DefaultAcsClient($clientProfile);
        dump($client);
        //创建request，并设置参数

        $pipeline_id = '020afe1d4eb34e8fa974341b19053fdd';
        $template_id = 'S00000001-200010';
        $oss_location = 'oss-cn-beijing';
        $oss_bucket = $fileDataDe['events'][0]['oss']['bucket']['name'];
        $oss_input_object = $fileName;
        $oss_output_object = Str::random(8,14).'.hls';


        $request = new Mts\SubmitJobsRequest();
        $request->setAcceptFormat('JSON');
        //转码
        #Input
        $input = array('Location' => $oss_location,
            'Bucket' => $oss_bucket,
            'Object' => urlencode($oss_input_object)
        );
        $request->setInput(json_encode($input));
        #Output
        $output = array('OutputObject' => urlencode($oss_output_object));
        //格式
        $output['Container'] = array('Format' => 'hls');
        //视频
        $output['Video'] = array('Codec' =>'H.264',
            'Bitrate' => 1500,
            'Width' => 1280,
            'Fps' => 25);
        //音频
        $output['Audio'] = array('Codec' => 'AAC',
            'Bitrate' => 128,
            'Channels' => 2,
            'Samplerate' => 44100);
        //模板
        $output['TemplateId'] = $template_id;
        $outputs = array($output);
        $request->setOUtputs(json_encode($outputs));
        $request->setOutputBucket($oss_bucket);
        $request->setOutputLocation($oss_location);
        //管道
        $request->setPipelineId($pipeline_id);


        //发起请求处理请求
        try {
            $response = $client->getAcsResponse($request);
            print 'RequestId is:' . $response->{'RequestId'} . "\n";;
            if ($response->{'JobResultList'}->{'JobResult'}[0]->{'Success'}) {
                print 'JobId is:' .
                    $response->{'JobResultList'}->{'JobResult'}[0]->{'Job'}->{'JobId'} . "\n";
            } else {
                print 'SubmitJobs Failed code:' .
                    $response->{'JobResultList'}->{'JobResult'}[0]->{'Code'} .
                    ' message:' .
                    $response->{'JobResultList'}->{'JobResult'}[0]->{'Message'} . "\n";
            }
        } catch(ServerException $e) {
            print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
        } catch(ClientException $e) {
            print 'Error: ' . $e->getErrorCode() . ' Message: ' . $e->getMessage() . "\n";
        }
    }
}
