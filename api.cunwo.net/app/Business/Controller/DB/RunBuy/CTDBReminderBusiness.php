<?php
// 催单
namespace App\Business\Controller\DB\RunBuy;

use Illuminate\Http\Request;
use App\Http\Controllers\CompController as Controller;
class CTDBReminderBusiness extends BasicPublicCTDBBusiness
{
    public static $model_name = 'RunBuy\Reminder';

}