<?php


namespace common\validate;

class User extends Base
{
    protected $mapMethod = [
        'username' => 'validateUsername',
        'password' => 'validatePassword',
        'email' => 'validateEmail',
        'serial' => 'validateSerial'
    ];

    protected function validateUsername($value)
    {
        return $this->validateField($value, 3, 64);
    }

    protected function validatePassword($value)
    {
        return $this->validateField($value, 6, 128);
    }

    protected function validateEmail($value)
    {
        $regexp = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/';;
        return $this->validateField($value, 5, 64, $regexp);
    }

    protected function validateSerial($value)
    {
        return $this->validateField($value, 32, 32);
    }
}