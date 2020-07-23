<?php


namespace common\utils;


trait Format
{
    final protected function toPair($data, $status = false)
    {
        return [$status, $data];
    }

    final protected function decodeToArray($string)
    {
        $array = json_decode($string, true);
        return is_array($array) ? $array : [];
    }

    final protected function encodeFromArray($array)
    {
        return json_encode(is_array($array) ? $array : []);
    }
}