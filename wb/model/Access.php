<?php


namespace model;


class Access
{
    public static $id = 'id';
    public static $expiresTime = 'expires_time';
    public static $count = 'count';
    public static $maxCount = 15;
    public static $defCount = 1;
    public static $ip = 'ip';
    public static $expires = 60;
    public static $tableName = ' wb_access ';
}