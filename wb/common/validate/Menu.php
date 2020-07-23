<?php


namespace common\validate;


class Menu extends Base
{
    protected $mapMethod = [
        'order_no' => 'validateOrderNo',
        'path' => 'validatePath',
        'name' => 'validateName'
    ];

    protected function validateOrderNo($value)
    {
        return $this->validateField($value, 1, 11, '/^[\d]{1,9}$/');
    }

    protected function validatePath($value)
    {
        return $this->validateField($value, 3, 64);
    }

    protected function validateName($value)
    {
        return $this->validateField($value, 3, 32);
    }
}