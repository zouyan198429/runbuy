<?php


namespace App\Services\Secure;

/**
 * https://www.cnblogs.com/fxyy/p/8868351.html
 * 对称加密函数
 * openssl_get_cipher_methods() : 返回 openssl 支持的所有加密方式.
 * openssl_encrypt($data, $method, $key, $options = 0, $iv = '') : 以指定方式 method 和密钥 key 加密 data, 返回 false 或加密后的数据.
 *
 * data : 明文
 * method : 加密算法
 * key : 密钥
 * options :
 * 0 : 自动对明文进行 padding, 返回的数据经过 base64 编码.
 * 1 : OPENSSL_RAW_DATA, 自动对明文进行 padding, 但返回的结果未经过 base64 编码.
 * 2 : OPENSSL_ZERO_PADDING, 自动对明文进行 0 填充, 返回的结果经过 base64 编码. 但是, openssl 不推荐 0 填充的方式,
 * 即使选择此项也不会自动进行 padding, 仍需手动 padding.
 * iv : 非空的初始化向量, 不使用此项会抛出一个警告. 如果未进行手动填充, 则返回加密失败.
 * openssl_decrypt($data, $method, $key, $options = 0, $iv = '') : 解密数据.
 * openssl_cipher_iv_length($method) : 获取 method 要求的初始化向量的长度.
 * openssl_random_pseudo_bytes($length) : 生成指定长度的伪随机字符串.
 * hash_mac($method, $data, $key, $raw_out) : 生成带有密钥的哈希值.
 *
 * method : 加密算法
 * data : 明文
 * key : 密钥
 * raw_output :
 * TRUE : 输出原始二进制数据
 * FALSE : 输出长度固定的小写 16 进制字符串
 *
 * 主流的对称加密方式有 DES, AES. 这两种加密方式都属于分组加密, 先将明文分成多个等长的模块 ( block ), 然后进行加密.
 * DES 加密
 * DES 加密的密钥长度为 64 bit, 实际应用中有效使用的是 56 位, 剩余 8 位作为奇偶校验位. 如果密钥长度不足 8 个字节,
 * 将会使用 \0 补充到 8 个字节. 如密钥为 "12345", 其加密后的密文与密钥 "12345\0\0\0" 加密后的密文相同.
 * 明文按 64 bit ( UTF-8 下为 8 个字节长度 ) 进行分组, 每 64 位分成一组 ( 最后一组不足 64 位的需要填充数据 ),
 * 分组后的明文组和密钥按位替代或交换的方法形成密文组.
 *
 */
class DES
{
    private $method = 'DES-CBC';
    private $key;

    public function __construct($key)
    {
        // 密钥长度不能超过64bit(UTF-8下为8个字符长度),超过64bit不会影响程序运行,但有效使用的部分只有64bit,多余部分无效,可通过openssl_error_string()查看错误提示
        $this->key = $key;
    }

    public function encrypt($plaintext)
    {
        // 生成加密所需的初始化向量, 加密时缺失iv会抛出一个警告
        $ivlen = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivlen);

        // 按64bit一组填充明文
        //$plaintext = $this->padding($plaintext);
        // 加密数据. 如果options参数为0, 则不再需要上述的填充操作. 如果options参数为1, 也不需要上述的填充操作, 但是返回的密文未经过base64编码. 如果options参数为2, 虽然PHP说明是自动0填充, 但实际未进行填充, 必须需要上述的填充操作进行手动填充. 上述手动填充的结果和options为0和1是自动填充的结果相同.
        $ciphertext = openssl_encrypt($plaintext, $this->method, $this->key, 1, $iv);
        // 生成hash
        $hash = hash_hmac('sha256', $ciphertext, $this->key, false);

        return base64_encode($iv . $hash . $ciphertext);

    }

    public function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        // 从密文中获取iv
        $ivlen = openssl_cipher_iv_length($this->method);
        $iv = substr($ciphertext, 0, $ivlen);
        // 从密文中获取hash
        $hash = substr($ciphertext, $ivlen, 64);
        // 获取原始密文
        $ciphertext = substr($ciphertext, $ivlen + 64);
        // hash校验
        if(hash_equals($hash, hash_hmac('sha256', $ciphertext, $this->key, false)))
        {
            // 解密数据
            $plaintext = openssl_decrypt($ciphertext, $this->method, $this->key, 1, $iv) ?? false;
            // 去除填充数据. 加密时进行了填充才需要去填充
            $plaintext = $plaintext? $this->unpadding($plaintext) : false;

            return $plaintext;
        }

        return '解密失败';
    }

    // 按64bit一组填充数据
    private function padding($plaintext)
    {
        $padding = 8 - (strlen($plaintext)%8);
        $chr = chr($padding);

        return $plaintext . str_repeat($chr, $padding);
    }

    private function unpadding($ciphertext)
    {
        $chr = substr($ciphertext, -1);
        $padding = ord($chr);

        if($padding > strlen($ciphertext))
        {
            return false;
        }
        if(strspn($ciphertext, $chr, -1 * $padding, $padding) !== $padding)
        {
            return false;
        }

        return substr($ciphertext, 0, -1 * $padding);
    }
}