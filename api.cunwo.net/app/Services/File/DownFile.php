<?php
//  文件下载类
namespace App\Services\File;

use App\Services\Tool;
use App\Services\Upload\UploadFile;

class DownFile{
//    var $file_name;
//    var $file_dir;
//    var $buffer_size = 1024;
//    var $err = "";

    public static $MIME_TYPE = array(
        "pdf"  =>"application/pdf",
        "exe"  =>"application/octet-stream",
        "zip"  =>"application/zip",
        "doc"  =>"application/msword",
        "xls"  =>"application/vnd.ms-excel",
        "ppt"  =>"application/vnd.ms-powerpoint",
        "gif"  =>"image/gif",
        "png"  =>"image/png",
        "jpeg" =>"jpg",
        "mp3"  =>"audio/mpeg",
        "wav"  =>"audio/x-wav",
        "mpeg" =>"mpg",
        "mpe"  =>"video/mpeg",
        "mov"  =>"video/quicktime",
        "avi"  =>"video/x-msvideo",
    );

//    public function __construct($file_dir="",$file_name=""){
//        $this->file_dir  = $file_dir;
//        $this->file_name = $file_name;
//        $this->path = $file_dir."/".$file_name;
//        $this->suffix = pathinfo($file_name,PATHINFO_EXTENSION);
//    }

    /**
     * 下载文件--文件全路径---只能针对本地文件
     *
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $file_path 文件路径 + 文件名  全部的路径了all/a.exe /srv/www/runbuy/admin.cunwo.net/public/resource/company/1/down/2019/10/06/20191006102805433363870c5b369e.png
     * @param int $buffer_size 每次读取多少
     * @return  mixed sting 具体错误 ； throws 错误 ；正确 true
     * @author zouyan(305463219@qq.com)
     */
    public static function downFilePath($errDo = 1,$file_path = "", $buffer_size = 1024){
        if(!file_exists($file_path)){
            $errMsg = '该文件不存在!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
        }
        $file_dir = dirname($file_path);// dirname() 函数返回路径中的目录名称部分
        $file_name = basename($file_path);// basename() 函数返回路径中的文件名部分。
        return static::down($errDo, $file_dir, $file_name, $buffer_size);
    }

    /**
     * 下载文件--文件路径 +   文件名---只能针对本地文件
     *
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $file_dir 文件路径 比如 all "/xxx/xxx"
     * @param string $file_name 文件名 比如 a.exe  合起来就是全部的路径了all/a.exe
     * @param int $buffer_size 每次读取多少
     * @return  mixed sting 具体错误 ； throws 错误 ；正确 true
     * @author zouyan(305463219@qq.com)
     */
    public static function down($errDo = 1,$file_dir = "", $file_name = "", $buffer_size = 1024){
        //用以解决中文不能显示出来的问题
        $file_dir = iconv("utf-8","gb2312",$file_dir);
        $file_name = iconv("utf-8","gb2312",$file_name);
        $path = $file_dir . "/" . $file_name;
        $suffix = pathinfo($file_name,PATHINFO_EXTENSION);

        if(!file_exists($path)){
            $errMsg = '该文件不存在!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
//            $err = "该文件被移除了";
//            return false;
        }
        $content_type = static::getMIME($suffix);
        $file_size = filesize($path);
        //指定下载文件类型
        //流的方式发送给浏览器 header("Content-Type: application/octet-stream")
        header("Content-type: ".$content_type);
        //以附件的形式发送给浏览器(也就是弹出，下载的对话框)
        header('Content-Disposition: attachment; filename="'.$file_name.'"');
        header("Content-type:text/html;charset=utf-8");
        @header("Cache-control: public");
        @header("Pragma: public");
        //指定下载文件的大小
        header("Content-Length: ".$file_size);
        ob_end_clean();
        //readfile($path); 一次性读出来
        $fp = fopen($path,"r");
        $cur_pos = 0; //记录读了多少了

        while( !feof($fp) && $file_size > ($buffer_size + $cur_pos) ){
            $buffer = fread($fp,$buffer_size); //每次读1024字节
            echo $buffer;
            $cur_pos += $buffer_size;
        }
        //把剩下的读出来 因为文件的带下很有很能不是1024 的整数倍
        if( ($file_size - $cur_pos) > 0){
            $buffer = fread($fp,$file_size - $cur_pos);
            echo $buffer;
        }
        fclose($fp);
        return true;
    }

    /**
     * 根据文件扩展名，返回对应的Content-Type参数的值,默认或没有则返回 application/octet-stream（ 二进制流，未知的文件类型）
     *
     * @param string $key 文件扩展名
     * @return  string 对应的Content-Type参数的值
     * @author zouyan(305463219@qq.com)
     */
    public static function getMIME($key = ""){
        if($key == "" || !isset(static::$MIME_TYPE[$key])){
            return "application/octet-stream";
        }
        return static::$MIME_TYPE[$key];
    }


