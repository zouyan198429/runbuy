<?php
// 订单商品制作单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBOrdersMakeBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\OrdersMake';

}