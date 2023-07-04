<?php
/* file: curl请求类
Created by wanghong<1204772286@qq.com>
Date: 2021-02-22 */

namespace utils;

class Curl{

    /* 
    请求封装-curl_request
    $url -string 请求地址
    $method -string 请求方式，默认GET
    $headers -array 请求头，默认[]
    $bodys -array 请求体，默认[]
    $json -boolean 对请求体进行json_encode处理，默认false
    return $response 请求返回值
    */
    public static function curl_request($url, $method = 'GET', $headers = [], $bodys = [], $json=false)
    {
        if($json==false){
            $bodys=json_encode($bodys);
        }
        // 创建连接
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        // 发送请求
        $response = curl_exec($curl);
        if($json && is_string($response)){
            $response=json_decode($response,true);
        }
        curl_close($curl);
        return $response;
    }

    /*
    get请求-curl_get
    $url -string 请求地址
    $json -boolean 对返回值进行json_decode处理，默认true进行处理成array
    */
    public static function curl_get($url,$json=true)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//跳过证书检查、
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $return = curl_exec($curl);
        curl_close ( $curl );
        if($json){
            return json_decode($return, true);
        }else{
            return $return;
        }
        
    }

    /* 
    POST请求-curl_post
    $url -string 请求地址
    $params - json 请求参数
    $rj -boolean 对返回值进行json_decode处理，默认true进行处理成array
    $headers -string 请求头
    */
    public static function curl_post($url,$params,$rj=true,$headers=''){
        if(!$headers){
            $headers=array(
                "Content-Type:application/x-www-form-urlencoded",
            );
        }
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $params );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 60 );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        if($rj){
            return json_decode($result,true);
        }else{
            return $result;
        }

    }

}