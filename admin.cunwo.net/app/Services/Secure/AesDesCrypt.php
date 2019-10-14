<?php
namespace App\Services\Secure;


class AesDesCrypt
{
    /**
     * 生成带有密钥的哈希值
     * @param string $method 加密算法
     * @link http://php.net/manual/en/function.hash-hmac.php
     * @param string $algo <p>
     * Name of selected hashing algorithm (i.e. "md5", "sha1", "sha256", "haval160,4", etc..) See <b>hash_algos</b> for a list of supported algorithms.
     * </p>
     * @param string $data 要加密的数据--明文
     * @param string $key 密匙
     * @param boolean raw_output  true:输出原始二进制数据;false:输出长度固定的小写 16 进制字符串[默认]
     * @return  string $data 解密后的数据
     * @author zouyan(305463219@qq.com)
     */

    public static function hashHmac($method, $data, $key, $raw_out = false)
    {
        return hash_hmac($method, $data, $key, $raw_out);
     }


    //经过AES中的base64_encode加密后，在传输的过程中，
    //+ 会变成 空格
    //因此需要对其进行安全转换：
    // 因此对base64_encode 进行了封装处理，其实就是进行吧 字符的替换工作，这样在传输的过程中就不会出现错误了。
    //解密的时候，重新把替换的字符替换回来，这两个函数就是做两件事情用的。
    public static function base64encode($data, $urlsafe = FALSE)
    {
        $data = base64_encode($data);
        return $urlsafe ? strtr($data, '+/', '-_') : $data;
    }

    public static function base64decode($data, $urlsafe = FALSE)
    {
        return base64_decode($urlsafe ? strtr($data, '-_', '+/') : $data);
    }

    // AES加密解密
    //随机生成了  openssl_random_pseudo_bytes(16);  16个伪字节符，用于加密更安全
    //通过16个字符

    /**
     *  AES加密解密 样例
     * $key = "456fggrhgfhhfghf中g";
     * $pass = AesDesCrypt::AESEncrypt('中华人民共和国dfasfsdf1145', $key, true);
     * echo $pass;
     * echo "<br>";
     *
     * $src = AesDesCrypt::AESDecrypt($pass,$key, true);
     * echo $src;
     */
    // AES加密
    public static function AESEncrypt($data, $key, $urlsafe = FALSE)
    { //openssl_get_cipher_methods
        if ($data && $key) {
            $iv = openssl_random_pseudo_bytes(16);  //随机生成一个伪字节  echo random_bytes(5);
            $data = static::base64encode($iv . openssl_encrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv), $urlsafe);
        }
        return $data;
    }

    // AES解密
    public static function AESDecrypt($data, $key, $urlsafe = FALSE)
    {
        if (strlen($data) >= (16 + 16) && $key) {
            $data = static::base64decode($data, $urlsafe);
            $data = openssl_decrypt(substr($data, 16), 'aes-128-cbc', $key, OPENSSL_RAW_DATA, substr($data, 0, 16));
        }
        return $data;
    }


