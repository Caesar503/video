<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>视频展示</title>
</head>
<body>
    <h3>
        {{$v['title']}}
    </h3>
    <video src="{{env('LOHOST')}}{{$v['path']}}" controls="controls"></video>
</body>
</html>