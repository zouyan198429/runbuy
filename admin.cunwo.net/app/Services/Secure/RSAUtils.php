<?php

namespace App\Services\Secure;

/**
 * php rsa 加密，解密，签名，验签  https://www.codingsky.com/article/vQSJiNPqjU.html
 *
 * 由于对接第三方机构使用的是Java版本的rsa加解密方法，所有刚开始在网上搜到很多PHP版本的rsa加解密，但是对接java大多都不适用。
 *
 * 以下php版本是适用于对接java接口，java适用密钥再php语言使用是需要添加
 *
 *
 * -----BEGIN CERTIFICATE-----
 *
 * -----END CERTIFICATE-----
 * 　
 * 使用密钥：
 * 加密公钥 public_key.cer
 *
 * 解密私钥 private_key.key
 *
 * 签名私钥 sign_key.key
 *
 * 验签公钥 verify.cer
 *
 *
 * 注意：
 *
 * 有时候用base64_encode加密后,以GET的形式传到其他页面,用base64_decode解密的时候,出现乱码.
 *
 * 遇到这个问题的时候,我就纳闷了,为什么有一些能正确解密,但是有一些却出现乱码呢?
 *
 * 后来经过检查,发现有一些中文字符,用GET形式传过来的时候,+号会被替换成空格.
 *
 * 为了防止出现乱码的情况,我做了一步替换,然后再解密,果然,乱码的问题,不复存在了!
 *
 * 比如你以GET的形式传过来一个oid变量,那么解密还原的时候,先用+号替换空格.那么输出就正常了.
 *
 * 如下: $oid=base64_decode(str_replace(" ","+",$_GET[oid]));
 *
 *
 */

class RSAUtils
{

    //加密公钥
    function redPukey()
    {
        //拼接加密公钥路径
        $encryptionKeyPath="D:/encryptions.cer";
        $encryptionKey4Server = file_get_contents($encryptionKeyPath);

        $pem = chunk_split(base64_encode($encryptionKey4Server),64,"\n");//转换为pem格式的公钥
        $pem = "-----BEGIN CERTIFICATE-----\n".$pem."-----END CERTIFICATE-----\n";
        $publicKey = openssl_pkey_get_public($pem);
        return $publicKey;
    }

    //解密私钥
    function redPikey()
    {
        //拼接解密私钥路径
        $decryptKeyPath="D:/decrypts.key";
        $decryptKey4Server = file_get_contents($decryptKeyPath);

        $pem = chunk_split($decryptKey4Server,64,"\n");//转换为pem格式的私钥
        $pem = "-----BEGIN PRIVATE KEY-----\n".$pem."-----END PRIVATE KEY-----\n";
        $privateKey = openssl_pkey_get_private($pem);
        return $privateKey;
    }

    //签名私钥
    function redSignkey()
    {
        //拼接签名路径
        $signKeyPath="D:/DEMO/sign.key";
        $signKey4Server = file_get_contents($signKeyPath);

        $pem = chunk_split($signKey4Server,64,"\n");//转换为pem格式的私钥
        $pem = "-----BEGIN PRIVATE KEY-----\n".$pem."-----END PRIVATE KEY-----\n";
        $signKey = openssl_pkey_get_private($pem);
        return $signKey;
    }

    //验签公钥
    function redVerifykey()
    {
        //拼接验签路径
        $verifyKeyPath="D:/DEMO/verify.cer";
        $verifyKey4Server = file_get_contents($verifyKeyPath);

        $pem = chunk_split(base64_encode($verifyKey4Server),64,"\n");//转换为pem格式的公钥
        $pem = "-----BEGIN CERTIFICATE-----\n".$pem."-----END CERTIFICATE-----\n";
        $verifyKey = openssl_pkey_get_public($pem);
        return $verifyKey;
    }

    //公钥加密
    function pubkeyEncrypt($source_data, $pu_key) {
        $data = "";
        $dataArray = str_split($source_data, 117);
        foreach ($dataArray as $value) {
            $encryptedTemp = "";
            openssl_public_encrypt($value,$encryptedTemp,$pu_key);//公钥加密
            $data .= base64_encode($encryptedTemp);
        }
        return $data;
    }

    //私钥解密
    function pikeyDecrypt($eccryptData,$decryptKey) {
        $decrypted = "";
        $decodeStr = base64_decode($eccryptData);
        $enArray = str_split($decodeStr, 256);

        foreach ($enArray as $va) {
            openssl_private_decrypt($va,$decryptedTemp,$decryptKey);//私钥解密
            $decrypted .= $decryptedTemp;
        }
        return $decrypted;
    }
}