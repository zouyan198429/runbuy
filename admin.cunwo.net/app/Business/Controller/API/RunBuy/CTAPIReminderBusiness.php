<?php
// 催单
namespace App\Business\Controller\API\RunBuy;

use App\Services\Excel\ImportExport;
use App\Services\Request\API\HttpRequest;
use App\Services\Tool;
use Illuminate\Http\Request;
use App\Services\Request\CommonRequest;
use App\Http\Controllers\BaseController as Controller;
class CTAPIReminderBusiness extends BasicPublicCTAPIBusiness
{
    public static $model_name = 'API\RunBuy\ReminderAPI';
    public static $table_name = 'reminder';// 表名称

}