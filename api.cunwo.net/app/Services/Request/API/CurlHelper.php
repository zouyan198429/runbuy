<?php
namespace App\Services\Request\API;


class CurlHelper
{

    /**
     * POST 请求
     *
     * @param string $url
     * @param array $param
     * @param boolean $post_file 是否文件上传
     * @param array $cert
     *
     * @return string content
     */
    public static function post($url, $param, $content_type = 'form', $post_file = false, $cert = [])
    {
        $ch = curl_init();

        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }

        if (PHP_VERSION_ID >= 50500 && class_exists('\CURLFile')) {
            $isCurlFile = true;
        } else {
            $isCurlFile = false;
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
        }

        if (is_string($param)) {
            $strPOST = $param;
        } elseif ($post_file) {
            if ($isCurlFile) {
                foreach ($param as $key => $val) {
                    if (substr($val, 0, 1) == '@') {
                        $param[$key] = new \CURLFile(realpath(substr($val, 1)));
                    }
                }
            }
            $strPOST = $param;
        } elseif ($content_type == 'json') {
            $strPOST = json_encode($param);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($strPOST),
            ]);
        } else {
            $aPOST = [];
            foreach ($param as $key => $val) {
                $aPOST[] = $key . "=" . urlencode($val);
            }
            $strPOST = join("&", $aPOST);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPOST);

        if ($cert) {
            foreach ($cert as $key => $value) {
                curl_setopt($ch, $key, $value);
            }
        }

//        Yii::info($url, 'curl\url');
//        Yii::info($strPOST, 'curl\postfields');

        $sContent = curl_exec($ch);

        if (! $sContent && $errMsg = curl_error($ch)) {
//            Yii::error($strPOST, 'curl\error');
            throws($errMsg, curl_errno($ch));
        }

        $aStatus = curl_getinfo($ch);
        curl_close($ch);

//        Yii::info($sContent, 'curl\response');

        if (intval($aStatus['http_code']) != 200) {
            return false;
        }

        return $sContent;
    }

    /**
     * GET 请求
     *
     * @param string $url
     */
    public static function get($url)
    {
        $ch = curl_init();
        if (stripos($url, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $sContent = curl_exec($ch);
        $aStatus = curl_getinfo($ch);
        curl_close($ch);

        if (intval($aStatus['http_code']) != 200) {
            return false;
        }

        return $sContent;
    }

    /**
     * put请求
     * @param string $URL 请求url
     * @param array /json $params 请求参数 "{user:\"admin\",pwd:\"admin\"}"
     * @param array $headers 请求头
     * // $headers=array('Content-Type: text/html; charset=utf-8');
     * // $headers=array('accept: application/json; Content-Type:application/json-patch+json');
     * // $headers=array('Content-Type:application/json-patch+json');
     * @param int $timeout 超时时间
     * @param string 获得的内容
     */
    public static function put($URL, $params = '', $headers = '', $timeout = 15)
    {
        $ch = curl_init();
        if (stripos($URL, "https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if ($headers != "") {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        }
        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $sContent = curl_exec($ch);//获得返回值
        $aStatus = curl_getinfo($ch);
        curl_close($ch);
        if (intval($aStatus['http_code']) != 200) {
            return false;
        }
        return $sContent;
    }

    /**
     * 以xml格式发送post请求
     *
     * @param $url
     * @param $xmldata
     * @param int $timeout
     *
     * @return mixed
     */
    public static function xmlPost($url, $xmldata, $timeout = 30)
    {
        $header[] = "Content-type: application/xml";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * 获取URL资源二进制
     *
     * @param string $url 远程url
     * @param int $timeout 超时时间,单位秒
     * @return binary 读出的二进制数据
     */
    public static function curlFile($url, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        // 在需要用户检测的网页里需要增加下面两行
        // 使用的 HTTP 验证方法 选项有： CURLAUTH_BASIC、 CURLAUTH_DIGEST、 CURLAUTH_GSSNEGOTIATE、 CURLAUTH_NTLM、 CURLAUTH_ANY和 CURLAUTH_ANYSAFE。
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        // 传递一个连接中需要的用户名和密码，格式为："[username]:[password]"。
        // curl_setopt($ch, CURLOPT_USERPWD, US_NAME.”:”.US_PWD);
        $contents = curl_exec($ch);
        curl_close($ch);
        return $contents;
    }
}