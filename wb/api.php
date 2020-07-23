<?php

namespace entry;

use http\Route;
use Exception;

class Api
{
    public static function autoLoadRegister()
    {
        spl_autoload_register(function ($class_name) {
            $sp = '/';
            $path = dirname(__FILE__)
                . $sp . str_replace('\\', $sp, $class_name)
                . '.php';
            if (file_exists($path)) {
                include_once $path;
            }
        });
    }

    private static function routine($routeData)
    {
        try {
            list($controller, $method, $params) = $routeData;
            $returnData = call_user_func_array(
                [new $controller(), $method],
                $params
            );
        } catch (Exception $e) {
            $returnData = [false, [1000, '内部未知错误', '']];
        }
        return $returnData;
    }

    public static function response()
    {
        list($status, $routeData) = Route::execute();
        if ($status) {
            $resData = static::routine($routeData);
        } else {
            $resData = [$status, $routeData];
        }
        list($status, list($code, $message, $data)) = $resData;
        echo json_encode([
            'status' => $status || !$code,
            'code' => $code,
            'data' => $data,
            'message' => $message
        ]);
    }
}

Api::autoLoadRegister();
Api::response();


