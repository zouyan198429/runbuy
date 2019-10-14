<?php
// 店铺收费记录
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBShopChargeRecordBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\ShopChargeRecord';

}