<?php
// 单号前缀
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBNumPrefixBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\NumPrefix';

}