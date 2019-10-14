<?php
// 店铺业务类型历史
namespace App\Business\API\RunBuy;


class ShopBusinessHistoryAPIBusiness extends ShopBusinessAPIBusiness
{
    public static $model_name = 'RunBuy\ShopBusinessHistory';
    public static $table_name = 'shop_business_history';// 表名称
}