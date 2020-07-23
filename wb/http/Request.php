<?php


namespace http;

use DateTime;
use common\utils\Format;



trait Request
{
    use Format;

    final protected function passwordHash($password)
    {
        $hash = password_hash($password, PASSWORD_ARGON2I);
        if ($hash) {
            $resData = $this->toPair($hash, true);
        } else {
            $resData = $this->toPair([1002, '密码加密失败']);
        }
        return $resData;
    }

    final protected function isPasswordHash($password, $hash)
    {
        return password_verify($password, $hash);
    }

    final protected function createUniqueString($len)
    {
        $res = '';
        while (--$len >= 0) {
            $res .= dechex(mt_rand(0, 15));
        }
        return $res . uniqid('', true);
    }

    final protected function hasEmptyValue($values)
    {
        $status = false;
        foreach ($values as $it) {
            if (!$it) {
                $status = true;
                break;
            }
        }
        return $status;
    }

    final protected function extract($data, $keys)
    {
        $res = [];
        foreach ($keys as $k) {
            $res[$k] = $data[$k] ?? '';
        }
        return $res;
    }

    protected function retrieveRequestData($fields)
    {
        $fieldsData = $this->body($fields);
        if (empty($fieldsData) || $this->hasEmptyValue($fieldsData)) {
            $resData = $this->toPair([1003, 'request 数据项不能为空', '']);
        } else {
            $resData = $this->toPair($fieldsData, true);
        }
        return $resData;
    }

    final private function body($keys = [])
    {
        $str = file_get_contents("php://input");
        $data = $this->decodeToArray($str);
        foreach ($data as $k => $v) {
            $data[$k] = trim($v);
        }
        return empty($keys) ? $data : $this->extract($data, $keys);
    }

    final protected function timestamp()
    {
        return (new DateTime())->getTimestamp();
    }

    final protected function uriPath()
    {
        $name = 'REQUEST_URI';
        return ltrim(str_replace('/api', '', $this->request($name)), '/');
    }

    final protected function method()
    {
        $name = 'REQUEST_METHOD';
        return strtolower($this->request($name));
    }

    final protected function ip()
    {
        $name = 'REMOTE_ADDR';
        return $this->request($name);
    }

    final protected function client()
    {
        $name = 'HTTP_USER_AGENT';
        return $this->request($name);
    }

    protected function isExpires($time)
    {
        return $this->timestamp() >= +$time;
    }

    final private function request($name)
    {
        return $_SERVER[$name] ?? '';
    }
}