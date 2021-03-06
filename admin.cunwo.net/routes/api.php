<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|

*/
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->post ('user/register', 'App\Api\Controllers\UserController@register');// 测试

    $api->group(["namespace" => "App\Http\Controllers\Api\V1",'middleware'=>'auth:api'], function ($api) {
        //之后在这里写api
        // $api->post('decode', 'AccountController@decode');
    });

    $api->group(["namespace" => "App\Http\Controllers\Api\V1"], function ($api) {
        //之前在这里写api
        // $api->post('login', 'AccountController@login');
        $api->get('users/{id}', 'UserController@show');
    });

});

// jwt测试
Route::post('login', 'ApiController@login');
Route::post('register', 'ApiController@register');
Route::post('testaa', 'ApiController@testaa');
Route::post('testbb', 'ApiController@testbb');

Route::group(['middleware' => 'auth.jwt'], function () {
    Route::get('logout', 'ApiController@logout');
    Route::get('usera', 'ApiController@getAuthUser');

    Route::get('products', 'ProductController@index');
    Route::get('products/{id}', 'ProductController@show');
    Route::post('products', 'ProductController@store');
    Route::put('products/{id}', 'ProductController@update');
    Route::delete('products/{id}', 'ProductController@destroy');
});
// 原文链接：https://blog.csdn.net/qq_37788558/article/details/91886363
// 然后在标头请求中添加“Authorization：Bearer {token}”
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Auth\JwtAuthController@login');
    Route::post('logout', 'Auth\JwtAuthController@logout');
    Route::post('refresh', 'Auth\JwtAuthController@refresh');
    Route::post('me', 'Auth\JwtAuthController@me');
});

// 文件上传 any(
// Route::post('file/upload', 'IndexController@upload');
Route::post('upload', 'UploadController@index');
// Route::post('upload/test', 'UploadController@test');
// excel
Route::get('excel/test','ExcelController@test');
Route::get('excel/export','ExcelController@export'); // 导出
Route::get('excel/import','ExcelController@import'); // 导入
Route::get('excel/import_test','ExcelController@import_test'); // 导入 - 测试

// ----大后台
// admin

Route::any('/test', 'IndexController@test');// 测试
// 上传图片
Route::post('admin/upload', 'Admin\UploadController@index');
Route::post('admin/upload/ajax_del', 'Admin\UploadController@ajax_del');// 根据id删除文件

//// 登陆
Route::any('admin/ajax_login', 'Admin\IndexController@ajax_login');// 登陆
Route::post('admin/ajax_password_save', 'Admin\IndexController@ajax_password_save');// 修改密码
Route::any('admin/ajax_info_save', 'Admin\IndexController@ajax_info_save');// 修改设置

//后台--管理员
Route::any('admin/staff/ajax_alist', 'Admin\StaffController@ajax_alist');//ajax获得列表数据
Route::post('admin/staff/ajax_del', 'Admin\StaffController@ajax_del');// 删除
Route::any('admin/staff/ajax_save', 'Admin\StaffController@ajax_save');// 新加/修改
Route::post('admin/staff/ajax_get_child', 'Admin\StaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/staff/ajax_get_areachild', 'Admin\StaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/staff/ajax_import_staff','Admin\StaffController@ajax_import'); // 导入员工

