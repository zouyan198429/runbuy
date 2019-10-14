<?php
// 商场历史[一级分类]
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBMarketsHistoryBusiness extends CTDBMarketsBusiness
{
    public static $model_name = 'RunBuy\MarketsHistory';

}