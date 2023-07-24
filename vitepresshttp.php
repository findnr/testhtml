<?php
$http = new Swoole\Http\Server('0.0.0.0', 9501);

swoole_timer_tick(600000, function () {
    exec('git pull origin master');
});

$http->on('start', function ($server) {
    echo "Swoole http server is started at http://0.0.0.0:9501\n";
});
$http->on('request', function ($request, $response) {
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }
    $web = './docs/.vitepress/dist';
    $path = $request->server['path_info'];
    $content = '';
    $file_url = '';
    $arr = explode('/', $path);
    if ($arr[0] == '*') {
        $response->end($content);
        return;
    }
    $len = count($arr);
    $end_path = $arr[$len - 1];
    if ($end_path == '') {
        $file_url = $web . $path . 'index.html';
    } else {
        $file_url = $web . $path;
    }
    $arr = explode('.', $file_url);
    $len = count($arr);
    $str = $arr[$len - 1];
    switch ($str) {
        case 'js':
            $response->header('Content-Type', 'application/x-javascript');
            break;
        case 'css':
            $response->header('Content-Type', 'text/css');
            break;
        case 'html':
            $response->header('Content-Type', 'text/html');
            break;
        default:
            $response->header('Content-Type', 'text/plain');
            # code...
            break;
    }

    if (is_file($file_url)) {
        $content = file_get_contents($file_url);
    }
    $response->end($content);
    $content = '';
});

$http->start();
