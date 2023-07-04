<?php
/**
 * Created by PhpStorm
 * User Julyssn
 * Date 2022/11/16 16:52
 */

namespace utils;

use Exception;
use think\facade\Config;

class CheckTicket
{


    /**
     * @param $ticket
     * @return false|string|array
     */
    public static function check($ticket)
    {
        Config::set([
            'public_key' => "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAv9Tc4Ap1zvbUKSiz7kQ3
wAvb4mrw4zAgViNzOVUkDZwqPTmx2pzPcUpNrh6qTX4JMwoTDRsu96M2a9DYv8iH
qjzU0yw3BfJFC4TZNVYCqD8ULVdiMutZeiAfpkx5jGjLGGXqgVFleQ8nmEE5yFdl
WUTyXjkfCENPdxeiBEp7aqqfKLv3U3t9OssoEYZYSpc+iZbSCyD9kIg8jxFTE2I2
VFl+9ec0Hl9k7R9CIXaO011oI9RVoauZNxgtUXauvU7GGQjsVHEcBj8qvDhLWVA7
MrKA9tkKDwyXDlHdBNtLAfwVgn7d7NkveqI8Qh2k7tXZhoP2txE9AiO9lIf7G4Pa
RQIDAQAB
-----END PUBLIC KEY-----"
        ], 'cipher');

        $rsa = new Rsa(new Config());

        try {
            $data = $rsa->publicDecrypt($ticket);
            if ($data) {
                return $data;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

}