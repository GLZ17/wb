<?php


namespace common\utils;


trait Openssl
{
    final private function encrypt($str)
    {
        $params = array_merge([$str], $this->retrieveOpenssl());
        $output = call_user_func_array('openssl_encrypt', $params);
        return base64_encode($output);
    }

    final private function decrypt($str)
    {
        $params = array_merge([base64_decode($str)], $this->retrieveOpenssl());
        return call_user_func_array('openssl_decrypt', $params);
    }

    final private function retrieveOpenssl()
    {
        $encrypt_method = "AES-256-CBC";
        $secret_key = '+1*2/3-4.5*6|a@z(w)q';
        $secret_iv = '10`wb.cn-1.8.1*1@1';
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        return [$encrypt_method, $key, 0, $iv];
    }
}