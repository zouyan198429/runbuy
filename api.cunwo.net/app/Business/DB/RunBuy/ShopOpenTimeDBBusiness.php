<?php
// 店铺营业时间
namespace App\Business\DB\RunBuy;

use Illuminate\Support\Facades\DB;
/**
 *
 */
class ShopOpenTimeDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\ShopOpenTime';
    public static $table_name = 'shop_open_time';// 表名称
}
