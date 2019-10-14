<?php
// 店铺收费记录
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIShopChargeRecordDoingBusiness extends CTAPIShopChargeRecordBusiness
{
    public static $model_name = 'API\RunBuy\ShopChargeRecordDoingAPI';
    public static $table_name = 'shop_charge_record_doing';// 表名称
}