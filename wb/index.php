<?php

//header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
//header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');


//[8000, '数据库错误', '']
namespace a11;

function executeRequest($controller, $method = 'get', $token = '', $data = [])
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'http://wb.cn/api' . $controller,
        CURLOPT_RETURNTRANSFER => true
    ]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['token:' . $token]);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if (is_array($data)) {
        $data = json_encode($data);
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function exec()
{
    $res = executeRequest(
        '/login',
        'post',
        '',
        [
            'password' => 'hello5',
            'username' => '123456',
//            'email' => '1'
        ]
    );
    echo 'res--' . $res;
//    $data = json_decode($res, true);
//    if ($data) {
//        print_r($data);
//    } else {
//        var_dump($data);
//    }
}

//exec();


//$res = uniqid(mt_rand(1000000, 999999999), true);
//echo $res.PHP_EOL;
//$res = uniqid(mt_rand(1, 100000), true);
//echo $res.PHP_EOL;
//echo strlen($res).PHP_EOL;









