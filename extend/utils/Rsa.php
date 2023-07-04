<?php
declare (strict_types=1);

namespace utils;

use think\facade\Config;

class Rsa
{
    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /**
     * 构造方法
     * @access public
     */
    public function __construct(Config $config)
    {
        $this->config = $config::get('cipher');
    }

    /**
     * 私钥加密
     * @param string $data 要加密的数据
     * @return false|string
     */
    public function privateEncrypt(string $data): string
    {
        $private_key = openssl_pkey_get_private($this->config['private_key']);

        openssl_private_encrypt($data, $encrypted, $private_key);
        return base64_encode($encrypted);
    }

    /**
     * 私钥解密
     * @param string $data
     * @return false|string
     */
    public function privateDecrypt(string $data): ?string
    {
        $private_key = openssl_pkey_get_private($this->config['private_key']);

        openssl_private_decrypt(base64_decode($data), $decrypted, $private_key);
        return $decrypted;
    }

    /**
     * 公钥加密
     * @param string $data
     * @return false|string
     */
    public function publicEncrypt(string $data): string
    {
        $public_key = openssl_pkey_get_public($this->config['public_key']);

        openssl_public_encrypt($data, $encrypted, $public_key);
        return base64_encode($encrypted);
    }

    /**
     * 公钥解密
     * @param string $data
     * @return false|string
     */
    public function publicDecrypt(string $data): ?string
    {
        $public_key = openssl_pkey_get_public($this->config['public_key']);

        openssl_public_decrypt(base64_decode($data), $decrypted, $public_key);
        return $decrypted;
    }
}