Route::post('admin/staff/import', 'Admin\StaffController@import');// 导入excel
Route::post('admin/staff/ajax_get_ids', 'Admin\StaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//加盟商--管理员
Route::any('admin/staffPartner/ajax_alist', 'Admin\StaffPartnerController@ajax_alist');//ajax获得列表数据
Route::post('admin/staffPartner/ajax_del', 'Admin\StaffPartnerController@ajax_del');// 删除
Route::any('admin/staffPartner/ajax_save', 'Admin\StaffPartnerController@ajax_save');// 新加/修改
Route::post('admin/staffPartner/ajax_get_child', 'Admin\StaffPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/staffPartner/ajax_get_areachild', 'Admin\StaffPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/staffPartner/ajax_import_staff','Admin\StaffPartnerController@ajax_import'); // 导入员工

Route::post('admin/staffPartner/import', 'Admin\StaffPartnerController@import');// 导入excel
Route::post('admin/staffPartner/ajax_get_ids', 'Admin\StaffPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//商家--管理员
Route::any('admin/staffSeller/ajax_alist', 'Admin\StaffSellerController@ajax_alist');//ajax获得列表数据
Route::post('admin/staffSeller/ajax_del', 'Admin\StaffSellerController@ajax_del');// 删除
Route::any('admin/staffSeller/ajax_save', 'Admin\StaffSellerController@ajax_save');// 新加/修改
Route::post('admin/staffSeller/ajax_get_child', 'Admin\StaffSellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/staffSeller/ajax_get_areachild', 'Admin\StaffSellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/staffSeller/ajax_import_staff','Admin\StaffSellerController@ajax_import'); // 导入员工

Route::post('admin/staffSeller/import', 'Admin\StaffSellerController@import');// 导入excel
Route::post('admin/staffSeller/ajax_get_ids', 'Admin\StaffSellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺--管理员
Route::any('admin/staffShop/ajax_alist', 'Admin\StaffShopController@ajax_alist');//ajax获得列表数据
Route::post('admin/staffShop/ajax_del', 'Admin\StaffShopController@ajax_del');// 删除
Route::any('admin/staffShop/ajax_save', 'Admin\StaffShopController@ajax_save');// 新加/修改
Route::post('admin/staffShop/ajax_get_child', 'Admin\StaffShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/staffShop/ajax_get_areachild', 'Admin\StaffShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/staffShop/ajax_import_staff','Admin\StaffShopController@ajax_import'); // 导入员工

Route::post('admin/staffShop/import', 'Admin\StaffShopController@import');// 导入excel
Route::post('admin/staffShop/ajax_get_ids', 'Admin\StaffShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//跑腿--管理员
Route::any('admin/staffRun/ajax_alist', 'Admin\StaffRunController@ajax_alist');//ajax获得列表数据
Route::post('admin/staffRun/ajax_del', 'Admin\StaffRunController@ajax_del');// 删除
Route::any('admin/staffRun/ajax_save', 'Admin\StaffRunController@ajax_save');// 新加/修改
Route::any('admin/staffRun/ajax_save_operate', 'Admin\StaffRunController@ajax_save_operate');// ajax保存数据-操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
Route::post('admin/staffRun/ajax_get_child', 'Admin\StaffRunController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/staffRun/ajax_get_areachild', 'Admin\StaffRunController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/staffRun/ajax_import_staff','Admin\StaffRunController@ajax_import'); // 导入员工

Route::post('admin/staffRun/import', 'Admin\StaffRunController@import');// 导入excel
Route::post('admin/staffRun/ajax_get_ids', 'Admin\StaffRunController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//用户--管理员
Route::any('admin/staffUser/ajax_alist', 'Admin\StaffUserController@ajax_alist');//ajax获得列表数据
Route::post('admin/staffUser/ajax_del', 'Admin\StaffUserController@ajax_del');// 删除
Route::any('admin/staffUser/ajax_save', 'Admin\StaffUserController@ajax_save');// 新加/修改
Route::post('admin/staffUser/ajax_get_child', 'Admin\StaffUserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/staffUser/ajax_get_areachild', 'Admin\StaffUserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/staffUser/ajax_import_staff','Admin\StaffUserController@ajax_import'); // 导入员工

Route::post('admin/staffUser/import', 'Admin\StaffUserController@import');// 导入excel
Route::post('admin/staffUser/ajax_get_ids', 'Admin\StaffUserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//城市
Route::any('admin/city/ajax_alist', 'Admin\CityController@ajax_alist');//ajax获得列表数据
Route::post('admin/city/ajax_del', 'Admin\CityController@ajax_del');// 删除
Route::post('admin/city/ajax_save', 'Admin\CityController@ajax_save');// 新加/修改
Route::post('admin/city/ajax_get_child', 'Admin\CityController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/city/ajax_get_areachild', 'Admin\CityController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/city/ajax_import_staff','Admin\CityController@ajax_import'); // 导入员工

Route::post('admin/city/import', 'Admin\CityController@import');// 导入excel
Route::post('admin/city/ajax_get_ids', 'Admin\CityController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
Route::any('admin/city/ajax_selected', 'Admin\CityController@ajax_selected');//ajax选择中记录/更新记录

//代理
Route::any('admin/cityPartner/ajax_alist', 'Admin\CityPartnerController@ajax_alist');//ajax获得列表数据
Route::post('admin/cityPartner/ajax_del', 'Admin\CityPartnerController@ajax_del');// 删除
Route::post('admin/cityPartner/ajax_save', 'Admin\CityPartnerController@ajax_save');// 新加/修改
Route::post('admin/cityPartner/ajax_get_child', 'Admin\CityPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/cityPartner/ajax_get_areachild', 'Admin\CityPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/cityPartner/ajax_import_staff','Admin\CityPartnerController@ajax_import'); // 导入员工

Route::post('admin/cityPartner/import', 'Admin\CityPartnerController@import');// 导入excel
Route::post('admin/cityPartner/ajax_get_ids', 'Admin\CityPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('admin/cityPartner/ajax_selected', 'Admin\CityPartnerController@ajax_selected');//ajax选择中记录/更新记录
//商家
Route::post('admin/seller/ajax_alist', 'Admin\SellerController@ajax_alist');//ajax获得列表数据
Route::post('admin/seller/ajax_del', 'Admin\SellerController@ajax_del');// 删除
Route::post('admin/seller/ajax_save', 'Admin\SellerController@ajax_save');// 新加/修改
Route::post('admin/seller/ajax_get_child', 'Admin\SellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/seller/ajax_get_areachild', 'Admin\SellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/seller/ajax_import_staff','Admin\SellerController@ajax_import'); // 导入员工

Route::post('admin/seller/import', 'Admin\SellerController@import');// 导入excel
Route::post('admin/seller/ajax_get_ids', 'Admin\SellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('admin/seller/ajax_selected', 'Admin\SellerController@ajax_selected');//ajax选择中记录/更新记录
//店铺分类
Route::any('admin/shopType/ajax_alist', 'Admin\ShopTypeController@ajax_alist');//ajax获得列表数据
Route::post('admin/shopType/ajax_del', 'Admin\ShopTypeController@ajax_del');// 删除
Route::post('admin/shopType/ajax_save', 'Admin\ShopTypeController@ajax_save');// 新加/修改
Route::post('admin/shopType/ajax_get_child', 'Admin\ShopTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/shopType/ajax_get_areachild', 'Admin\ShopTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/shopType/ajax_import_staff','Admin\ShopTypeController@ajax_import'); // 导入员工

Route::post('admin/shopType/import', 'Admin\ShopTypeController@import');// 导入excel
Route::post('admin/shopType/ajax_get_ids', 'Admin\ShopTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺商品属性
Route::any('admin/prop/ajax_alist', 'Admin\PropController@ajax_alist');//ajax获得列表数据
Route::post('admin/prop/ajax_del', 'Admin\PropController@ajax_del');// 删除
Route::any('admin/prop/ajax_save', 'Admin\PropController@ajax_save');// 新加/修改
Route::post('admin/prop/ajax_get_child', 'Admin\PropController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/prop/ajax_get_areachild', 'Admin\PropController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/prop/ajax_import_staff','Admin\PropController@ajax_import'); // 导入员工
Route::any('admin/prop/ajax_pv_used', 'Admin\PropController@ajax_pv_used');// 查询属性值id是否有商品正在使用

Route::post('admin/prop/import', 'Admin\PropController@import');// 导入excel
Route::post('admin/prop/ajax_get_ids', 'Admin\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('admin/prop/ajax_selected', 'Admin\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('admin/prop/ajax_selected_multi', 'Admin\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('admin/shop/ajax_alist', 'Admin\ShopController@ajax_alist');//ajax获得列表数据
Route::post('admin/shop/ajax_del', 'Admin\ShopController@ajax_del');// 删除
Route::any('admin/shop/ajax_save', 'Admin\ShopController@ajax_save');// 新加/修改
Route::any('admin/shop/ajax_save_close', 'Admin\ShopController@ajax_save_close');// 息业
Route::any('admin/shop/ajax_save_open', 'Admin\ShopController@ajax_save_open');// 开业
Route::post('admin/shop/ajax_get_child', 'Admin\ShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/shop/ajax_get_areachild', 'Admin\ShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/shop/ajax_import_staff','Admin\ShopController@ajax_import'); // 导入员工

Route::post('admin/shop/import', 'Admin\ShopController@import');// 导入excel
Route::post('admin/shop/ajax_get_ids', 'Admin\ShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('admin/shop/ajax_selected', 'Admin\ShopController@ajax_selected');//ajax选择中记录/更新记录

//商品
Route::any('admin/shopGoods/ajax_alist', 'Admin\ShopGoodsController@ajax_alist');//ajax获得列表数据
Route::post('admin/shopGoods/ajax_del', 'Admin\ShopGoodsController@ajax_del');// 删除
Route::post('admin/shopGoods/ajax_save', 'Admin\ShopGoodsController@ajax_save');// 新加/修改
Route::post('admin/shopGoods/ajax_get_child', 'Admin\ShopGoodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/shopGoods/ajax_get_areachild', 'Admin\ShopGoodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/shopGoods/ajax_import_staff','Admin\ShopGoodsController@ajax_import'); // 导入员工
Route::any('admin/shopGoods/ajax_get_prop', 'Admin\ShopGoodsController@ajax_get_prop');//ajax初始化属性地址-根据商品id

Route::post('admin/shopGoods/import', 'Admin\ShopGoodsController@import');// 导入excel
Route::post('admin/shopGoods/ajax_get_ids', 'Admin\ShopGoodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('admin/shopGoods/ajax_selected', 'Admin\ShopGoodsController@ajax_selected');//ajax选择中记录/更新记录

//店铺商品分类[一级分类]
Route::post('admin/shopGoodsType/ajax_alist', 'Admin\ShopGoodsTypeController@ajax_alist');//ajax获得列表数据
Route::post('admin/shopGoodsType/ajax_del', 'Admin\ShopGoodsTypeController@ajax_del');// 删除
Route::post('admin/shopGoodsType/ajax_save', 'Admin\ShopGoodsTypeController@ajax_save');// 新加/修改
Route::post('admin/shopGoodsType/ajax_get_child', 'Admin\ShopGoodsTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/shopGoodsType/ajax_get_areachild', 'Admin\ShopGoodsTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/shopGoodsType/ajax_import_staff','Admin\ShopGoodsTypeController@ajax_import'); // 导入员工
Route::any('admin/shopGoodsType/ajax_get_kv', 'Admin\ShopGoodsTypeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('admin/shopGoodsType/import', 'Admin\ShopGoodsTypeController@import');// 导入excel
Route::post('admin/shopGoodsType/ajax_get_ids', 'Admin\ShopGoodsTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//桌位人数分类[一级分类]
Route::any('admin/tablePerson/ajax_alist', 'Admin\TablePersonController@ajax_alist');//ajax获得列表数据
Route::post('admin/tablePerson/ajax_del', 'Admin\TablePersonController@ajax_del');// 删除
Route::post('admin/tablePerson/ajax_save', 'Admin\TablePersonController@ajax_save');// 新加/修改
Route::post('admin/tablePerson/ajax_get_child', 'Admin\TablePersonController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/tablePerson/ajax_get_areachild', 'Admin\TablePersonController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/tablePerson/ajax_import_staff','Admin\TablePersonController@ajax_import'); // 导入员工
Route::any('admin/tablePerson/ajax_get_kv', 'Admin\TablePersonController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('admin/tablePerson/import', 'Admin\TablePersonController@import');// 导入excel
Route::post('admin/tablePerson/ajax_get_ids', 'Admin\TablePersonController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//桌位
Route::any('admin/tables/ajax_alist', 'Admin\TablesController@ajax_alist');//ajax获得列表数据
Route::post('admin/tables/ajax_del', 'Admin\TablesController@ajax_del');// 删除
Route::post('admin/tables/ajax_save', 'Admin\TablesController@ajax_save');// 新加/修改
Route::post('admin/tables/ajax_get_child', 'Admin\TablesController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/tables/ajax_get_areachild', 'Admin\TablesController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/tables/ajax_import_staff','Admin\TablesController@ajax_import'); // 导入员工
Route::any('admin/tables/ajax_get_kv', 'Admin\TablesController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('admin/tables/import', 'Admin\TablesController@import');// 导入excel
Route::post('admin/tables/ajax_get_ids', 'Admin\TablesController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
Route::any('admin/tables/ajax_getCountByStatus', 'Admin\TablesController@ajax_getCountByStatus');//ajax获得统计数据
Route::any('admin/tables/ajax_status_count', 'Admin\TablesController@ajax_status_count');// 状态统计
Route::any('admin/tables/ajax_create_qrcode', 'Admin\TablesController@ajax_create_qrcode');// 生成二维码


//店铺营业时间
Route::post('admin/shopOpenTime/ajax_alist', 'Admin\ShopOpenTimeController@ajax_alist');//ajax获得列表数据
Route::post('admin/shopOpenTime/ajax_del', 'Admin\ShopOpenTimeController@ajax_del');// 删除
Route::post('admin/shopOpenTime/ajax_save', 'Admin\ShopOpenTimeController@ajax_save');// 新加/修改
Route::post('admin/shopOpenTime/ajax_get_child', 'Admin\ShopOpenTimeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/shopOpenTime/ajax_get_areachild', 'Admin\ShopOpenTimeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/shopOpenTime/ajax_import_staff','Admin\ShopOpenTimeController@ajax_import'); // 导入员工
Route::any('admin/shopOpenTime/ajax_get_kv', 'Admin\ShopOpenTimeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('admin/shopOpenTime/import', 'Admin\ShopOpenTimeController@import');// 导入excel
Route::post('admin/shopOpenTime/ajax_get_ids', 'Admin\ShopOpenTimeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//站点介绍
Route::post('admin/siteIntro/ajax_alist', 'Admin\SiteIntroController@ajax_alist');//ajax获得列表数据
Route::post('admin/siteIntro/ajax_del', 'Admin\SiteIntroController@ajax_del');// 删除
Route::post('admin/siteIntro/ajax_save', 'Admin\SiteIntroController@ajax_save');// 新加/修改
Route::post('admin/siteIntro/ajax_get_child', 'Admin\SiteIntroController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/siteIntro/ajax_get_areachild', 'Admin\SiteIntroController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/siteIntro/ajax_import_staff','Admin\SiteIntroController@ajax_import'); // 导入员工

Route::post('admin/siteIntro/import', 'Admin\SiteIntroController@import');// 导入excel
Route::post('admin/siteIntro/ajax_get_ids', 'Admin\SiteIntroController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//站点介绍-跑腿人员
Route::post('admin/siteIntroRuner/ajax_alist', 'Admin\SiteIntroRunerController@ajax_alist');//ajax获得列表数据
Route::post('admin/siteIntroRuner/ajax_del', 'Admin\SiteIntroRunerController@ajax_del');// 删除
Route::post('admin/siteIntroRuner/ajax_save', 'Admin\SiteIntroRunerController@ajax_save');// 新加/修改
Route::post('admin/siteIntroRuner/ajax_get_child', 'Admin\SiteIntroRunerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/siteIntroRuner/ajax_get_areachild', 'Admin\SiteIntroRunerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/siteIntroRuner/ajax_import_staff','Admin\SiteIntroRunerController@ajax_import'); // 导入员工

Route::post('admin/siteIntroRuner/import', 'Admin\SiteIntroRunerController@import');// 导入excel
Route::post('admin/siteIntroRuner/ajax_get_ids', 'Admin\SiteIntroRunerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//标签[一级分类]
Route::post('admin/labels/ajax_alist', 'Admin\LabelsController@ajax_alist');//ajax获得列表数据
Route::post('admin/labels/ajax_del', 'Admin\LabelsController@ajax_del');// 删除
Route::post('admin/labels/ajax_save', 'Admin\LabelsController@ajax_save');// 新加/修改
Route::post('admin/labels/ajax_get_child', 'Admin\LabelsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/labels/ajax_get_areachild', 'Admin\LabelsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/labels/ajax_import_staff','Admin\LabelsController@ajax_import'); // 导入员工


Route::post('admin/labels/import', 'Admin\NumPrefixController@import');// 导入excel
Route::post('admin/labels/ajax_get_ids', 'Admin\NumPrefixController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

// 单号前缀
Route::post('admin/numPrefix/ajax_alist', 'Admin\NumPrefixController@ajax_alist');//ajax获得列表数据
Route::post('admin/numPrefix/ajax_del', 'Admin\NumPrefixController@ajax_del');// 删除
Route::post('admin/numPrefix/ajax_save', 'Admin\NumPrefixController@ajax_save');// 新加/修改
Route::post('admin/numPrefix/ajax_get_child', 'Admin\NumPrefixController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/numPrefix/ajax_get_areachild', 'Admin\NumPrefixController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/numPrefix/ajax_import_staff','Admin\NumPrefixController@ajax_import'); // 导入员工


Route::post('admin/numPrefix/import', 'Admin\NumPrefixController@import');// 导入excel
Route::post('admin/numPrefix/ajax_get_ids', 'Admin\NumPrefixController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//地址[一级分类]
Route::any('admin/commonAddr/ajax_alist', 'Admin\CommonAddrController@ajax_alist');//ajax获得列表数据
Route::post('admin/commonAddr/ajax_del', 'Admin\CommonAddrController@ajax_del');// 删除
Route::post('admin/commonAddr/ajax_save', 'Admin\CommonAddrController@ajax_save');// 新加/修改
Route::post('admin/commonAddr/ajax_get_child', 'Admin\CommonAddrController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/commonAddr/ajax_get_areachild', 'Admin\CommonAddrController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/commonAddr/ajax_import_staff','Admin\CommonAddrController@ajax_import'); // 导入员工


Route::post('admin/commonAddr/import', 'Admin\CommonAddrController@import');// 导入excel
Route::post('admin/commonAddr/ajax_get_ids', 'Admin\CommonAddrController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//公告
Route::any('admin/notice/ajax_alist', 'Admin\NoticeController@ajax_alist');//ajax获得列表数据
Route::post('admin/notice/ajax_del', 'Admin\NoticeController@ajax_del');// 删除
Route::post('admin/notice/ajax_save', 'Admin\NoticeController@ajax_save');// 新加/修改
Route::post('admin/notice/ajax_get_child', 'Admin\NoticeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/notice/ajax_get_areachild', 'Admin\NoticeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/notice/ajax_import_staff','Admin\NoticeController@ajax_import'); // 导入员工

Route::post('admin/notice/import', 'Admin\NoticeController@import');// 导入excel
Route::post('admin/notice/ajax_get_ids', 'Admin\NoticeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//收费标准
Route::any('admin/feeScale/ajax_alist', 'Admin\FeeScaleController@ajax_alist');//ajax获得列表数据
Route::post('admin/feeScale/ajax_del', 'Admin\FeeScaleController@ajax_del');// 删除
Route::post('admin/feeScale/ajax_save', 'Admin\FeeScaleController@ajax_save');// 新加/修改
Route::post('admin/feeScale/ajax_get_child', 'Admin\FeeScaleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/feeScale/ajax_get_areachild', 'Admin\FeeScaleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/feeScale/ajax_import_staff','Admin\FeeScaleController@ajax_import'); // 导入员工

Route::post('admin/feeScale/import', 'Admin\FeeScaleController@import');// 导入excel
Route::post('admin/feeScale/ajax_get_ids', 'Admin\FeeScaleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//收费标准-时间段
Route::any('admin/feeScaleTime/ajax_alist', 'Admin\FeeScaleTimeController@ajax_alist');//ajax获得列表数据
Route::post('admin/feeScaleTime/ajax_del', 'Admin\FeeScaleTimeController@ajax_del');// 删除
Route::post('admin/feeScaleTime/ajax_save', 'Admin\FeeScaleTimeController@ajax_save');// 新加/修改
Route::post('admin/feeScaleTime/ajax_save_bath', 'Admin\FeeScaleTimeController@ajax_save_bath');// 新加/修改--按城市批量
Route::post('admin/feeScaleTime/ajax_get_child', 'Admin\FeeScaleTimeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/feeScaleTime/ajax_get_areachild', 'Admin\FeeScaleTimeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/feeScaleTime/ajax_import_staff','Admin\FeeScaleTimeController@ajax_import'); // 导入员工

Route::post('admin/feeScaleTime/import', 'Admin\FeeScaleTimeController@import');// 导入excel
Route::post('admin/feeScaleTime/ajax_get_ids', 'Admin\FeeScaleTimeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//订单
Route::any('admin/order/ajax_alist', 'Admin\OrdersController@ajax_alist');//ajax获得列表数据
Route::post('admin/order/ajax_del', 'Admin\OrdersController@ajax_del');// 删除
Route::post('admin/order/ajax_save', 'Admin\OrdersController@ajax_save');// 新加/修改
Route::post('admin/order/ajax_get_child', 'Admin\OrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/order/ajax_get_areachild', 'Admin\OrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/order/ajax_import_staff','Admin\OrdersController@ajax_import'); // 导入员工

Route::post('admin/order/import', 'Admin\OrdersController@import');// 导入excel
Route::post('admin/order/ajax_get_ids', 'Admin\OrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
Route::any('admin/order/ajax_getCountByStatus', 'Admin\OrdersController@ajax_getCountByStatus');//ajax获得统计数据
Route::any('admin/order/ajax_status_count', 'Admin\OrdersController@ajax_status_count');// 工单状态统计
Route::any('admin/order/refundOrder', 'Admin\OrdersController@refundOrder');// 退单
Route::any('admin/order/ajax_count_orders', 'Admin\OrdersController@ajax_count_orders');// 统计抢单/订单
// ----城市代理后台
// city
// 上传图片
Route::post('city/upload', 'City\UploadController@index');
Route::post('city/upload/ajax_del', 'City\UploadController@ajax_del');// 根据id删除文件

//// 登陆
Route::any('city/ajax_login', 'City\IndexController@ajax_login');// 登陆
Route::post('city/ajax_password_save', 'City\IndexController@ajax_password_save');// 修改密码
Route::any('city/ajax_info_save', 'City\IndexController@ajax_info_save');// 修改设置

//后台--管理员
//Route::any('city/staff/ajax_alist', 'City\StaffController@ajax_alist');//ajax获得列表数据
//Route::post('city/staff/ajax_del', 'City\StaffController@ajax_del');// 删除
//Route::any('city/staff/ajax_save', 'City\StaffController@ajax_save');// 新加/修改
//Route::post('city/staff/ajax_get_child', 'City\StaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('city/staff/ajax_get_areachild', 'City\StaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('city/staff/ajax_import_staff','City\StaffController@ajax_import'); // 导入员工
//
//Route::post('city/staff/import', 'City\StaffController@import');// 导入excel
//Route::post('city/staff/ajax_get_ids', 'City\StaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//加盟商--管理员
Route::any('city/staffPartner/ajax_alist', 'City\StaffPartnerController@ajax_alist');//ajax获得列表数据
Route::post('city/staffPartner/ajax_del', 'City\StaffPartnerController@ajax_del');// 删除
Route::any('city/staffPartner/ajax_save', 'City\StaffPartnerController@ajax_save');// 新加/修改
Route::post('city/staffPartner/ajax_get_child', 'City\StaffPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/staffPartner/ajax_get_areachild', 'City\StaffPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/staffPartner/ajax_import_staff','City\StaffPartnerController@ajax_import'); // 导入员工

Route::post('city/staffPartner/import', 'City\StaffPartnerController@import');// 导入excel
Route::post('city/staffPartner/ajax_get_ids', 'City\StaffPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//商家--管理员
Route::any('city/staffSeller/ajax_alist', 'City\StaffSellerController@ajax_alist');//ajax获得列表数据
Route::post('city/staffSeller/ajax_del', 'City\StaffSellerController@ajax_del');// 删除
Route::any('city/staffSeller/ajax_save', 'City\StaffSellerController@ajax_save');// 新加/修改
Route::post('city/staffSeller/ajax_get_child', 'City\StaffSellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/staffSeller/ajax_get_areachild', 'City\StaffSellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/staffSeller/ajax_import_staff','City\StaffSellerController@ajax_import'); // 导入员工

Route::post('city/staffSeller/import', 'City\StaffSellerController@import');// 导入excel
Route::post('city/staffSeller/ajax_get_ids', 'City\StaffSellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺--管理员
Route::any('city/staffShop/ajax_alist', 'City\StaffShopController@ajax_alist');//ajax获得列表数据
Route::post('city/staffShop/ajax_del', 'City\StaffShopController@ajax_del');// 删除
Route::any('city/staffShop/ajax_save', 'City\StaffShopController@ajax_save');// 新加/修改
Route::post('city/staffShop/ajax_get_child', 'City\StaffShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/staffShop/ajax_get_areachild', 'City\StaffShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/staffShop/ajax_import_staff','City\StaffShopController@ajax_import'); // 导入员工

Route::post('city/staffShop/import', 'City\StaffShopController@import');// 导入excel
Route::post('city/staffShop/ajax_get_ids', 'City\StaffShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//跑腿--管理员
Route::any('city/staffRun/ajax_alist', 'City\StaffRunController@ajax_alist');//ajax获得列表数据
Route::post('city/staffRun/ajax_del', 'City\StaffRunController@ajax_del');// 删除
Route::any('city/staffRun/ajax_save', 'City\StaffRunController@ajax_save');// 新加/修改
Route::any('city/staffRun/ajax_save_operate', 'City\StaffRunController@ajax_save_operate');// ajax保存数据-操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
Route::post('city/staffRun/ajax_get_child', 'City\StaffRunController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/staffRun/ajax_get_areachild', 'City\StaffRunController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/staffRun/ajax_import_staff','City\StaffRunController@ajax_import'); // 导入员工

Route::post('city/staffRun/import', 'City\StaffRunController@import');// 导入excel
Route::post('city/staffRun/ajax_get_ids', 'City\StaffRunController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//用户--管理员
Route::any('city/staffUser/ajax_alist', 'City\StaffUserController@ajax_alist');//ajax获得列表数据
Route::post('city/staffUser/ajax_del', 'City\StaffUserController@ajax_del');// 删除
Route::any('city/staffUser/ajax_save', 'City\StaffUserController@ajax_save');// 新加/修改
Route::post('city/staffUser/ajax_get_child', 'City\StaffUserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/staffUser/ajax_get_areachild', 'City\StaffUserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/staffUser/ajax_import_staff','City\StaffUserController@ajax_import'); // 导入员工

Route::post('city/staffUser/import', 'City\StaffUserController@import');// 导入excel
Route::post('city/staffUser/ajax_get_ids', 'City\StaffUserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//城市
//Route::any('city/city/ajax_alist', 'City\CityController@ajax_alist');//ajax获得列表数据
//Route::post('city/city/ajax_del', 'City\CityController@ajax_del');// 删除
//Route::post('city/city/ajax_save', 'City\CityController@ajax_save');// 新加/修改
Route::post('city/city/ajax_get_child', 'City\CityController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('city/city/ajax_get_areachild', 'City\CityController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('city/city/ajax_import_staff','City\CityController@ajax_import'); // 导入员工
//
//Route::post('city/city/import', 'City\CityController@import');// 导入excel
//Route::post('city/city/ajax_get_ids', 'City\CityController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//Route::any('city/city/ajax_selected', 'City\CityController@ajax_selected');//ajax选择中记录/更新记录

//代理
//Route::any('city/cityPartner/ajax_alist', 'City\CityPartnerController@ajax_alist');//ajax获得列表数据
//Route::post('city/cityPartner/ajax_del', 'City\CityPartnerController@ajax_del');// 删除
//Route::post('city/cityPartner/ajax_save', 'City\CityPartnerController@ajax_save');// 新加/修改
//Route::post('city/cityPartner/ajax_get_child', 'City\CityPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('city/cityPartner/ajax_get_areachild', 'City\CityPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('city/cityPartner/ajax_import_staff','City\CityPartnerController@ajax_import'); // 导入员工
//
//Route::post('city/cityPartner/import', 'City\CityPartnerController@import');// 导入excel
//Route::post('city/cityPartner/ajax_get_ids', 'City\CityPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//Route::any('city/cityPartner/ajax_selected', 'City\CityPartnerController@ajax_selected');//ajax选择中记录/更新记录
//商家
Route::post('city/seller/ajax_alist', 'City\SellerController@ajax_alist');//ajax获得列表数据
Route::post('city/seller/ajax_del', 'City\SellerController@ajax_del');// 删除
Route::post('city/seller/ajax_save', 'City\SellerController@ajax_save');// 新加/修改
Route::post('city/seller/ajax_get_child', 'City\SellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/seller/ajax_get_areachild', 'City\SellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/seller/ajax_import_staff','City\SellerController@ajax_import'); // 导入员工

Route::post('city/seller/import', 'City\SellerController@import');// 导入excel
Route::post('city/seller/ajax_get_ids', 'City\SellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('city/seller/ajax_selected', 'City\SellerController@ajax_selected');//ajax选择中记录/更新记录
//店铺分类
//Route::post('city/shopType/ajax_alist', 'City\ShopTypeController@ajax_alist');//ajax获得列表数据
//Route::post('city/shopType/ajax_del', 'City\ShopTypeController@ajax_del');// 删除
//Route::post('city/shopType/ajax_save', 'City\ShopTypeController@ajax_save');// 新加/修改
//Route::post('city/shopType/ajax_get_child', 'City\ShopTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('city/shopType/ajax_get_areachild', 'City\ShopTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('city/shopType/ajax_import_staff','City\ShopTypeController@ajax_import'); // 导入员工
//
//Route::post('city/shopType/import', 'City\ShopTypeController@import');// 导入excel
//Route::post('city/shopType/ajax_get_ids', 'City\ShopTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺商品属性
Route::any('city/prop/ajax_alist', 'City\PropController@ajax_alist');//ajax获得列表数据
Route::post('city/prop/ajax_del', 'City\PropController@ajax_del');// 删除
Route::any('city/prop/ajax_save', 'City\PropController@ajax_save');// 新加/修改
Route::post('city/prop/ajax_get_child', 'City\PropController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/prop/ajax_get_areachild', 'City\PropController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/prop/ajax_import_staff','City\PropController@ajax_import'); // 导入员工
Route::any('city/prop/ajax_pv_used', 'City\PropController@ajax_pv_used');// 查询属性值id是否有商品正在使用

Route::post('city/prop/import', 'City\PropController@import');// 导入excel
Route::post('city/prop/ajax_get_ids', 'City\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('city/prop/ajax_selected', 'City\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('city/prop/ajax_selected_multi', 'City\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('city/shop/ajax_alist', 'City\ShopController@ajax_alist');//ajax获得列表数据
Route::post('city/shop/ajax_del', 'City\ShopController@ajax_del');// 删除
Route::any('city/shop/ajax_save', 'City\ShopController@ajax_save');// 新加/修改
Route::any('city/shop/ajax_save_close', 'City\ShopController@ajax_save_close');// 息业
Route::any('city/shop/ajax_save_open', 'City\ShopController@ajax_save_open');// 开业
Route::post('city/shop/ajax_get_child', 'City\ShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/shop/ajax_get_areachild', 'City\ShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/shop/ajax_import_staff','City\ShopController@ajax_import'); // 导入员工

Route::post('city/shop/import', 'City\ShopController@import');// 导入excel
Route::post('city/shop/ajax_get_ids', 'City\ShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('city/shop/ajax_selected', 'City\ShopController@ajax_selected');//ajax选择中记录/更新记录


//商品
Route::any('city/shopGoods/ajax_alist', 'City\ShopGoodsController@ajax_alist');//ajax获得列表数据
Route::post('city/shopGoods/ajax_del', 'City\ShopGoodsController@ajax_del');// 删除
Route::post('city/shopGoods/ajax_save', 'City\ShopGoodsController@ajax_save');// 新加/修改
Route::post('city/shopGoods/ajax_get_child', 'City\ShopGoodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/shopGoods/ajax_get_areachild', 'City\ShopGoodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/shopGoods/ajax_import_staff','City\ShopGoodsController@ajax_import'); // 导入员工
Route::any('city/shopGoods/ajax_get_prop', 'City\ShopGoodsController@ajax_get_prop');//ajax初始化属性地址-根据商品id

Route::post('city/shopGoods/import', 'City\ShopGoodsController@import');// 导入excel
Route::post('city/shopGoods/ajax_get_ids', 'City\ShopGoodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('city/shopGoods/ajax_selected', 'City\ShopGoodsController@ajax_selected');//ajax选择中记录/更新记录
//店铺商品分类[一级分类]
Route::post('city/shopGoodsType/ajax_alist', 'City\ShopGoodsTypeController@ajax_alist');//ajax获得列表数据
Route::post('city/shopGoodsType/ajax_del', 'City\ShopGoodsTypeController@ajax_del');// 删除
Route::post('city/shopGoodsType/ajax_save', 'City\ShopGoodsTypeController@ajax_save');// 新加/修改
Route::post('city/shopGoodsType/ajax_get_child', 'City\ShopGoodsTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/shopGoodsType/ajax_get_areachild', 'City\ShopGoodsTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/shopGoodsType/ajax_import_staff','City\ShopGoodsTypeController@ajax_import'); // 导入员工
Route::any('city/shopGoodsType/ajax_get_kv', 'City\ShopGoodsTypeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('city/shopGoodsType/import', 'City\ShopGoodsTypeController@import');// 导入excel
Route::post('city/shopGoodsType/ajax_get_ids', 'City\ShopGoodsTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//桌位人数分类[一级分类]
Route::any('city/tablePerson/ajax_alist', 'City\TablePersonController@ajax_alist');//ajax获得列表数据
Route::post('city/tablePerson/ajax_del', 'City\TablePersonController@ajax_del');// 删除
Route::post('city/tablePerson/ajax_save', 'City\TablePersonController@ajax_save');// 新加/修改
Route::post('city/tablePerson/ajax_get_child', 'City\TablePersonController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/tablePerson/ajax_get_areachild', 'City\TablePersonController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/tablePerson/ajax_import_staff','City\TablePersonController@ajax_import'); // 导入员工
Route::any('city/tablePerson/ajax_get_kv', 'City\TablePersonController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('city/tablePerson/import', 'City\TablePersonController@import');// 导入excel
Route::post('city/tablePerson/ajax_get_ids', 'City\TablePersonController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺营业时间
Route::post('city/shopOpenTime/ajax_alist', 'City\ShopOpenTimeController@ajax_alist');//ajax获得列表数据
Route::post('city/shopOpenTime/ajax_del', 'City\ShopOpenTimeController@ajax_del');// 删除
Route::post('city/shopOpenTime/ajax_save', 'City\ShopOpenTimeController@ajax_save');// 新加/修改
Route::post('city/shopOpenTime/ajax_get_child', 'City\ShopOpenTimeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/shopOpenTime/ajax_get_areachild', 'City\ShopOpenTimeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/shopOpenTime/ajax_import_staff','City\ShopOpenTimeController@ajax_import'); // 导入员工
Route::any('city/shopOpenTime/ajax_get_kv', 'City\ShopOpenTimeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('city/shopOpenTime/import', 'City\ShopOpenTimeController@import');// 导入excel
Route::post('city/shopOpenTime/ajax_get_ids', 'City\ShopOpenTimeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//站点介绍
//Route::post('city/siteIntro/ajax_alist', 'City\SiteIntroController@ajax_alist');//ajax获得列表数据
//Route::post('city/siteIntro/ajax_del', 'City\SiteIntroController@ajax_del');// 删除
//Route::post('city/siteIntro/ajax_save', 'City\SiteIntroController@ajax_save');// 新加/修改
//Route::post('city/siteIntro/ajax_get_child', 'City\SiteIntroController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('city/siteIntro/ajax_get_areachild', 'City\SiteIntroController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('city/siteIntro/ajax_import_staff','City\SiteIntroController@ajax_import'); // 导入员工
//
//Route::post('city/siteIntro/import', 'City\SiteIntroController@import');// 导入excel
//Route::post('city/siteIntro/ajax_get_ids', 'City\SiteIntroController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//标签[一级分类]
//Route::post('city/labels/ajax_alist', 'City\LabelsController@ajax_alist');//ajax获得列表数据
//Route::post('city/labels/ajax_del', 'City\LabelsController@ajax_del');// 删除
//Route::post('city/labels/ajax_save', 'City\LabelsController@ajax_save');// 新加/修改
//Route::post('city/labels/ajax_get_child', 'City\LabelsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('city/labels/ajax_get_areachild', 'City\LabelsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('city/labels/ajax_import_staff','City\LabelsController@ajax_import'); // 导入员工
//
//Route::post('city/labels/import', 'City\LabelsController@import');// 导入excel
//Route::post('city/labels/ajax_get_ids', 'City\LabelsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//公告
Route::any('city/notice/ajax_alist', 'City\NoticeController@ajax_alist');//ajax获得列表数据
Route::post('city/notice/ajax_del', 'City\NoticeController@ajax_del');// 删除
Route::post('city/notice/ajax_save', 'City\NoticeController@ajax_save');// 新加/修改
Route::post('city/notice/ajax_get_child', 'City\NoticeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/notice/ajax_get_areachild', 'City\NoticeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/notice/ajax_import_staff','City\NoticeController@ajax_import'); // 导入员工

Route::post('city/notice/import', 'City\NoticeController@import');// 导入excel
Route::post('city/notice/ajax_get_ids', 'City\NoticeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//收费标准
Route::any('city/feeScale/ajax_alist', 'City\FeeScaleController@ajax_alist');//ajax获得列表数据
Route::post('city/feeScale/ajax_del', 'City\FeeScaleController@ajax_del');// 删除
Route::post('city/feeScale/ajax_save', 'City\FeeScaleController@ajax_save');// 新加/修改
Route::post('city/feeScale/ajax_get_child', 'City\FeeScaleController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/feeScale/ajax_get_areachild', 'City\FeeScaleController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/feeScale/ajax_import_staff','City\FeeScaleController@ajax_import'); // 导入员工

Route::post('city/feeScale/import', 'City\FeeScaleController@import');// 导入excel
Route::post('city/feeScale/ajax_get_ids', 'City\FeeScaleController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//收费标准-时间段
Route::any('city/feeScaleTime/ajax_alist', 'City\FeeScaleTimeController@ajax_alist');//ajax获得列表数据
Route::post('city/feeScaleTime/ajax_del', 'City\FeeScaleTimeController@ajax_del');// 删除
Route::post('city/feeScaleTime/ajax_save', 'City\FeeScaleTimeController@ajax_save');// 新加/修改
Route::post('city/feeScaleTime/ajax_save_bath', 'City\FeeScaleTimeController@ajax_save_bath');// 新加/修改--按城市批量
Route::post('city/feeScaleTime/ajax_get_child', 'City\FeeScaleTimeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/feeScaleTime/ajax_get_areachild', 'City\FeeScaleTimeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/feeScaleTime/ajax_import_staff','City\FeeScaleTimeController@ajax_import'); // 导入员工

Route::post('city/feeScaleTime/import', 'City\FeeScaleTimeController@import');// 导入excel
Route::post('city/feeScaleTime/ajax_get_ids', 'City\FeeScaleTimeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//订单
Route::any('city/order/ajax_alist', 'City\OrdersController@ajax_alist');//ajax获得列表数据
Route::post('city/order/ajax_del', 'City\OrdersController@ajax_del');// 删除
Route::post('city/order/ajax_save', 'City\OrdersController@ajax_save');// 新加/修改
Route::post('city/order/ajax_get_child', 'City\OrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('city/order/ajax_get_areachild', 'City\OrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('city/order/ajax_import_staff','City\OrdersController@ajax_import'); // 导入员工

Route::post('city/order/import', 'City\OrdersController@import');// 导入excel
Route::post('city/order/ajax_get_ids', 'City\OrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
Route::any('city/order/ajax_getCountByStatus', 'City\OrdersController@ajax_getCountByStatus');//ajax获得统计数据
Route::any('city/order/ajax_status_count', 'City\OrdersController@ajax_status_count');// 工单状态统计
Route::any('city/order/refundOrder', 'City\OrdersController@refundOrder');// 退单
Route::any('city/order/ajax_count_orders', 'City\OrdersController@ajax_count_orders');// 统计抢单/订单
// ----商家后台
// seller
// 上传图片
Route::post('seller/upload', 'Seller\UploadController@index');
Route::post('seller/upload/ajax_del', 'Seller\UploadController@ajax_del');// 根据id删除文件

//// 登陆
Route::any('seller/ajax_login', 'Seller\IndexController@ajax_login');// 登陆
Route::post('seller/ajax_password_save', 'Seller\IndexController@ajax_password_save');// 修改密码
Route::any('seller/ajax_info_save', 'Seller\IndexController@ajax_info_save');// 修改设置

//后台--管理员
//Route::any('seller/staff/ajax_alist', 'Seller\StaffController@ajax_alist');//ajax获得列表数据
//Route::post('seller/staff/ajax_del', 'Seller\StaffController@ajax_del');// 删除
//Route::any('seller/staff/ajax_save', 'Seller\StaffController@ajax_save');// 新加/修改
//Route::post('seller/staff/ajax_get_child', 'Seller\StaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/staff/ajax_get_areachild', 'Seller\StaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/staff/ajax_import_staff','Seller\StaffController@ajax_import'); // 导入员工
//
//Route::post('seller/staff/import', 'Seller\StaffController@import');// 导入excel
//Route::post('seller/staff/ajax_get_ids', 'Seller\StaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//加盟商--管理员
//Route::any('seller/staffPartner/ajax_alist', 'Seller\StaffPartnerController@ajax_alist');//ajax获得列表数据
//Route::post('seller/staffPartner/ajax_del', 'Seller\StaffPartnerController@ajax_del');// 删除
//Route::any('seller/staffPartner/ajax_save', 'Seller\StaffPartnerController@ajax_save');// 新加/修改
//Route::post('seller/staffPartner/ajax_get_child', 'Seller\StaffPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/staffPartner/ajax_get_areachild', 'Seller\StaffPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/staffPartner/ajax_import_staff','Seller\StaffPartnerController@ajax_import'); // 导入员工
//
//Route::post('seller/staffPartner/import', 'Seller\StaffPartnerController@import');// 导入excel
//Route::post('seller/staffPartner/ajax_get_ids', 'Seller\StaffPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//商家--管理员
Route::any('seller/staffSeller/ajax_alist', 'Seller\StaffSellerController@ajax_alist');//ajax获得列表数据
Route::post('seller/staffSeller/ajax_del', 'Seller\StaffSellerController@ajax_del');// 删除
Route::any('seller/staffSeller/ajax_save', 'Seller\StaffSellerController@ajax_save');// 新加/修改
Route::post('seller/staffSeller/ajax_get_child', 'Seller\StaffSellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/staffSeller/ajax_get_areachild', 'Seller\StaffSellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/staffSeller/ajax_import_staff','Seller\StaffSellerController@ajax_import'); // 导入员工

Route::post('seller/staffSeller/import', 'Seller\StaffSellerController@import');// 导入excel
Route::post('seller/staffSeller/ajax_get_ids', 'Seller\StaffSellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺--管理员
Route::any('seller/staffShop/ajax_alist', 'Seller\StaffShopController@ajax_alist');//ajax获得列表数据
Route::post('seller/staffShop/ajax_del', 'Seller\StaffShopController@ajax_del');// 删除
Route::any('seller/staffShop/ajax_save', 'Seller\StaffShopController@ajax_save');// 新加/修改
Route::post('seller/staffShop/ajax_get_child', 'Seller\StaffShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/staffShop/ajax_get_areachild', 'Seller\StaffShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/staffShop/ajax_import_staff','Seller\StaffShopController@ajax_import'); // 导入员工

Route::post('seller/staffShop/import', 'Seller\StaffShopController@import');// 导入excel
Route::post('seller/staffShop/ajax_get_ids', 'Seller\StaffShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//跑腿--管理员
//Route::any('seller/staffRun/ajax_alist', 'Seller\StaffRunController@ajax_alist');//ajax获得列表数据
//Route::post('seller/staffRun/ajax_del', 'Seller\StaffRunController@ajax_del');// 删除
//Route::any('seller/staffRun/ajax_save', 'Seller\StaffRunController@ajax_save');// 新加/修改
//Route::any('seller/staffRun/ajax_save_operate', 'Seller\StaffRunController@ajax_save_operate');// ajax保存数据-操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
//Route::post('seller/staffRun/ajax_get_child', 'Seller\StaffRunController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/staffRun/ajax_get_areachild', 'Seller\StaffRunController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/staffRun/ajax_import_staff','Seller\StaffRunController@ajax_import'); // 导入员工
//
//Route::post('seller/staffRun/import', 'Seller\StaffRunController@import');// 导入excel
//Route::post('seller/staffRun/ajax_get_ids', 'Seller\StaffRunController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//用户--管理员
//Route::any('seller/staffUser/ajax_alist', 'Seller\StaffUserController@ajax_alist');//ajax获得列表数据
//Route::post('seller/staffUser/ajax_del', 'Seller\StaffUserController@ajax_del');// 删除
//Route::any('seller/staffUser/ajax_save', 'Seller\StaffUserController@ajax_save');// 新加/修改
//Route::post('seller/staffUser/ajax_get_child', 'Seller\StaffUserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/staffUser/ajax_get_areachild', 'Seller\StaffUserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/staffUser/ajax_import_staff','Seller\StaffUserController@ajax_import'); // 导入员工
//
//Route::post('seller/staffUser/import', 'Seller\StaffUserController@import');// 导入excel
//Route::post('seller/staffUser/ajax_get_ids', 'Seller\StaffUserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//城市
//Route::any('seller/city/ajax_alist', 'Seller\CityController@ajax_alist');//ajax获得列表数据
//Route::post('seller/city/ajax_del', 'Seller\CityController@ajax_del');// 删除
//Route::post('seller/city/ajax_save', 'Seller\CityController@ajax_save');// 新加/修改
Route::post('seller/city/ajax_get_child', 'Seller\CityController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/city/ajax_get_areachild', 'Seller\CityController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/city/ajax_import_staff','Seller\CityController@ajax_import'); // 导入员工
//
//Route::post('seller/city/import', 'Seller\CityController@import');// 导入excel
//Route::post('seller/city/ajax_get_ids', 'Seller\CityController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//Route::any('seller/city/ajax_selected', 'Seller\CityController@ajax_selected');//ajax选择中记录/更新记录

//代理
//Route::any('seller/cityPartner/ajax_alist', 'Seller\CityPartnerController@ajax_alist');//ajax获得列表数据
//Route::post('seller/cityPartner/ajax_del', 'Seller\CityPartnerController@ajax_del');// 删除
//Route::post('seller/cityPartner/ajax_save', 'Seller\CityPartnerController@ajax_save');// 新加/修改
//Route::post('seller/cityPartner/ajax_get_child', 'Seller\CityPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/cityPartner/ajax_get_areachild', 'Seller\CityPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/cityPartner/ajax_import_staff','Seller\CityPartnerController@ajax_import'); // 导入员工
//
//Route::post('seller/cityPartner/import', 'Seller\CityPartnerController@import');// 导入excel
//Route::post('seller/cityPartner/ajax_get_ids', 'Seller\CityPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//Route::any('seller/cityPartner/ajax_selected', 'Seller\CityPartnerController@ajax_selected');//ajax选择中记录/更新记录
//商家
//Route::post('seller/seller/ajax_alist', 'Seller\SellerController@ajax_alist');//ajax获得列表数据
//Route::post('seller/seller/ajax_del', 'Seller\SellerController@ajax_del');// 删除
//Route::post('seller/seller/ajax_save', 'Seller\SellerController@ajax_save');// 新加/修改
//Route::post('seller/seller/ajax_get_child', 'Seller\SellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/seller/ajax_get_areachild', 'Seller\SellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/seller/ajax_import_staff','Seller\SellerController@ajax_import'); // 导入员工
//
//Route::post('seller/seller/import', 'Seller\SellerController@import');// 导入excel
//Route::post('seller/seller/ajax_get_ids', 'Seller\SellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//Route::any('seller/seller/ajax_selected', 'Seller\SellerController@ajax_selected');//ajax选择中记录/更新记录
//店铺分类
//Route::post('seller/shopType/ajax_alist', 'Seller\ShopTypeController@ajax_alist');//ajax获得列表数据
//Route::post('seller/shopType/ajax_del', 'Seller\ShopTypeController@ajax_del');// 删除
//Route::post('seller/shopType/ajax_save', 'Seller\ShopTypeController@ajax_save');// 新加/修改
//Route::post('seller/shopType/ajax_get_child', 'Seller\ShopTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/shopType/ajax_get_areachild', 'Seller\ShopTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/shopType/ajax_import_staff','Seller\ShopTypeController@ajax_import'); // 导入员工
//
//Route::post('seller/shopType/import', 'Seller\ShopTypeController@import');// 导入excel
//Route::post('seller/shopType/ajax_get_ids', 'Seller\ShopTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺商品属性
Route::any('seller/prop/ajax_alist', 'Seller\PropController@ajax_alist');//ajax获得列表数据
Route::post('seller/prop/ajax_del', 'Seller\PropController@ajax_del');// 删除
Route::any('seller/prop/ajax_save', 'Seller\PropController@ajax_save');// 新加/修改
Route::post('seller/prop/ajax_get_child', 'Seller\PropController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/prop/ajax_get_areachild', 'Seller\PropController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/prop/ajax_import_staff','Seller\PropController@ajax_import'); // 导入员工
Route::any('seller/prop/ajax_pv_used', 'Seller\PropController@ajax_pv_used');// 查询属性值id是否有商品正在使用

Route::post('seller/prop/import', 'Seller\PropController@import');// 导入excel
Route::post('seller/prop/ajax_get_ids', 'Seller\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('seller/prop/ajax_selected', 'Seller\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('seller/prop/ajax_selected_multi', 'Seller\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('seller/shop/ajax_alist', 'Seller\ShopController@ajax_alist');//ajax获得列表数据
Route::post('seller/shop/ajax_del', 'Seller\ShopController@ajax_del');// 删除
Route::any('seller/shop/ajax_save', 'Seller\ShopController@ajax_save');// 新加/修改
Route::any('seller/shop/ajax_save_close', 'Seller\ShopController@ajax_save_close');// 息业
Route::any('seller/shop/ajax_save_open', 'Seller\ShopController@ajax_save_open');// 开业
Route::post('seller/shop/ajax_get_child', 'Seller\ShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/shop/ajax_get_areachild', 'Seller\ShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/shop/ajax_import_staff','Seller\ShopController@ajax_import'); // 导入员工

Route::post('seller/shop/import', 'Seller\ShopController@import');// 导入excel
Route::post('seller/shop/ajax_get_ids', 'Seller\ShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('seller/shop/ajax_selected', 'Seller\ShopController@ajax_selected');//ajax选择中记录/更新记录

//商品
Route::any('seller/shopGoods/ajax_alist', 'Seller\ShopGoodsController@ajax_alist');//ajax获得列表数据
Route::post('seller/shopGoods/ajax_del', 'Seller\ShopGoodsController@ajax_del');// 删除
Route::post('seller/shopGoods/ajax_save', 'Seller\ShopGoodsController@ajax_save');// 新加/修改
Route::post('seller/shopGoods/ajax_get_child', 'Seller\ShopGoodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/shopGoods/ajax_get_areachild', 'Seller\ShopGoodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/shopGoods/ajax_import_staff','Seller\ShopGoodsController@ajax_import'); // 导入员工
Route::any('seller/shopGoods/ajax_get_prop', 'Seller\ShopGoodsController@ajax_get_prop');//ajax初始化属性地址-根据商品id

Route::post('seller/shopGoods/import', 'Seller\ShopGoodsController@import');// 导入excel
Route::post('seller/shopGoods/ajax_get_ids', 'Seller\ShopGoodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('seller/shopGoods/ajax_selected', 'Seller\ShopGoodsController@ajax_selected');//ajax选择中记录/更新记录
//店铺商品分类[一级分类]
Route::post('seller/shopGoodsType/ajax_alist', 'Seller\ShopGoodsTypeController@ajax_alist');//ajax获得列表数据
Route::post('seller/shopGoodsType/ajax_del', 'Seller\ShopGoodsTypeController@ajax_del');// 删除
Route::post('seller/shopGoodsType/ajax_save', 'Seller\ShopGoodsTypeController@ajax_save');// 新加/修改
Route::post('seller/shopGoodsType/ajax_get_child', 'Seller\ShopGoodsTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/shopGoodsType/ajax_get_areachild', 'Seller\ShopGoodsTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/shopGoodsType/ajax_import_staff','Seller\ShopGoodsTypeController@ajax_import'); // 导入员工
Route::any('seller/shopGoodsType/ajax_get_kv', 'Seller\ShopGoodsTypeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('seller/shopGoodsType/import', 'Seller\ShopGoodsTypeController@import');// 导入excel
Route::post('seller/shopGoodsType/ajax_get_ids', 'Seller\ShopGoodsTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//桌位人数分类[一级分类]
Route::any('seller/tablePerson/ajax_alist', 'Seller\TablePersonController@ajax_alist');//ajax获得列表数据
Route::post('seller/tablePerson/ajax_del', 'Seller\TablePersonController@ajax_del');// 删除
Route::post('seller/tablePerson/ajax_save', 'Seller\TablePersonController@ajax_save');// 新加/修改
Route::post('seller/tablePerson/ajax_get_child', 'Seller\TablePersonController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/tablePerson/ajax_get_areachild', 'Seller\TablePersonController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/tablePerson/ajax_import_staff','Seller\TablePersonController@ajax_import'); // 导入员工
Route::any('seller/tablePerson/ajax_get_kv', 'Seller\TablePersonController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('seller/tablePerson/import', 'Seller\TablePersonController@import');// 导入excel
Route::post('seller/tablePerson/ajax_get_ids', 'Seller\TablePersonController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//店铺营业时间
Route::post('seller/shopOpenTime/ajax_alist', 'Seller\ShopOpenTimeController@ajax_alist');//ajax获得列表数据
Route::post('seller/shopOpenTime/ajax_del', 'Seller\ShopOpenTimeController@ajax_del');// 删除
Route::post('seller/shopOpenTime/ajax_save', 'Seller\ShopOpenTimeController@ajax_save');// 新加/修改
Route::post('seller/shopOpenTime/ajax_get_child', 'Seller\ShopOpenTimeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/shopOpenTime/ajax_get_areachild', 'Seller\ShopOpenTimeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/shopOpenTime/ajax_import_staff','Seller\ShopOpenTimeController@ajax_import'); // 导入员工
Route::any('seller/shopOpenTime/ajax_get_kv', 'Seller\ShopOpenTimeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('seller/shopOpenTime/import', 'Seller\ShopOpenTimeController@import');// 导入excel
Route::post('seller/shopOpenTime/ajax_get_ids', 'Seller\ShopOpenTimeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//站点介绍
//Route::post('seller/siteIntro/ajax_alist', 'Seller\SiteIntroController@ajax_alist');//ajax获得列表数据
//Route::post('seller/siteIntro/ajax_del', 'Seller\SiteIntroController@ajax_del');// 删除
//Route::post('seller/siteIntro/ajax_save', 'Seller\SiteIntroController@ajax_save');// 新加/修改
//Route::post('seller/siteIntro/ajax_get_child', 'Seller\SiteIntroController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/siteIntro/ajax_get_areachild', 'Seller\SiteIntroController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/siteIntro/ajax_import_staff','Seller\SiteIntroController@ajax_import'); // 导入员工
//
//Route::post('seller/siteIntro/import', 'Seller\SiteIntroController@import');// 导入excel
//Route::post('seller/siteIntro/ajax_get_ids', 'Seller\SiteIntroController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//标签[一级分类]
//Route::post('seller/labels/ajax_alist', 'Seller\LabelsController@ajax_alist');//ajax获得列表数据
//Route::post('seller/labels/ajax_del', 'Seller\LabelsController@ajax_del');// 删除
//Route::post('seller/labels/ajax_save', 'Seller\LabelsController@ajax_save');// 新加/修改
//Route::post('seller/labels/ajax_get_child', 'Seller\LabelsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/labels/ajax_get_areachild', 'Seller\LabelsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/labels/ajax_import_staff','Seller\LabelsController@ajax_import'); // 导入员工
//
//Route::post('seller/labels/import', 'Seller\LabelsController@import');// 导入excel
//Route::post('seller/labels/ajax_get_ids', 'Seller\LabelsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//公告
//Route::any('seller/notice/ajax_alist', 'Seller\NoticeController@ajax_alist');//ajax获得列表数据
//Route::post('seller/notice/ajax_del', 'Seller\NoticeController@ajax_del');// 删除
//Route::post('seller/notice/ajax_save', 'Seller\NoticeController@ajax_save');// 新加/修改
//Route::post('seller/notice/ajax_get_child', 'Seller\NoticeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('seller/notice/ajax_get_areachild', 'Seller\NoticeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('seller/notice/ajax_import_staff','Seller\NoticeController@ajax_import'); // 导入员工
//
//Route::post('seller/notice/import', 'Seller\NoticeController@import');// 导入excel
//Route::post('seller/notice/ajax_get_ids', 'Seller\NoticeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//订单
Route::any('seller/order/ajax_alist', 'Seller\OrdersController@ajax_alist');//ajax获得列表数据
Route::post('seller/order/ajax_del', 'Seller\OrdersController@ajax_del');// 删除
Route::post('seller/order/ajax_save', 'Seller\OrdersController@ajax_save');// 新加/修改
Route::post('seller/order/ajax_get_child', 'Seller\OrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('seller/order/ajax_get_areachild', 'Seller\OrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('seller/order/ajax_import_staff','Seller\OrdersController@ajax_import'); // 导入员工

Route::post('seller/order/import', 'Seller\OrdersController@import');// 导入excel
Route::post('seller/order/ajax_get_ids', 'Seller\OrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
Route::any('seller/order/ajax_getCountByStatus', 'Seller\OrdersController@ajax_getCountByStatus');//ajax获得统计数据
Route::any('seller/order/ajax_status_count', 'Seller\OrdersController@ajax_status_count');// 工单状态统计
Route::any('seller/order/refundOrder', 'Seller\OrdersController@refundOrder');// 退单


// ----店铺后台
// seller
// 上传图片
Route::post('shop/upload', 'Shop\UploadController@index');
Route::post('shop/upload/ajax_del', 'Shop\UploadController@ajax_del');// 根据id删除文件

//// 登陆
Route::any('shop/ajax_login', 'Shop\IndexController@ajax_login');// 登陆
Route::post('shop/ajax_password_save', 'Shop\IndexController@ajax_password_save');// 修改密码
Route::any('shop/ajax_info_save', 'Shop\IndexController@ajax_info_save');// 修改设置

//后台--管理员
//Route::any('shop/staff/ajax_alist', 'Shop\StaffController@ajax_alist');//ajax获得列表数据
//Route::post('shop/staff/ajax_del', 'Shop\StaffController@ajax_del');// 删除
//Route::any('shop/staff/ajax_save', 'Shop\StaffController@ajax_save');// 新加/修改
//Route::post('shop/staff/ajax_get_child', 'Shop\StaffController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/staff/ajax_get_areachild', 'Shop\StaffController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/staff/ajax_import_staff','Shop\StaffController@ajax_import'); // 导入员工
//
//Route::post('shop/staff/import', 'Shop\StaffController@import');// 导入excel
//Route::post('shop/staff/ajax_get_ids', 'Shop\StaffController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//加盟商--管理员
//Route::any('shop/staffPartner/ajax_alist', 'Shop\StaffPartnerController@ajax_alist');//ajax获得列表数据
//Route::post('shop/staffPartner/ajax_del', 'Shop\StaffPartnerController@ajax_del');// 删除
//Route::any('shop/staffPartner/ajax_save', 'Shop\StaffPartnerController@ajax_save');// 新加/修改
//Route::post('shop/staffPartner/ajax_get_child', 'Shop\StaffPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/staffPartner/ajax_get_areachild', 'Shop\StaffPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/staffPartner/ajax_import_staff','Shop\StaffPartnerController@ajax_import'); // 导入员工
//
//Route::post('shop/staffPartner/import', 'Shop\StaffPartnerController@import');// 导入excel
//Route::post('shop/staffPartner/ajax_get_ids', 'Shop\StaffPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//商家--管理员
//Route::any('shop/staffSeller/ajax_alist', 'Shop\StaffSellerController@ajax_alist');//ajax获得列表数据
//Route::post('shop/staffSeller/ajax_del', 'Shop\StaffSellerController@ajax_del');// 删除
//Route::any('shop/staffSeller/ajax_save', 'Shop\StaffSellerController@ajax_save');// 新加/修改
//Route::post('shop/staffSeller/ajax_get_child', 'Shop\StaffSellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/staffSeller/ajax_get_areachild', 'Shop\StaffSellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/staffSeller/ajax_import_staff','Shop\StaffSellerController@ajax_import'); // 导入员工
//
//Route::post('shop/staffSeller/import', 'Shop\StaffSellerController@import');// 导入excel
//Route::post('shop/staffSeller/ajax_get_ids', 'Shop\StaffSellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺--管理员
Route::any('shop/staffShop/ajax_alist', 'Shop\StaffShopController@ajax_alist');//ajax获得列表数据
Route::post('shop/staffShop/ajax_del', 'Shop\StaffShopController@ajax_del');// 删除
Route::any('shop/staffShop/ajax_save', 'Shop\StaffShopController@ajax_save');// 新加/修改
Route::post('shop/staffShop/ajax_get_child', 'Shop\StaffShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/staffShop/ajax_get_areachild', 'Shop\StaffShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/staffShop/ajax_import_staff','Shop\StaffShopController@ajax_import'); // 导入员工

Route::post('shop/staffShop/import', 'Shop\StaffShopController@import');// 导入excel
Route::post('shop/staffShop/ajax_get_ids', 'Shop\StaffShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//跑腿--管理员
//Route::any('shop/staffRun/ajax_alist', 'Shop\StaffRunController@ajax_alist');//ajax获得列表数据
//Route::post('shop/staffRun/ajax_del', 'Shop\StaffRunController@ajax_del');// 删除
//Route::any('shop/staffRun/ajax_save', 'Shop\StaffRunController@ajax_save');// 新加/修改
//Route::any('shop/staffRun/ajax_save_operate', 'Shop\StaffRunController@ajax_save_operate');// ajax保存数据-操作类型 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班
//Route::post('shop/staffRun/ajax_get_child', 'Shop\StaffRunController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/staffRun/ajax_get_areachild', 'Shop\StaffRunController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/staffRun/ajax_import_staff','Shop\StaffRunController@ajax_import'); // 导入员工
//
//Route::post('shop/staffRun/import', 'Shop\StaffRunController@import');// 导入excel
//Route::post('shop/staffRun/ajax_get_ids', 'Shop\StaffRunController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//用户--管理员
//Route::any('shop/staffUser/ajax_alist', 'Shop\StaffUserController@ajax_alist');//ajax获得列表数据
//Route::post('shop/staffUser/ajax_del', 'Shop\StaffUserController@ajax_del');// 删除
//Route::any('shop/staffUser/ajax_save', 'Shop\StaffUserController@ajax_save');// 新加/修改
//Route::post('shop/staffUser/ajax_get_child', 'Shop\StaffUserController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/staffUser/ajax_get_areachild', 'Shop\StaffUserController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/staffUser/ajax_import_staff','Shop\StaffUserController@ajax_import'); // 导入员工
//
//Route::post('shop/staffUser/import', 'Shop\StaffUserController@import');// 导入excel
//Route::post('shop/staffUser/ajax_get_ids', 'Shop\StaffUserController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//城市
//Route::any('shop/city/ajax_alist', 'Shop\CityController@ajax_alist');//ajax获得列表数据
//Route::post('shop/city/ajax_del', 'Shop\CityController@ajax_del');// 删除
//Route::post('shop/city/ajax_save', 'Shop\CityController@ajax_save');// 新加/修改
Route::post('shop/city/ajax_get_child', 'Shop\CityController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/city/ajax_get_areachild', 'Shop\CityController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/city/ajax_import_staff','Shop\CityController@ajax_import'); // 导入员工
//
//Route::post('shop/city/import', 'Shop\CityController@import');// 导入excel
//Route::post('shop/city/ajax_get_ids', 'Shop\CityController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//Route::any('shop/city/ajax_selected', 'Shop\CityController@ajax_selected');//ajax选择中记录/更新记录

//代理
//Route::any('shop/cityPartner/ajax_alist', 'Shop\CityPartnerController@ajax_alist');//ajax获得列表数据
//Route::post('shop/cityPartner/ajax_del', 'Shop\CityPartnerController@ajax_del');// 删除
//Route::post('shop/cityPartner/ajax_save', 'Shop\CityPartnerController@ajax_save');// 新加/修改
//Route::post('shop/cityPartner/ajax_get_child', 'Shop\CityPartnerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/cityPartner/ajax_get_areachild', 'Shop\CityPartnerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/cityPartner/ajax_import_staff','Shop\CityPartnerController@ajax_import'); // 导入员工
//
//Route::post('shop/cityPartner/import', 'Shop\CityPartnerController@import');// 导入excel
//Route::post('shop/cityPartner/ajax_get_ids', 'Shop\CityPartnerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//Route::any('shop/cityPartner/ajax_selected', 'Shop\CityPartnerController@ajax_selected');//ajax选择中记录/更新记录
//商家
//Route::post('shop/seller/ajax_alist', 'Shop\SellerController@ajax_alist');//ajax获得列表数据
//Route::post('shop/seller/ajax_del', 'Shop\SellerController@ajax_del');// 删除
//Route::post('shop/seller/ajax_save', 'Shop\SellerController@ajax_save');// 新加/修改
//Route::post('shop/seller/ajax_get_child', 'Shop\SellerController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/seller/ajax_get_areachild', 'Shop\SellerController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/seller/ajax_import_staff','Shop\SellerController@ajax_import'); // 导入员工
//
//Route::post('shop/seller/import', 'Shop\SellerController@import');// 导入excel
//Route::post('shop/seller/ajax_get_ids', 'Shop\SellerController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
//
//Route::any('shop/seller/ajax_selected', 'Shop\SellerController@ajax_selected');//ajax选择中记录/更新记录
//店铺分类
//Route::post('shop/shopType/ajax_alist', 'Shop\ShopTypeController@ajax_alist');//ajax获得列表数据
//Route::post('shop/shopType/ajax_del', 'Shop\ShopTypeController@ajax_del');// 删除
//Route::post('shop/shopType/ajax_save', 'Shop\ShopTypeController@ajax_save');// 新加/修改
//Route::post('shop/shopType/ajax_get_child', 'Shop\ShopTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/shopType/ajax_get_areachild', 'Shop\ShopTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/shopType/ajax_import_staff','Shop\ShopTypeController@ajax_import'); // 导入员工
//
//Route::post('shop/shopType/import', 'Shop\ShopTypeController@import');// 导入excel
//Route::post('shop/shopType/ajax_get_ids', 'Shop\ShopTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//店铺商品属性
Route::any('shop/prop/ajax_alist', 'Shop\PropController@ajax_alist');//ajax获得列表数据
Route::post('shop/prop/ajax_del', 'Shop\PropController@ajax_del');// 删除
Route::any('shop/prop/ajax_save', 'Shop\PropController@ajax_save');// 新加/修改
Route::post('shop/prop/ajax_get_child', 'Shop\PropController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/prop/ajax_get_areachild', 'Shop\PropController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/prop/ajax_import_staff','Shop\PropController@ajax_import'); // 导入员工
Route::any('shop/prop/ajax_pv_used', 'Shop\PropController@ajax_pv_used');// 查询属性值id是否有商品正在使用

Route::post('shop/prop/import', 'Shop\PropController@import');// 导入excel
Route::post('shop/prop/ajax_get_ids', 'Shop\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('shop/prop/ajax_selected', 'Shop\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('shop/prop/ajax_selected_multi', 'Shop\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('shop/shop/ajax_alist', 'Shop\ShopController@ajax_alist');//ajax获得列表数据
Route::post('shop/shop/ajax_del', 'Shop\ShopController@ajax_del');// 删除
Route::any('shop/shop/ajax_save', 'Shop\ShopController@ajax_save');// 新加/修改
Route::any('shop/shop/ajax_save_close', 'Shop\ShopController@ajax_save_close');// 息业
Route::any('shop/shop/ajax_save_open', 'Shop\ShopController@ajax_save_open');// 开业
Route::post('shop/shop/ajax_get_child', 'Shop\ShopController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/shop/ajax_get_areachild', 'Shop\ShopController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/shop/ajax_import_staff','Shop\ShopController@ajax_import'); // 导入员工

Route::post('shop/shop/import', 'Shop\ShopController@import');// 导入excel
Route::post('shop/shop/ajax_get_ids', 'Shop\ShopController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('shop/shop/ajax_selected', 'Shop\ShopController@ajax_selected');//ajax选择中记录/更新记录

//商品
Route::any('shop/shopGoods/ajax_alist', 'Shop\ShopGoodsController@ajax_alist');//ajax获得列表数据
Route::post('shop/shopGoods/ajax_del', 'Shop\ShopGoodsController@ajax_del');// 删除
Route::post('shop/shopGoods/ajax_save', 'Shop\ShopGoodsController@ajax_save');// 新加/修改
Route::post('shop/shopGoods/ajax_get_child', 'Shop\ShopGoodsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/shopGoods/ajax_get_areachild', 'Shop\ShopGoodsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/shopGoods/ajax_import_staff','Shop\ShopGoodsController@ajax_import'); // 导入员工
Route::any('shop/shopGoods/ajax_get_prop', 'Shop\ShopGoodsController@ajax_get_prop');//ajax初始化属性地址-根据商品id

Route::post('shop/shopGoods/import', 'Shop\ShopGoodsController@import');// 导入excel
Route::post('shop/shopGoods/ajax_get_ids', 'Shop\ShopGoodsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('shop/shopGoods/ajax_selected', 'Shop\ShopGoodsController@ajax_selected');//ajax选择中记录/更新记录
//店铺商品分类[一级分类]
Route::post('shop/shopGoodsType/ajax_alist', 'Shop\ShopGoodsTypeController@ajax_alist');//ajax获得列表数据
Route::post('shop/shopGoodsType/ajax_del', 'Shop\ShopGoodsTypeController@ajax_del');// 删除
Route::post('shop/shopGoodsType/ajax_save', 'Shop\ShopGoodsTypeController@ajax_save');// 新加/修改
Route::post('shop/shopGoodsType/ajax_get_child', 'Shop\ShopGoodsTypeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/shopGoodsType/ajax_get_areachild', 'Shop\ShopGoodsTypeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/shopGoodsType/ajax_import_staff','Shop\ShopGoodsTypeController@ajax_import'); // 导入员工
Route::any('shop/shopGoodsType/ajax_get_kv', 'Shop\ShopGoodsTypeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('shop/shopGoodsType/import', 'Shop\ShopGoodsTypeController@import');// 导入excel
Route::post('shop/shopGoodsType/ajax_get_ids', 'Shop\ShopGoodsTypeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔



//桌位人数分类[一级分类]
Route::any('shop/tablePerson/ajax_alist', 'Shop\TablePersonController@ajax_alist');//ajax获得列表数据
Route::post('shop/tablePerson/ajax_del', 'Shop\TablePersonController@ajax_del');// 删除
Route::post('shop/tablePerson/ajax_save', 'Shop\TablePersonController@ajax_save');// 新加/修改
Route::post('shop/tablePerson/ajax_get_child', 'Shop\TablePersonController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/tablePerson/ajax_get_areachild', 'Shop\TablePersonController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/tablePerson/ajax_import_staff','Shop\TablePersonController@ajax_import'); // 导入员工
Route::any('shop/tablePerson/ajax_get_kv', 'Shop\TablePersonController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('shop/tablePerson/import', 'Shop\TablePersonController@import');// 导入excel
Route::post('shop/tablePerson/ajax_get_ids', 'Shop\TablePersonController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//店铺营业时间
Route::post('shop/shopOpenTime/ajax_alist', 'Shop\ShopOpenTimeController@ajax_alist');//ajax获得列表数据
Route::post('shop/shopOpenTime/ajax_del', 'Shop\ShopOpenTimeController@ajax_del');// 删除
Route::post('shop/shopOpenTime/ajax_save', 'Shop\ShopOpenTimeController@ajax_save');// 新加/修改
Route::post('shop/shopOpenTime/ajax_get_child', 'Shop\ShopOpenTimeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/shopOpenTime/ajax_get_areachild', 'Shop\ShopOpenTimeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/shopOpenTime/ajax_import_staff','Shop\ShopOpenTimeController@ajax_import'); // 导入员工
Route::any('shop/shopOpenTime/ajax_get_kv', 'Shop\ShopOpenTimeController@ajax_get_kv');// 根据店铺id，获得店铺分类信息

Route::post('shop/shopOpenTime/import', 'Shop\ShopOpenTimeController@import');// 导入excel
Route::post('shop/shopOpenTime/ajax_get_ids', 'Shop\ShopOpenTimeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔


//站点介绍
//Route::post('shop/siteIntro/ajax_alist', 'Shop\SiteIntroController@ajax_alist');//ajax获得列表数据
//Route::post('shop/siteIntro/ajax_del', 'Shop\SiteIntroController@ajax_del');// 删除
//Route::post('shop/siteIntro/ajax_save', 'Shop\SiteIntroController@ajax_save');// 新加/修改
//Route::post('shop/siteIntro/ajax_get_child', 'Shop\SiteIntroController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/siteIntro/ajax_get_areachild', 'Shop\SiteIntroController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/siteIntro/ajax_import_staff','Shop\SiteIntroController@ajax_import'); // 导入员工
//
//Route::post('shop/siteIntro/import', 'Shop\SiteIntroController@import');// 导入excel
//Route::post('shop/siteIntro/ajax_get_ids', 'Shop\SiteIntroController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//标签[一级分类]
//Route::post('shop/labels/ajax_alist', 'Shop\LabelsController@ajax_alist');//ajax获得列表数据
//Route::post('shop/labels/ajax_del', 'Shop\LabelsController@ajax_del');// 删除
//Route::post('shop/labels/ajax_save', 'Shop\LabelsController@ajax_save');// 新加/修改
//Route::post('shop/labels/ajax_get_child', 'Shop\LabelsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/labels/ajax_get_areachild', 'Shop\LabelsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/labels/ajax_import_staff','Shop\LabelsController@ajax_import'); // 导入员工
//
//Route::post('shop/labels/import', 'Shop\LabelsController@import');// 导入excel
//Route::post('shop/labels/ajax_get_ids', 'Shop\LabelsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//公告
//Route::any('shop/notice/ajax_alist', 'Shop\NoticeController@ajax_alist');//ajax获得列表数据
//Route::post('shop/notice/ajax_del', 'Shop\NoticeController@ajax_del');// 删除
//Route::post('shop/notice/ajax_save', 'Shop\NoticeController@ajax_save');// 新加/修改
//Route::post('shop/notice/ajax_get_child', 'Shop\NoticeController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
//Route::post('shop/notice/ajax_get_areachild', 'Shop\NoticeController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
//Route::post('shop/notice/ajax_import_staff','Shop\NoticeController@ajax_import'); // 导入员工
//
//Route::post('shop/notice/import', 'Shop\NoticeController@import');// 导入excel
//Route::post('shop/notice/ajax_get_ids', 'Shop\NoticeController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//订单
Route::any('shop/order/ajax_alist', 'Shop\OrdersController@ajax_alist');//ajax获得列表数据
Route::post('shop/order/ajax_del', 'Shop\OrdersController@ajax_del');// 删除
Route::post('shop/order/ajax_save', 'Shop\OrdersController@ajax_save');// 新加/修改
Route::post('shop/order/ajax_get_child', 'Shop\OrdersController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('shop/order/ajax_get_areachild', 'Shop\OrdersController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('shop/order/ajax_import_staff','Shop\OrdersController@ajax_import'); // 导入员工

Route::post('shop/order/import', 'Shop\OrdersController@import');// 导入excel
Route::post('shop/order/ajax_get_ids', 'Shop\OrdersController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔
Route::any('shop/order/ajax_getCountByStatus', 'Shop\OrdersController@ajax_getCountByStatus');//ajax获得统计数据
Route::any('shop/order/ajax_status_count', 'Shop\OrdersController@ajax_status_count');// 工单状态统计
Route::any('shop/order/refundOrder', 'Shop\OrdersController@refundOrder');// 退单


// 微信相关的
// 一定是 Route::any, 因为微信服务端认证的时候是 GET, 接收用户消息时是 POST ！
Route::any('wx/wechat', 'WX\WeChatController@index');
Route::any('wx/jssdkconfig', 'WX\WeChatController@getJSSDKConfig');

Route::any('wx/test', 'WX\WeChatController@test');
// oauth
Route::any('wx/profile', 'WX\WeChatController@profile');// 需要授权才能访问的页面
Route::any('wx/callback', 'WX\WeChatController@callback');// 授权回调页

// 小程序相关的

// 上传图片
Route::post('upload', 'WX\UploadController@index');
Route::post('upload/ajax_del', 'WX\UploadController@ajax_del');// 根据id删除文件


// 搜索标签
// Route::any('staff/ajax_alist', 'WX\StaffController@ajax_alist');//ajax获得列表数据
Route::any('staff/ajax_info', 'WX\StaffController@ajax_info');//ajax获得详情数据
Route::post('staff/ajax_save', 'WX\StaffController@ajax_save');// 新加/修改
Route::any('staff/ajax_save_operate', 'WX\StaffController@ajax_save_operate');//ajax操作类型  operate_type 1 提交申请修改信息 ;2 审核通过 3 审核不通过 4 冻结 5 解冻 6 上班 7 下班



Route::any('miniProgram/test', 'WX\MiniProgramController@test');// 测试
Route::any('miniProgram/login', 'WX\MiniProgramController@ajax_login');// 登陆-用户
Route::any('miniProgram/login_run', 'WX\MiniProgramController@ajax_login_run');// 登陆-快跑人员小程序
Route::any('miniProgram/login_app', 'WX\MiniProgramController@ajax_login_app');// 登陆-快跑人员app

// 平台相关的
// Route::any('platForm/getLabels', 'WX\platFormController@getLabels');// 获得店铺标签--有分页
// Route::any('platForm/getAllLabel', 'WX\platFormController@getAllLabel');// 获得店铺标签--所有的
// Route::any('platForm/getShopTypes', 'WX\platFormController@getShopTypes');// 获得店铺分类--有分页
// Route::any('platForm/getAllShopType', 'WX\platFormController@getAllShopType');// 获得店铺分类--所有的
// Route::any('platForm/getNotes', 'WX\platFormController@getNotes');// 获得公告--有分页
// Route::any('platForm/getNoteInfo', 'WX\platFormController@getNoteInfo');// 根据id获得公告详情
Route::any('platForm/feeScale', 'WX\platFormController@feeScale');// 根据城市id,获得收费标准

//公告
Route::any('notice/ajax_alist', 'WX\NoticeController@ajax_alist');//ajax获得列表数据
Route::any('notice/ajax_info/{id}', 'WX\NoticeController@ajax_info');//ajax获得详情数据
Route::any('notice/ajax_infoByCityId/{city_id}', 'WX\NoticeController@ajax_infoByCityId');//ajax获得详情数据--根据城市id

// 搜索标签
Route::any('labels/ajax_alist', 'WX\LabelsController@ajax_alist');//ajax获得列表数据
Route::any('labels/ajax_info/{id}', 'WX\LabelsController@ajax_info');//ajax获得详情数据

// 店铺分类
Route::any('shopType/ajax_alist', 'WX\ShopTypeController@ajax_alist');//ajax获得列表数据
Route::any('shopType/ajax_info/{id}', 'WX\ShopTypeController@ajax_info');//ajax获得详情数据

//站点介绍
Route::any('siteIntro/ajax_alist', 'WX\SiteIntroController@ajax_alist');//ajax获得列表数据
Route::any('siteIntro/ajax_info/{id}', 'WX\SiteIntroController@ajax_info');//ajax获得详情数据

//站点介绍-跑腿人员
Route::any('siteIntroRuner/ajax_alist', 'WX\SiteIntroRunerController@ajax_alist');//ajax获得列表数据
Route::any('siteIntroRuner/ajax_info/{id}', 'WX\SiteIntroRunerController@ajax_info');//ajax获得详情数据

//收费标准
Route::any('feeScale/ajax_alist', 'WX\FeeScaleController@ajax_alist');//ajax获得列表数据
Route::any('feeScale/ajax_info/{id}', 'WX\FeeScaleController@ajax_info');//ajax获得详情数据
Route::any('feeScale/ajax_infoByCityId/{city_id}', 'WX\FeeScaleController@ajax_infoByCityId');//ajax获得详情数据--根据城市id


// 帮助相关的
// Route::any('help/siteIntroList', 'WX\HelpController@siteIntroList');// 获得站点介绍列表--所有
// Route::any('help/siteIntroInfo', 'WX\HelpController@siteIntroInfo');// 获得站点介绍详情


// 城市相关的
Route::any('city/getNearCity', 'WX\CityController@getNearCity');// 根据经纬度坐标，获得最近的城市信息
Route::any('city/getCitys', 'WX\CityController@getCitys');// 获得所有的城市信息
Route::any('city/getOrderSaturation', 'WX\CityController@getOrderSaturation');// 获得订单饱和度

// 店铺相关的
Route::any('shop/ajax_alist', 'WX\ShopController@ajax_alist');//ajax获得列表数据
Route::any('shop/ajax_info/{id}', 'WX\ShopController@ajax_info');//ajax获得详情数据

// 商品相关的
// Route::any('goods/list', 'WX\GoodsController@list');// 根据店铺id，分类id获取店铺的商品信息--有分页
Route::any('shopGoods/ajax_alist', 'WX\ShopGoodsController@ajax_alist');//ajax获得列表数据
// Route::any('shopGoods/ajax_info/{id}', 'WX\ShopGoodsController@ajax_info');//ajax获得详情数据

// 商品分类相关的
Route::any('shopGoodsType/ajax_alist', 'WX\ShopGoodsTypeController@ajax_alist');//ajax获得列表数据

// 购物车相关的
Route::any('cart/ajax_save', 'WX\CartController@ajax_save');// 添加单个商品到购物车，已有的，数量+n
// Route::any('cart/addGoodCount', 'WX\CartController@addGoodCount');// 修改商品数量
Route::any('cart/ajax_initCart', 'WX\CartController@ajax_initCart');// 根据城市id,获得购物车数据
Route::any('cart/ajax_getStartPrice', 'WX\CartController@ajax_getStartPrice');// 根据城市id,获得购物车的配送费用数据
// Route::any('cart/ajax_alist', 'WX\CartController@ajax_alist');//获得当前用户所有的购物车商品，按商户分组
// Route::any('cart/ajax_del', 'WX\CartController@ajax_del');//  移除商品
Route::any('cart/ajax_del_shop', 'WX\CartController@ajax_del_shop');//  移除商品--通过店铺id
// Route::any('cart/empty', 'WX\CartController@empty');// 清空用户的购物车
Route::any('cart/ajax_prop', 'WX\CartController@ajax_prop');//  购物车商品属性操作 good_prop_table_id  多个用逗号分隔, 0 ：代表一个都没有选
Route::any('cart/ajax_createOrder', 'WX\CartController@ajax_createOrder');// 生成订单
// 收货地址
//Route::any('address/add', 'WX\AddressController@add');// 添加 收货地址
//Route::any('address/list', 'WX\AddressController@list');// 列表 收货地址--有分页
//Route::any('address/modify', 'WX\AddressController@modify');// 修改 收货地址
//Route::any('address/del', 'WX\AddressController@del');// 删除 收货地址

// 搜索标签
Route::any('commonAddr/ajax_alist', 'WX\CommonAddrController@ajax_alist');//ajax获得列表数据
Route::any('commonAddr/ajax_info/{id}', 'WX\CommonAddrController@ajax_info');//ajax获得详情数据
Route::any('commonAddr/ajax_firstInfo', 'WX\CommonAddrController@ajax_firstInfo');//ajax获得详情数据--默认地址或最新的第一条[没有设置默认]
Route::post('commonAddr/ajax_del', 'WX\CommonAddrController@ajax_del');// 删除
Route::post('commonAddr/ajax_save', 'WX\CommonAddrController@ajax_save');// 新加/修改

// 订单相关的

Route::any('order/getInfoByOrderNoDoing', 'WX\OrderController@getInfoByOrderNoDoing');// 订单详情根据订单编号查询订单

//Route::any('order/create', 'WX\OrderController@create');// 生成订单
Route::any('order/cancel', 'WX\OrderController@cancel');// 订单作废
Route::any('order/chState', 'WX\OrderController@chState');// 更新订单状态
Route::any('order/getList', 'WX\OrderController@getList');// 订单--列表--有分页
Route::any('order/ajax_alist', 'WX\OrderController@ajax_alist');//ajax获得列表数据--列表--有分页
Route::any('order/ajax_getCountByStatus', 'WX\OrderController@ajax_getCountByStatus');//ajax获得统计数据
Route::any('order/grabOrder', 'WX\OrderController@grabOrder');// 抢单
Route::any('order/finishOrder', 'WX\OrderController@finishOrder');// 订单完成
Route::any('order/delOrder', 'WX\OrderController@delOrder');// 订单删除
Route::any('order/eachDoOrder', 'WX\OrderController@eachDoOrder');// 每30秒或1分钟去执行一次的方法,获得这段时间内的待接订单
Route::any('order/getCountList', 'WX\OrderController@getCountList');// 统计抢单/订单

// 订单支付相关的
Route::any('orderPay/pay', 'WX\OrderPayController@pay');// 订单付款
Route::any('orderPay/refund', 'WX\OrderPayController@refund');// 订单退款
Route::any('orderPay/bond', 'WX\OrderPayController@bond');// 支付保证金
Route::any('orderPay/recharge', 'WX\OrderPayController@recharge');// 充值

// 微信支付相关的
Route::any('pay/unifiedorderByNo', 'WX\PayController@unifiedorderByNo');// 统一下单--支付

Route::any('pay/unifiedorder', 'WX\PayController@unifiedorder');// 统一下单
Route::any('pay/wechatNotify', 'WX\PayController@wechatNotify');// 支付结果通知--回调
Route::any('pay/refundOrder', 'WX\PayController@refundOrder');// 退单
Route::any('pay/refundNotify', 'WX\PayController@refundNotify');// 退款结果通知--回调
Route::any('pay/sweepCodePayNotify', 'WX\PayController@sweepCodePayNotify');// 扫码支付通知

Route::any('pay/operateRefundByNo', 'WX\PayController@operateRefundByNo');// 退款--手动查询退单结果并操作记录

Route::any('pay/test', 'WX\PayController@test');// 统一下单


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/*
Route::post('file/upload', function(\Illuminate\Http\Request $request) {
    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
        $photo = $request->file('photo');
        $extension = $photo->extension();
        //$store_result = $photo->store('photo');
        $store_result = $photo->storeAs('photo', 'test.jpg');
        $output = [
            'extension' => $extension,
            'store_result' => $store_result
        ];
        print_r($output);exit();
    }
    exit('未获取到上传文件或上传过程出错');
});
*/