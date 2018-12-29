<?php
// 支付方式[一级分类]
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBPayTypeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\PayType';

}