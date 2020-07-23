<?php


namespace http;

use controller\user\common\Token;

final class Route extends Token
{
    private static $routes = [
        'register' => ['post' => ['user/register', 'index']],
        'login' => ['post' => ['user/login', 'index']],
        'logout' => ['post' => ['user/logout', 'index']],
        'password/appeal' => ['post' => ['user/password/appeal', 'index']],
        'password/retrieve' => ['post' => ['user/password/retrieve', 'index']],
        'menu/([\d]+)' => [
            'put' => ['admin/menu', 'update'],
            'delete' => ['admin/menu', 'remove'],
        ],
        'menu' => [
            'get' => ['admin/menu', 'index'],
            'post' => ['admin/menu', 'create'],
        ]
    ];
    private static $excludes = [
        'register', 'login', 'password/appeal', 'password/retrieve'
    ];

    public static function execute()
    {
        $returnStatus = false;
        $route = new Route();
        list($status, $routeData) = $route->retrieveRouteData();
        if (!$status) {
            $returnData = [1001, 'route 接口不存在', ''];
            goto end;
        }
        list($routePath, $params, list($controllerPath, $method)) = $routeData;
        $controller = $route->convertController($controllerPath);
        if ($route->isExclude($routePath)) {
            $returnStatus = true;
            $returnData = [$controller, $method, [[], $params]];
            goto end;
        }
        list($status, $userInfo) = $route->validateToken();
        if (!$status) {
            $returnData = $userInfo;
            goto end;
        }
        $returnData = [$controller, $method, [$userInfo, $params]];
        $returnStatus = true;
        end:
        return $route->toPair($returnData, $returnStatus);
    }

    private function retrieveRouteData()
    {
        $uriPath = $this->uriPath();
        $method = $this->method();
        foreach (static::$routes as $path => $verbs) {
            $regexp = '/^' . str_replace('/', '\\/', ltrim($path, '/')) . '\\/?/i';
            if (preg_match($regexp, $uriPath, $matches)) {
                foreach ($verbs as $verb => $routeData) {
                    if ($verb === $method) {
                        $params = array_slice($matches, 1, count($matches));
                        return $this->toPair([$path, $params, $routeData], true);
                    }
                }
            }
        }
        return $resData = $this->toPair([]);
    }

    private function convertController($path)
    {
        $paths = explode('/', 'controller/' . ltrim($path, '/'));
        $pos = count($paths) - 1;
        $paths[$pos] = ucwords($paths[$pos]);
        return implode('\\', $paths);
    }

    private function isExclude($routePath)
    {
        return in_array($routePath, static::$excludes);
    }
}