<?php
// 统计城市合伙人注册
namespace App\Business\Controller\API\RunBuy;


use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPICountPartnerRegBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\CountPartnerRegAPI';
    public static $table_name = 'count_partner_reg';// 表名称

}