//    --------------------------
//    DES 加密 
//     
//    DES3 加密  
//     
//    以下这两种加密方式，其实也是openssl 加密中的，只不过是 加密的方法不一样， 不要以为是其他的加密方式。
//     
//     
//     $data = openssl_encrypt($data, 'des-ecb', $key);
//      
//     $data = openssl_decrypt($data, 'des-ecb', $key);
//     
//    就是第二个参数传递不一样。


    /**
     * DES 样例
     * $key = "456fggrhgfhhfghf中g";
     * $pass = AesDesCrypt::DESEncrypt('中华人民共和国dfasfsdf1145', $key);
     * echo $pass;
     * echo "<br>";
     *
     * $src = AesDesCrypt::DESDecrypt($pass,$key);
     *
     * echo $src;
     *
     *
     */
    //    DES 加密
    public static function DESEncrypt($data, $key, $urlsafe = FALSE)
    {
        if ($data && $key) {
            $data = openssl_encrypt($data, 'des-ecb', $key);
            $urlsafe && $data = strtr($data, '+/', '-_');
        }
        return $data;
    }

    //    DES 解密
    public static function DESDecrypt($data, $key, $urlsafe = FALSE)
    {
        if ($data && $key) {
            $urlsafe && $data = strtr($data, '-_', '+/');
            $data = openssl_decrypt($data, 'des-ecb', $key);
        }
        return $data;
    }

    /**
     * DES3 样例
     * $key = "456fggrhgfhhfghf中g";
     * $pass = AesDesCrypt::DES3Encrypt('中华人民共和国dfasfsdf1145', $key);
     * echo $pass;
     * echo "<br>";
     *
     * $src = AesDesCrypt::DES3Decrypt($pass,$key);
     *
     * echo $src;
     * die;
     *
     */
    //    DES3 加密
    public static function DES3Encrypt($data, $key, $urlsafe = FALSE)
    {
        if ($data && $key) {
            $data = openssl_encrypt($data, 'des-ede3', $key);

            $urlsafe && $data = strtr($data, '+/', '-_');
        }
        return $data;
    }

    //    DES3 解密
    public static function DES3Decrypt($data, $key, $urlsafe = FALSE)
    {
        if ($data && $key) {
            $urlsafe && $data = strtr($data, '-_', '+/');

            if ($result = openssl_decrypt($data, 'des-ede3', $key, OPENSSL_ZERO_PADDING)) {
                $padding = ord(substr($result, -1)); //DESede/ECB/ISO10126Padding
                $padding <= 8 && $result = substr($result, 0, -$padding);
            }

            return $result;
        } else return $data;
    }

    // 通用加密解密
    //     加密

    /**
     * 通用加密 -- 如果有$iv,则会加到加密后的数据的最前面
     * @param string $method 加密要使用的方法 aes-128-cbc :  AES加解密 --需要base64;des-ecb : DES加解密--不需要base64;des-ede3 : DES3 加解密--不需要base64
     * @param string $data 要加密的数据
     * @param string $key 密匙
     * @param int $options [optional] <p> 小于0,则自动处理
     * options is a bitwise disjunction of the flags OPENSSL_RAW_DATA and OPENSSL_ZERO_PADDING.
     * </p>
     * 0 : 自动对明文进行 padding, 返回的数据经过 base64 编码.
     * 1 : OPENSSL_RAW_DATA, 自动对明文进行 padding, 但返回的结果未经过 base64 编码.
     * 2 : OPENSSL_ZERO_PADDING, 自动对明文进行 0 填充, 返回的结果经过 base64 编码.
     * 但是, openssl 不推荐 0 填充的方式, 即使选择此项也不会自动进行 padding, 仍需手动 padding.
     * @param boolean $base64 是否进行base64转换 true:转换;false:不转换[默认]
     * @param boolean $urlsafe 是否进行url传输转换 true:转换;false:不转换[默认]
     * @param string &$tag <p>The authentication tag passed by reference when using AEAD cipher mode (GCM or CCM).</p>
     * @param string $aad <p>Additional authentication data.</p>
     * @param int $tag_length [optional] <p>
     * The length of the authentication tag. Its value can be between 4 and 16 for GCM mode.
     * </p>
     * @return  string $data 加密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function CommonEncrypt($method, $data, $key, $options = -1, $base64 = FALSE, $urlsafe = FALSE)// , &$tag = NULL, $aad = "", $tag_length = 16
    {
        if ($data && $key) {
            $ssl_iv_len = static::getIvLen($method); //得到这个加密方法需要的 位数
            $iv = static::getIv($method, $ssl_iv_len);// iv : 非空的初始化向量, 不使用此项会抛出一个警告. 如果未进行手动填充, 则返回加密失败.
            if($options < 0) $options = $ssl_iv_len > 0 ?  OPENSSL_RAW_DATA : 0;// OPENSSL_RAW_DATA OPENSSL_ZERO_PADDING
            $data = $iv . openssl_encrypt($data, $method, $key, $options, $iv);// ,$tag, $aad, $tag_length

            $base64 && $data = base64_encode($data);// 还需要进行base64转换
            $urlsafe && $data = strtr($data, '+/', '-_');
        }
        return $data;
    }

    //     解密
    /**
     * 通用解密----如果有$iv,则在加密后的数据的最前面
     * @param string $method 加密要使用的方法 aes-128-cbc :  AES加解密 --需要base64;des-ecb : DES加解密--不需要base64;des-ede3 : DES3 加解密--不需要base64
     * @param string $data 要解密的数据
     * @param string $key 密匙
     * @param int $options [optional] <p> 小于0,则自动处理
     * Setting to true will take a raw encoded string,
     * otherwise a base64 string is assumed for the
     * <i>data</i> parameter.
     * </p>
     * @param boolean $base64 是否进行base64转换 true:转换;false:不转换[默认]
     * @param boolean $urlsafe 是否进行url传输转换 true:转换;false:不转换[默认]
     * @param string $tag [optional] <p>
     * The authentication tag in AEAD cipher mode. If it is incorrect, the authentication fails and the function returns <b>FALSE</b>.
     * </p>
     * @param string $aad [optional] <p>Additional authentication data.</p>
     * @return  string $data 解密后的数据
     * @author zouyan(305463219@qq.com)
     */
    public static function CommonDecrypt($method, $data, $key, $options = -1, $base64 = FALSE, $urlsafe = FALSE)// , $tag = "",  $aad = ""
    {
        $ssl_iv_len = static::getIvLen($method); //得到这个加密方法需要的 位数
        if ($data && $key ) {

            $urlsafe && $data = strtr($data, '-_', '+/');
            $base64 && $data = base64_decode($data);
            $iv = '';
            if($ssl_iv_len > 0) {
                $iv = substr($data, 0, $ssl_iv_len);
                $data = substr($data, $ssl_iv_len);
                if(strlen($data) <= 0) return $data;// 如果空数据，则直接返回
            }
            if($options < 0) $options = $ssl_iv_len > 0 ?  OPENSSL_RAW_DATA : 1;// OPENSSL_ZERO_PADDING;
            $data = openssl_decrypt($data, $method, $key, $options, $iv);// , $tag, $aad
            // 有的解密需要特殊处理
            switch ($method)
            {
                case 'des-ede3':// des-ede3  DES3 解密
                    if ($data && $method == 'des-ede3') {
                        $padding = ord(substr($data, -1)); //DESede/ECB/ISO10126Padding
                        $padding <= 8 && $data = substr($data, 0, -$padding);
                    }
                    break;
                default:
                    break;
            }
            return $data;
        } else{
            return $data;
        }
    }

    /**
     * 根据加密要使用的方法，获得iv
     * @param string $method 加密要使用的方法
     * @param string int 获得iv的长度, <0 ，则自动重新获取
     * @return  string iv
     * @author zouyan(305463219@qq.com)
     */
    public static function getIv($method, $ssl_iv_len = 0)
    {
        if($ssl_iv_len == 0) return '';
        if (!is_numeric($ssl_iv_len) ||  $ssl_iv_len <= 0 )  $ssl_iv_len = static::getIvLen($method); //得到这个加密方法需要的 位数
        if (!is_numeric($ssl_iv_len) ||  $ssl_iv_len <= 0 ) return '';
        $iv = openssl_random_pseudo_bytes($ssl_iv_len); //需要知道加密iv 的向量的位数
        return $iv;
    }

    /**
     * 根据加密要使用的方法，获得iv的长度
     * @param string $method 加密要使用的方法
     * @return  int $ssl_iv_len
     * @author zouyan(305463219@qq.com)
     */
    public static function getIvLen($method)
    {
        $ssl_methods = openssl_get_cipher_methods();
        if (!in_array($method, $ssl_methods)) throws('$method is on in openssl_get_cipher_methods');

        $ssl_iv_len = openssl_cipher_iv_length($method); //得到这个加密方法需要的 位数
        if (!is_numeric($ssl_iv_len) || $ssl_iv_len <= 0) $ssl_iv_len = 0;

        return $ssl_iv_len;
    }
}