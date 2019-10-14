<?php
namespace App\Services\Upload;

use App\Services\Tool;
use Illuminate\Support\Facades\Log;

class UploadFile
{
    // 大后台 admin/年/月/日/文件
    // 企业 company/[生产单元/]年/月/日/文件
    public static $source_path = '/resource/company/';
    public static $source_tmp_path = '/resource/tmp/';// 临时文件夹
    public static $cache_block = 2; // 1 redis缓存分片内容--适合redis内存比较大的服务器，2 临时文件缓存分片内容--redis内存比较小时


    // 1:图片;2:excel
    public static $resource_type = [
        '1' => [
            'name' => '图片文件',
            'ext' => ['jpg','jpeg','gif','png','bmp','ico'],// 扩展名
            'dir' => 'images',// 文件夹名称
            'maxSize' => 5,// 文件最大值  单位 M
            'other' => [],// 其它各自类型需要判断的指标
        ],
        '2' => [
            'name' => 'excel文件',
            'ext' => ['xlsx', 'xls'],// 扩展名
            'dir' => 'excel',// 文件夹名称
            'maxSize' => 10,// 文件最大值 单位 M
            'other' => [],// 其它各自类型需要判断的指标
        ]
    ];

    /**
     * 功能：获得上传或生成文件的相关路径--目录不存在会自动创建
     * @param int $company_id 企业id
     * @param int $resource_type 文件资源类型下标id
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param array $pathSequence 路径顺序数组  1：$company_id；2  $typeDir 类型文件夹 ;3 其它文件夹 $otherDir ;4 Y/m/d/；5 Y/；6m/；7 d/；8 H/i/s/; 9 H/; 10 i/; 11 s/
     * @param string $saveName 文件名，注意要有扩展名  20191003121326d710d554edce12a1.png
     * @param string $otherDir 其它路径：如  qrcode   ....  ,注意前后不用加/,会自动在最后加上/
     * @param string $ext 扩展名
     * @param int $size 当前文件大小，< 0:则不判断
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
    public static function getFilePath($company_id = 0, $resource_type = 1, $errDo = 1, $pathSequence = [1,2,3,4], $saveName = '', $otherDir = '', $ext = 'png', $size = -1 ){

        $resourceTypeArr = static::$resource_type[$resource_type] ?? [];
        if(empty($resourceTypeArr)){
            $errMsg = '不明确的资源类型!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        $typeName = $resourceTypeArr['name'] ?? '';// 类型名称
        $typeExt = $resourceTypeArr['ext'] ?? [];// 扩展名
        $typeDir = $resourceTypeArr['dir'] ?? '';// 文件夹名称
        $typeMaxSize = $resourceTypeArr['maxSize'] ?? '0.5';// 文件最大值 单位 M
        if(!is_numeric($typeMaxSize)) $typeMaxSize = 0.5;// 0.5M
        $typeOther = $resourceTypeArr['other'] ?? [];// 其它各自类型需要判断的指标
        if(!in_array($ext , $typeExt)) {
            $errMsg = $typeName . '扩展名必须为[' . implode('、', $typeExt) . ']';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        //这里可根据配置文件的设置，做得更灵活一点
        if($size >= 0 && $size > $typeMaxSize * 1024 * 1024){
            $errMsg = '上传文件不能超过[' . $typeMaxSize . 'M]';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }

        $publicPath = Tool::getPath('public');
        $savePath = static::mkDir($publicPath, $company_id, $resource_type, $pathSequence, $otherDir);
        //if(is_numeric($pro_unit_id)){
        //    $savePath .=   'pro' . $pro_unit_id . '/';
        //}

        if(empty($saveName)) $saveName = static::createFileName(30, $ext);// Tool::createUniqueNumber(30) .'.' . $ext;
        $files_names = $savePath . $saveName;
        return [
            'publicPath' => $publicPath,// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
            'savePath' => $savePath,// 文件目录 '/resource/company/1/images/2019/10/04/'
            'saveName' => $saveName,// 文件名  20191003121326d710d554edce12a1.png
            'files_names' => $files_names,//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
            'web_url' => url($files_names),//  网址全路径
            'full_names' => $publicPath . $files_names // 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        ];
    }

    /**
     * 功能：生成和创建目录
     * @param int $sysAbsDir  目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public  默认为public目录
     * @param int $company_id 企业id
     * @param int $resource_type 文件资源类型下标id  --可以为0，不存在的
     * @param array $pathSequence 路径顺序数组  1：$company_id；2  $typeDir 类型文件夹 ;3 其它文件夹 $otherDir ;4 Y/m/d/；5 Y/；6m/；7 d/；8 H/i/s/; 9 H/; 10 i/; 11 s/
     * @param string $otherDir 其它路径：如  qrcode   ....  ,注意前后不用加/,会自动在最后加上/
     * @return mixed  $savePath  文件目录 '/resource/company/1/images/2019/10/04/'
     * @author zouyan(305463219@qq.com)
     */
    public static function mkDir($sysAbsDir = '', $company_id = 0, $resource_type = 1, $pathSequence = [1,2,3,4], $otherDir = ''){
        if(strlen($sysAbsDir) <= 0) $sysAbsDir = Tool::getPath('public');
        // --可以为0，不存在的
        $resourceTypeArr = static::$resource_type[$resource_type] ?? [];
        $typeDir = $resourceTypeArr['dir'] ?? '';// 文件夹名称

        // 生成保存路径
        $savePath = static::$source_path;
        foreach($pathSequence as $sequence_num){
            switch ($sequence_num)
            {
                case 1:// 1：$company_id；
                    if(is_numeric($company_id) && $company_id >= 0 )  $savePath .= $company_id . '/';
                    break;
                case 2:// 2  $typeDir 类型文件夹 ;
                    if($typeDir != '' ) $savePath .=   $typeDir . '/';// 类型文件夹
                    break;
                case 3:// 3  其它文件夹 $otherDir ;
                    if($otherDir != '' ) $savePath .=   trim($otherDir, '/') . '/';// 其它文件夹
                    break;
                case 4:// 4 Y/m/d/
                    $savePath .= date('Y/m/d/',time());
                    break;
                case 5:// 5 Y/；
                    $savePath .= date('Y/',time());
                    break;
                case 6:// 6m/；
                    $savePath .= date('m/',time());
                    break;
                case 7:// 7 d/；
                    $savePath .= date('d/',time());
                    break;
                case 8:// 8 H/i/s/;
                    $savePath .= date('H/i/s/',time());
                    break;
                case 9://  9 H/;
                    $savePath .= date('H/',time());
                    break;
                case 10:// 10 i/;
                    $savePath .= date('i/',time());
                    break;
                case 11:// 11 s/
                    $savePath .= date('s/',time());
                    break;
                default:
                    break;
            }

        }
        $filePath = $sysAbsDir . $savePath;
        if(!file_exists($filePath)) makeDir($filePath);// 创建目录
        return $savePath;
    }

    /**
     * 功能：生成新的文件名--可以没有扩展名
     * @param int $fileNameLen  文件名的长度 默认 30位
     * @param string $ext 扩展名 png ，可以 为空，则返回的文件名没有扩展名
     * @return string  $fileName  生成新的文件名--可以没有扩展名
     * @author zouyan(305463219@qq.com)
     */
    public static function createFileName($fileNameLen = 30, $ext = ''){
        $fileName = Tool::createUniqueNumber($fileNameLen);
        if(strlen($ext) > 0) $fileName .= '.' . $ext;
        return $fileName;
    }
}