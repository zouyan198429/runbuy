<?php
// 购物车
namespace App\Business\DB\RunBuy;

/**
 *
 */
class CartDBBusiness extends BasePublicDBBusiness
{
    public static $model_name = 'RunBuy\Cart';
    public static $table_name = 'cart';// 表名称
}
