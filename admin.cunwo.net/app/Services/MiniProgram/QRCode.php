<?php
namespace App\Services\MiniProgram;


use App\Services\Upload\UploadFile;
use Illuminate\Support\Facades\Log;
use App\Services\EasyWechat\MniProgram\AppCode;

class QRCode
{

    /**
     * 功能：生成二维码
     * @param int $company_id 企业id
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param  string  $block 配置标签  'default'
     * @param string $otherDir 其它路径：如  qrcode   ....  ,注意前后不用加/,会自动在最后加上/
     * @param  string  $savePath 保存路径  '/path/to/directory'
     * @param  string  $fileName 保存文件名  'appcode.png'
     * @param string $scene  最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~，
     *                       其它字符请自行编码为合法字符（因不支持%，中文无法使用 urlencode 处理，请使用其他编码方式）
     * @param  array  $optional  可选参数
     *   $optional = [
     *       'page' => '',// string	主页	否	必须是已经发布的小程序存在的页面（否则报错），例如 pages/index/index,
     *                           根路径前不要填加 /,不能携带参数（参数请放在scene字段里），
     *                           如果不填写这个字段，默认跳主页面
     *       'width' => '', // number  二维码的宽度，单位 px。最小 280px，最大 1280px
     *      'auto_color' => '', // boolean  false自动配置线条颜色，如果颜色依然是黑色，则说明不建议配置主色调
     *       'line_color' => '', // object  / array [ 'r' => 105, 'g' => 166, 'b' => 134,] {"r":0,"g":0,"b":0}	否	auto_color 为 false 时生效，使用 rgb 设置颜色 例如 {"r":"xxx","g":"xxx","b":"xxx"} 十进制表示
     *       'is_hyaline' => '', // boolean	false	否	是否需要透明底色，为 true 时，生成透明底色的小程序码
     *   ];
     * @return mixed  sting 具体错误 ； throws 错误 ；正确 array
     *   return [
     *       'publicPath' => $publicPath,// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
     *       'savePath' => $savePath,// 文件目录 '/resource/company/1/images/2019/10/04/'
     *       'saveName' => $saveName,// 文件名  20191003121326d710d554edce12a1.png
     *       'files_names' => $files_names,//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
     *       'web_url' => url($files_names),//  网址全路径
     *       'full_names' => $publicPath . $files_names // 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
     *   ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getCodeUnlimited($company_id, $errDo = 1, $block = 'default', $otherDir = '', $fileName = '', $scene = '', $optional = []){

        Log::info('微信日志-生成二维码参数:',[$block,  $fileName, $scene, $optional]);
        $app = app('wechat.mini_program.' . $block);
        $ext = 'png';
        $filePathArr = UploadFile::getFilePath($company_id, 1, $errDo, [1,2,3], $fileName, 'qrcode/' . $otherDir, $ext, -1);

        $publicPath = $filePathArr['publicPath'] ?? '';// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
        $savePath = $filePathArr['savePath'] ?? '';// 文件目录 '/resource/company/1/images/2019/10/04/'
        $saveName = $filePathArr['saveName'] ?? '';// 文件名  20191003121326d710d554edce12a1.png
        $files_names = $filePathArr['files_names'] ?? '';//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        $full_names = $filePathArr['full_names'] ?? '';// 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        // 生成保存路径
        $createFilename = AppCode::getwxacodeunlimit($app, rtrim($publicPath . $savePath, '/'), $fileName, $scene, $optional);
        Log::info('微信日志-二维码生成保存路径:',[$createFilename]);
        return $filePathArr;
    }
}