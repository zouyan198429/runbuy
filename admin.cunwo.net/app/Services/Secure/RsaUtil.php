<?php
namespace App\Services\Secure;

/**
 *
 *  php部分接收数据，然后进行解密。解密之后打印，看看数据对不对。
 *  https://blog.csdn.net/LJFPHP/article/details/78566133
 *
 *   config
 *   database
 *   keys 生成的公钥和私钥 放此目录下
 *      rsa_private_key.pem
 *      rsa_public_key.pem
 *   public
 * （1）控制器代码
 *  测试Post过来的值
 * public function PostRsa(Request $request){
 *    $username = $request->input('username');
 *   //var_dump($username);
 *   //这里的 self::rsa_decode（）就是解密的算法，算是封装好的，直接调用即可
 *   $user_name = self::rsa_decode($username);
 *    //var_dump($user_name);
 *   return view('rsa.rsa_res',['result'=>$user_name]);
 *
 * }

 * （2）封装的解密方法
 *
 * // rsa 解密
 *  private static function rsa_decode($data){
 *    return urldecode(
 *        RsaUtil::privDecrypt(
 *            $data ,
 *           file_get_contents( base_path('keys/rsa_private_key.pem') )
 *        )
 *     );
 * }
 *
 *
 *
 */

class RsaUtil
{

    private static function getPrivateKey($privateKey){
        return openssl_pkey_get_private($privateKey);
    }

    /**
     * 私钥加密
     */
    public static function privEncrypt($data,$privateKey){
        if(!is_string($data)){
            return null;
        }
        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey($privateKey))? base64_encode($encrypted) : null;
    }

    /**
     * 私钥解密
     */
    public static function privDecrypt($encrypted,$privateKey)
    {
        if(!is_string($encrypted)){
            return null;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey($privateKey)))? $decrypted : null;
    }
}