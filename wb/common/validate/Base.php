<?php


namespace common\validate;

use common\utils\Format;

class Base
{
    protected $mapMethod = [];
    use Format;

    public static function validate($data)
    {
        $status = true;
        $ob = new static();
        foreach ($data as $field => $value) {
            $methodName = $ob->mapMethod[$field] ?? '';
            if (!$methodName) {
                continue;
            }
            $status = call_user_func([$ob, $methodName], $value);
            if (!$status) {
                break;
            }
        }
        $resData = $status ? $data : [1301, 'user/validate 数据无效', ''];
        return $ob->toPair($resData, $status);
    }

    protected function validateField($value, $min, $max, $regexp = '')
    {
        $len = strlen($value);
        $status = $len >= $min && $max >= $len;
        if ($status && $regexp) {
            $status = !!preg_match($regexp, $value);
        }
        return $status;
    }
}