    /**
     * 根据文件扩展名，返回对应的Content-Type参数的值,默认或没有则返回 application/octet-stream（ 二进制流，未知的文件类型）
     *
     * @param string $extension 文件扩展名
     * @return  string 对应的Content-Type参数的值
     * @author zouyan(305463219@qq.com)
     */
    public static function file_type($extension)
    {
        $content_type = "application/octet-stream";
        switch ($extension)
        {
            case 'html':
            case "log":
            case "php":
            case "phtml":
                $content_type = "text/plain";
                break;
            case "css":
                $content_type = "text/css";
                break;
            case "xml":
            case "xsl":
                $content_type = "text/xml";
                break;
            case "js":
                $content_type = "text/javascript";
                break;
            case "gif":
                $content_type = "image/gif";
                break;
            case "jpeg":
            case "jpg":
                $content_type = "image/jpg";
                break;
            case "png":
                $content_type = "image/png";
                break;
            case "pdf":
                $content_type = "application/pdf";
                break;
            case "doc":
            case "dot":
            case "docx":
                $content_type = "application/msword";
                break;
            case "zip":
                $content_type = "application/zip";
                break;
            case "rar":
                $content_type = "application/rar";
                break;
            case "swf":
                $content_type = "application/x-shockwave-flash";
                break;
            case "bin":
            case "exe":
            case "com":
            case "dll":
            case "class":
                $content_type = "application/octet-steam";
                break;
            default:
                break;
        }
        return $content_type;
    }

    //~~~~~~~~~~~~~~~~~~~~~~~~方式二~~~~~~~不用这个~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * 下载文件--文件路径 +   文件名 --- 读完了再输出 ---只能针对本地文件
     *
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param string $file_dir 文件路径 比如 all  "/xxx/xxx"
     * @param string $file_name 文件名 比如 a.exe  合起来就是全部的路径了all/a.exe
     * @param int $buffer_size 每次读取多少
     * @return  mixed sting 具体错误 ； throws 错误 ；正确 true
     * @author zouyan(305463219@qq.com)
     */
    public static function down_file($errDo = 1, $file_sub_dir = "", $file_name= "", $buffer_size = 1024){
        //死去活来，演示下载一个图片.
        //如果文件是中文.
        //原因 php文件函数，比较古老，需要对中文转码 gb2312
        $file_sub_dir = iconv("utf-8","gb2312",$file_sub_dir);
        $file_name = iconv("utf-8","gb2312",$file_name);
        //绝对路径
//        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_sub_dir . $file_name;
        $file_path = $file_sub_dir . "/" . $file_name;
        //如果你希望绝对路径
        //1.打开文件
        if(!file_exists($file_path)){
            $errMsg = '文件不存在!';
            if($errDo == 1) throws($errMsg);
            return $errMsg;
//            echo "文件不存在!";
//            return ;
        }

//        $fp = fopen($file_path,"r");

        //获取下载文件的大小
        $file_size = filesize($file_path);
//        if($file_size > 30 * 1024){
//            echo "<script language='javascript'>window.alert('过大')</script>";
////          fclose($fp);
//            return ;
//        }
        //返回的文件
        header("Content-type: application/octet-stream");
        //按照字节大小返回
        header("Accept-Ranges: bytes");
        //返回文件大小
        header("Accept-Length: $file_size");
        //这里客户端的弹出对话框，对应的文件名
        header("Content-Disposition: attachment; filename=".$file_name);
        header("Content-type:text/html;charset=utf-8");
        //向客户端回送数据
        echo static::read_file_stream($file_path,1024);
//        $buffer = 1024;
//        //为了下载的安全，我们最好做一个文件字节读取计数器
//        $file_count = 0;
//        //这句话用于判断文件是否结束
//        while(!feof($fp) && ( ($file_size - $file_count) > 0) ){
//            $file_data = fread($fp, $buffer);
//            //统计读了多少个字节
//            $file_count += $buffer;
//            //把部分数据回送给浏览器;
//            echo $file_data;
//        }
//        //关闭文件
//        fclose($fp);
    }

