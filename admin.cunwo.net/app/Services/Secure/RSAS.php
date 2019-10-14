<?php

namespace App\Services\Secure;

/*
 *
 * 非对称加密函数 https://www.cnblogs.com/fxyy/p/8868351.html
 * $res = openssl_pkey_new([array $config]) : 生成一个新的私钥和公钥对. 通过配置数组, 可以微调密钥的生成.
 *
 * digest_alg : 摘要或签名哈希算法.
 * private_key_bits : 指定生成的私钥的长度.
 * private_key_type : 指定生成私钥的算法. 默认 OPENSSL_KEYTYPE_RSA, 可指定 OPENSSL_KEYTYPE_DSA, OPENSSL_KEYTYPE_DH, OPENSSL_KEYTYPE_RSA,
 * OPENSSL_KEYTYPE_EC.
 * config : 自定义 openssl.conf 文件的路径.
 * openssl_pkey_free($res) : 释放有 openssl_pkey_new() 创建的私钥.
 * openssl_get_md_methods() : 获取可用的摘要算法.
 * openssl_pkey_export_to_file($res, $outfilename) : 将 ASCII 格式 ( PEM 编码 ) 的密钥导出到文件中. 使用相对路径时, 是相对服务器目录,
  * 而非当前所在目录.
 * openssl_pkey_export($res, &$out) : 提取 PEM 格式私钥字符串.
 * openssl_pkey_get_details($res) : 返回包含密钥详情的数组.
 * openssl_get_privatekey($key) : 获取私钥. key 是一个 PEM 格式的文件或一个 PEM 格式的私钥.
 * openssl_get_publickey($certificate) : 获取公钥. certificate 是一个 X.509 证书资源或一个 PEM 格式的文件或一个 PEM 格式的公钥.
 * openssl_private_encrypt($data, &$crypted, $privKey [, $padding = OPENSSL_PKCS1_PADDING]) : 使用私钥加密数据, 并保存到 crypted .
 * 其中填充模式为 OPENSSL_PKCS1_PADDING 时, 如果明文长度不够, 加密时会在明文中随机填充数据. 为 OPENSSL_NO_PADDING 时, 如果明文长度不够,
 *  会在明文的头部填充 0 .
 * openssl_public_decrypt($crypted, &$decrypted, $pubKey [, $padding]) : 使用公钥解密数据, 并保存到 decrypted .
 * openssl_public_encrypt($data, &$crypted, $pubKey [, $padding]) : 使用公钥加密数据, 并保存到 crypted .
 * openssl_private_decrypt($crypted, &$decrypted, $privKey [, $padding]) : 使用私钥解密数据, 并保存到 decrypted .
 *  RSA 也是一种分组加密方式, 但明文的分组长度根据选择的填充方式的不同而不同.
 * 在传输重要信息时, 一般会采用对称加密和非对称加密相结合的方式, 而非使用单一加密方式. 一般先通过 AES 加密数据, 然后通过 RSA 加密 AES 密钥,
 * 然后将加密后的密钥和数据一起发送. 接收方接收到数据后, 先解密 AES 密钥, 然后使用解密后的密钥解密数据.
 *
 */
class RSAS
{
    private $private_key; // 私钥
    private $public_key; // 公钥
    private $private_res; // 私钥资源
    private $public_res; // 公钥资源

    public function __construct()
    {
        extension_loaded('openssl') or die('未加载 openssl');
        // 生成新的公钥和私钥对资源
        $config = [
            'digest_alg' => 'sha256',
            'private_key_bits' => 1204,
            'private_key_type' => OPENSSL_KEYTYPE_RSA
        ];
        $res = openssl_pkey_new($config);
        if(!$res)
        {
            die('生成密钥对失败');
        }

        // 获取公钥, 生成公钥资源
        $this->public_key = openssl_pkey_get_details($res)['key'];
        $this->public_res = openssl_pkey_get_public($this->public_key);

        // 获取私钥, 生成私钥资源
        openssl_pkey_export($res, $this->private_key);
        $this->private_res = openssl_pkey_get_private($this->private_key);

        openssl_free_key($res);
    }

    // 加密
    public function encrypt($plaintext)
    {
        $ciphertext = null;
        openssl_public_encrypt($plaintext, $ciphertext, $this->public_res);
        return $ciphertext;
    }

    // 解密
    public function decrypt($ciphertext)
    {
        $plaintext = null;
        openssl_private_decrypt($ciphertext, $plaintext, $this->private_res);
        return $plaintext;
    }
}