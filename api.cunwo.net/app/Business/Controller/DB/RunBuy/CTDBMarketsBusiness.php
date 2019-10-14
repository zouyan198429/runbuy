<?php
// 商场[一级分类]
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBMarketsBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\Markets';

}