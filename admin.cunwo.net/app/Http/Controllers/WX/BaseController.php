<?php

namespace App\Http\Controllers\WX;

use App\Http\Controllers\WorksController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BaseController extends WorksController
{
    public $save_session = false;// true后台来的，false小程序来的
    public $source = 3;// 来源-1网站页面，2ajax；3小程序
    public $company_id = 1;//
}
