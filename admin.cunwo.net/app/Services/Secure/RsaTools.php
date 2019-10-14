<?php
namespace App\Services\Secure;
/**
 * RSA加密  https://luchanglong.com.cn/?p=447
 * env:(PHP 4 >= 4.0.4, PHP 5, PHP 7)
 * 密钥对生成
 * 工具：OpenSSL
 * 生成私钥：genrsa -out rsa_private_key.pem 1024
 * 生成公钥：rsa -in rsa_private_key.pem -pubout -out rsa_public_key.pem
 * Author:luchanglong
 * Date:2017-12-14
 * **********************************************
 * * 私钥丢失将导致数据永久无法解密
 * *********************************************
 */

//header("Content-type: text/html; charset=utf-8");

/**
 *
 *
 *  $r=new RsaTools();
 *  $str="0123456789?><~!@#$%^&*()_+qwertyuiopasdfghjklzxcvbnm";
 *  echo '待加密：'.$str."<br>";
 *  $en=$r->rsaEncrypt($str);
 *  echo '加密后：'.$en.'<br>';
 *  $de=$r->rsaDecrypt($en);
 *  echo '解密后：'.$de;
 *
 */

class RsaTools
{
//私钥文件路径
    private $rsaPrivateKeyFilePath;

    //私钥值
    private $rsaPrivateKey;

    //公钥文件路径
    private $rsaPublicKeyFilePath;

    //公钥值
    private $rsaPublicKey;

    function __construct()
    {
        $this->rsaPrivateKeyFilePath=dirname(__FILE__).DIRECTORY_SEPARATOR.'key'.DIRECTORY_SEPARATOR.'rsa_private_key.pem';
        $this->rsaPublicKeyFilePath=dirname(__FILE__).DIRECTORY_SEPARATOR.'key'.DIRECTORY_SEPARATOR.'rsa_public_key.pem';
    }

    /**
     *  rsa公钥加密
     **/
    public function rsaEncrypt($data) {
        if($this->checkEmpty($this->rsaPublicKeyFilePath)){
            //读取字符串
            $pubKey= $this->rsaPublicKey;
            $res = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($pubKey, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }else {
            //读取公钥文件
            $pubKey = file_get_contents($this->rsaPublicKeyFilePath);
            //转换为openssl格式密钥
            $res = openssl_get_publickey($pubKey);
        }

        ($res) or die('RSA公钥错误。请检查公钥文件格式是否正确');
        $data=trim($data);
        openssl_public_encrypt($data,$encrypted,$pubKey);//公钥加密
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }
    /**
     * rsa私钥解密
     **/
    public function rsaDecrypt($data) {

        if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
            //读字符串
            $priKey=$this->rsaPrivateKey;
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($priKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        }else {
            $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
            $res = openssl_get_privatekey($priKey);
        }
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        $data=base64_decode($data);
        openssl_private_decrypt($data, $dcyCont, $res);
        return $dcyCont;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
}