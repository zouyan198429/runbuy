<?php
// 主名称词
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPINamesBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\NamesAPI';
    public static $table_name = 'names';// 表名称

}