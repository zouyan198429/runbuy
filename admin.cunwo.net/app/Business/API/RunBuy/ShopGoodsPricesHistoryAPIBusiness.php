<?php
// 店铺商品价格历史
namespace App\Business\API\RunBuy;


class ShopGoodsPricesHistoryAPIBusiness extends ShopGoodsPricesAPIBusiness
{
    public static $model_name = 'RunBuy\ShopGoodsPricesHistory';
    public static $table_name = 'shop_goods_prices_history';// 表名称
}