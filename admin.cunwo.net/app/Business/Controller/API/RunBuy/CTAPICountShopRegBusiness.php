<?php
// 统计店铺注册
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICountShopRegBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\CountShopRegAPI';
    public static $table_name = 'count_shop_reg';// 表名称

}