    /**
     *  根据全路径，读取文件内容---只能针对本地文件
     *
     * @param string $file_path 错误处理方式 1 throws 2直接返回错误
     * @param int $buffer 每次读取多少
     * @return  mixed 读取到数据内容
     * @author zouyan(305463219@qq.com)
     */
    public static function read_file_stream($file_path, $buffer = 1024){
        $data_stream = "";
        $fp = fopen($file_path,"r");
        //获取下载文件的大小
        $file_size = filesize($file_path);
        //$buffer=1024;
        //为了下载的安全，我们最好做一个文件字节读取计数器
        $file_count = 0;
        //这句话用于判断文件是否结束
        while(!feof($fp) && ( ($file_size - $file_count) > 0) ){
            $file_data = fread($fp ,$buffer);
            //统计读了多少个字节
            $file_count += $buffer;
            //把部分数据回送给浏览器;
            //echo $file_data;
            $data_stream .= $file_data;
        }
        //关闭文件
        fclose($fp);
        return $data_stream;
    }
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    /**
     * @desc 判断远程图片是否存在
     * @param string $url 远程url
     * @return  boolean  true:存在，false：不存在
     */
    public static function exist_file($url){
        $opts=array(
            'http'=>array(
                'method'=>'HEAD',
                'timeout'=>2
            ));
        @file_get_contents($url,false,stream_context_create($opts));
        if ($http_response_header[0] == 'HTTP/1.1 200 OK') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 功能：获得文件内容
     * @param string $url 远程url
     * @return  string 文件内容
     * @author zouyan(305463219@qq.com)
     */
    public static function getFileContents($url){
        return file_get_contents($url);
    }

    /**
     * 功能：远程文件保存到本地
     * @param string $url 远程url
     * @param string $file_path 文件路径 + 文件名  全部的路径了all/a.exe
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function get($url, $file)
    {
        return file_put_contents($file, file_get_contents($url));
    }
    /**
     * 功能：获得文件内容
     * @param string $url 远程url
     * @return  string 文件内容
     * @author zouyan(305463219@qq.com)
     */
    public static function curlGetFileContents($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $file_content = curl_exec($ch);
        curl_close($ch);
        return $file_content;
    }

    /**
     * 功能：远程文件保存到本地
     * @param string $url 远程url
     * @param string $file_path 文件路径 + 文件名  全部的路径了all/a.exe
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function curlGet($url, $file)
    {
        $file_content = static::curlGetFileContents($url);
        $downloaded_file = fopen($file, 'w');
        fwrite($downloaded_file, $file_content);
        fclose($downloaded_file);
    }

    /**
     * 功能：获得远程文件内容
     *
     * @param string $url 远程url
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param int $buffer 每次读取多少
     * @return  string 读取到数据内容
     * @author zouyan(305463219@qq.com)
     */
    public static function getUrlFileContent($url, $errDo = 1, $buffer = 1024 * 8){
        set_time_limit(0);
        $data_stream = "";
//        $hostfile = fopen("https://github.com/tommy-muehle/puppet-vagrant-boxes/releases/download/1.1.0/centos-7.0-x86_64.box", 'r');
        $hostfile = fopen($url, 'r');

        while (!feof($hostfile)) {
//            $output = fread($hostfile, 8192);
            $output = fread($hostfile, $buffer);
            $data_stream .= $output;
        }
        fclose($hostfile);
        return $data_stream;
    }
    /**
     * 功能：远程文件保存到本地
     *  PHP远程下载大文件方法，防止内存溢出
     *  官方文档链接：http://php.net/manual/zh/ref.curl.php
     *
     * @param string $url 远程url
     * @param string $file_path 文件路径 + 文件名  全部的路径了all/a.exe
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param int $buffer 每次读取多少
     * @return  null 无返回值
     * @author zouyan(305463219@qq.com)
     */
    public static function saveUrlFileToLocal($url, $file_path, $errDo = 1, $buffer = 1024 * 8){
        set_time_limit(0);
//        $hostfile = fopen("https://github.com/tommy-muehle/puppet-vagrant-boxes/releases/download/1.1.0/centos-7.0-x86_64.box", 'r');
        $hostfile = fopen($url, 'r');
//        $fh = fopen("centos-7.0-x86_64.box", 'w');
        $fh = fopen($file_path, 'w');

        while (!feof($hostfile)) {
//            $output = fread($hostfile, 8192);
            $output = fread($hostfile, $buffer);
            fwrite($fh, $output);
        }
        fclose($hostfile);
        fclose($fh);
    }

    /**
     *  $res = DownFile::getUrlFileToLocal($publicPath . $qrcode_url_old, $this->company_id, 3, '',  '');
     * 功能：远程文件保存到本地  ----远程url的或本机的都 可以
     * @param string $url 远程url
     * @param int $company_id 企业id
     * @param string $getSaveType 读取及保存类型 1 file_put_contents file_get_contents 形式; 2 curl 形式 ;3流的形式 [默认]下载大文件方法，防止内存溢出
     * @param string $otherDir 其它路径：如  qrcode   ....  ,注意前后不用加/,会自动在最后加上/  ,为空：自动为'down/'
     * @param  string  $fileName 保存文件名（最好不要加扩展名---系统自动用原文件的扩展名）  'appcode.png' ,可为空：系统处动生成。可没有扩展名：按原文件扩展名 ; 999888777:则按下文件原名
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param int $buffer 每次读取多少
     * @return mixed sting 具体错误 ； throws 错误 ；正确 array
     *   return [
     *    'publicPath' => $sysAbsDir,// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
     *    'savePath' => $filePath,// 文件目录 '/resource/company/1/images/2019/10/04/'
     *    'saveName' => $fileName,// 文件名  20191003121326d710d554edce12a1.png
     *   'files_names' => $files_names,//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
     *   'web_url' => url($files_names),//  网址全路径
     *   'full_names' => $full_names // 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
     *   ];
     * @author zouyan(305463219@qq.com)
     */
    public static function getUrlFileToLocal($url, $company_id, $getSaveType = 3, $otherDir = '', $fileName = '',$errDo = 1, $buffer = 1024 * 8){
        if(strlen($otherDir) <= 0)  $otherDir = 'down/';

        $url_file_name = basename($url);// basename() 函数返回路径中的文件名部分。
        $url_file_extension = pathinfo($url_file_name,PATHINFO_EXTENSION);

        $sysAbsDir = Tool::getPath('public');
        // 文件目录 '/resource/company/1/images/2019/10/04/'
        $filePath = UploadFile::mkDir($sysAbsDir, $company_id, 0, [1,3,4], $otherDir);

        // 系统生成文件名
        if(strlen($fileName) <= 0 )  $fileName = UploadFile::createFileName(30, '');
        // 用原文件名
        if($fileName == '999888777') $fileName = $url_file_name;
        // 判断是否有扩展名,没有则用原文件的
        $suffix = pathinfo($fileName,PATHINFO_EXTENSION);
        if(strlen($suffix) <= 0  && strlen($url_file_extension) > 0) $fileName .= '.' . $url_file_extension;

        $files_names = $filePath . $fileName;
        $full_names = $sysAbsDir . $files_names;

        switch($getSaveType){
            case 1:// 1 file_put_contents file_get_contents 形式;
                static::get($url, $full_names);
                break;
            case 2:// 2 curl 形式 ;
                static::curlGet($url, $full_names);
                break;
            case 3:// 3流的形式 [默认]下载大文件方法，防止内存溢出
                static::saveUrlFileToLocal($url, $full_names, $errDo, $buffer);
                break;
            default:
                static::saveUrlFileToLocal($url, $full_names, $errDo, $buffer);
                break;
        }
        return [
            'publicPath' => $sysAbsDir,// public目录(系统的) 绝对路径 /srv/www/work/work.0101jz.com/public
            'savePath' => $filePath,// 文件目录 '/resource/company/1/images/2019/10/04/'
            'saveName' => $fileName,// 文件名  20191003121326d710d554edce12a1.png
            'files_names' => $files_names,//  文件目录+文件名 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
            'web_url' => url($files_names),//  网址全路径
            'full_names' => $full_names // 服务器中的全路径（目录+文件名）  站点public目录 + 文件目录+文件名 '/data/public/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'
        ];
    }

    /**
     * 功能：获得文件内容----远程url的或本机的都 可以
     * @param string $files_names 站点文件的 '/resource/company/1/images/2019/10/04/20191003121326d710d554edce12a1.png'，系统会自动加上public目录的全路径
     *                            或系统的  /srv/www/runbuy/admin.cunwo.net/public/resource/company/1/down/2019/10/06/20191006102805433363870c5b369e.png
     * @param string $filePathType 文件目录类型 1  站点文件的 【默认】; 2 系统的
     * @param string $getType 读取及保存类型 1 file_get_contents 形式; 2 curl 形式 ;3流的形式 [默认]下载大文件方法，防止内存溢出
     * @param string $errDo 错误处理方式 1 throws 2直接返回错误
     * @param int $buffer 每次读取多少
     * @return  string 文件内容
     * @author zouyan(305463219@qq.com)
     */
    public static function getFileContent($files_names, $filePathType = 1, $getType = 3, $errDo = 1, $buffer = 1024 * 8){
        $file_content = '';
        if($filePathType == 1){
            $sysAbsDir = Tool::getPath('public');
            $files_names = $sysAbsDir . $files_names;
        }
        switch($getType){
            case 1:// 1 file_get_contents 形式;
                $file_content = static::getFileContents($files_names);
                break;
            case 2:// 2 curl 形式 ;
                $file_content = static::curlGetFileContents($files_names);
                break;
            case 3:// 3流的形式 [默认]下载大文件方法，防止内存溢出
                $file_content = static::getUrlFileContent($files_names, $errDo, $buffer);
                break;
            default:
                $file_content = static::getUrlFileContent($files_names, $errDo, $buffer);
                break;
        }
        return $file_content;
    }

}
