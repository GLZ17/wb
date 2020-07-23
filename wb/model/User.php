<?php


namespace model;

class User
{
    public static $id = 'id';
    public static $status = 'status';
    public static $enStatus = '1';
    public static $disStatus = '0';
    public static $roleId = 'role_id';
    public static $defRoleId = '10000';
    public static $loginTime = 'login_time';
    public static $tokenExpiresTime = 'token_expires_time';
    public static $serial = 'serial';
    public static $serialExpiresTime = 'serial_expires_time';
    public static $accessId = 'access_id';
    public static $password = 'password';
    public static $username = 'username';
    public static $email = 'email';

    public static $tokenExpires = 60 * 60;
    public static $tokenName = 'token';
    public static $serialExpires = 60 * 3;

    public static $tableName = ' wb_user ';
}
