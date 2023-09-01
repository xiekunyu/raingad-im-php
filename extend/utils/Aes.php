<?php

namespace utils;

class Aes
{
    /**
     * AES ECB 加密
     * @param $data string 加密数据
     * @param $key string 秘钥
     * @return string
     */
    public static function encrypt($data, $key)
    {
        $data = openssl_encrypt($data, 'aes-128-ecb', $key, OPENSSL_RAW_DATA);
        return base64_encode($data);
    }

    /**
     *  AES ECB 解密
     * @param $data string 解密数据
     * @param $key string 秘钥
     * @return false|string
     */
    public static function decrypt($data, $key)
    {
        $encrypted = base64_decode($data);
        return openssl_decrypt($encrypted, 'aes-128-ecb', $key, OPENSSL_RAW_DATA);
    }

}