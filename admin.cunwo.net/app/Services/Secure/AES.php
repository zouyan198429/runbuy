<?php

namespace App\Services\Secure;
/**
 *
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
 * AES 加密
 * AES 加密的分组长度是 128 位, 即每个分组为 16 个字节 ( 每个字节 8 位 ). 密钥的长度根据加密方式的不同可以是 128 位, 192 位, 256 位.
 * 与 DES 加密一样. 密钥长度超过指定长度时, 超出部分无效. 密钥长度不足时, 会自动以`\0`补充到指定长度.
 *
 * AES	密钥长度 ( 位 )	分组长度 ( 位 )
 * AES-128	128	128
 * AES-192	192	128
 * AES-256	256	128
 *
 */
class AES
{
    private $key;
    private $method = 'aes-128-cbc';

    public function __construct($key)
    {
        // 是否启用了openssl扩展
        extension_loaded('openssl') or die('未启用 OPENSSL 扩展');
        $this->key = $key;
    }

    public function encrypt($plaintext)
    {
        if(!in_array($this->method, openssl_get_cipher_methods()))
        {
            die('不支持该加密算法!');
        }
        // options为1, 不需要手动填充
        //$plaintext = $this->padding($plaintext);
        // 获取加密算法要求的初始化向量的长度
        $ivlen = openssl_cipher_iv_length($this->method);
        // 生成对应长度的初始化向量. aes-128模式下iv长度是16个字节, 也可以自由指定.
        $iv = openssl_random_pseudo_bytes($ivlen);
        // 加密数据
        $ciphertext = openssl_encrypt($plaintext, $this->method, $this->key, 1, $iv);
        $hmac = hash_hmac('sha256', $ciphertext, $this->key, false);

        return base64_encode($iv . $hmac . $ciphertext);
    }

    public function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($this->method);
        $iv = substr($ciphertext, 0, $ivlen);
        $hmac = substr($ciphertext, $ivlen, 64);
        $ciphertext = substr($ciphertext, $ivlen + 64);
        $verifyHmac = hash_hmac('sha256', $ciphertext, $this->key, false);
        if(hash_equals($hmac, $verifyHmac))
        {
            $plaintext = openssl_decrypt($ciphertext, $this->method, $this->key, 1, $iv)??false;
            // 加密时未手动填充, 不需要去填充
            //if($plaintext)
            //{
            //    $plaintext = $this->unpadding($plaintext);
            //    echo $plaintext;
            //}

            return $plaintext;
        }else
        {
            die('数据被修改!');
        }
    }

    private function padding(string $data) : string
    {
        $padding = 16 - (strlen($data) % 16);
        $chr = chr($padding);
        return $data . str_repeat($chr, $padding);
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