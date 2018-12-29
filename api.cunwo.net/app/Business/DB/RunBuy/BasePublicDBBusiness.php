<?php

namespace App\Business\DB\RunBuy;

use App\Business\DB\BaseDBBusiness;


/**
 *
 */
class BasePublicDBBusiness extends BaseDBBusiness
{
    public static $model_name = '';// 相对于Models的数据模型名称;在子类中定义，使用时用static::,不用self::
    public static $table_name = '';// 表名称
}