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
Route::post('admin/shopType/ajax_alist', 'Admin\ShopTypeController@ajax_alist');//ajax获得列表数据
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

Route::post('admin/prop/import', 'Admin\PropController@import');// 导入excel
Route::post('admin/prop/ajax_get_ids', 'Admin\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('admin/prop/ajax_selected', 'Admin\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('admin/prop/ajax_selected_multi', 'Admin\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('admin/shop/ajax_alist', 'Admin\ShopController@ajax_alist');//ajax获得列表数据
Route::post('admin/shop/ajax_del', 'Admin\ShopController@ajax_del');// 删除
Route::any('admin/shop/ajax_save', 'Admin\ShopController@ajax_save');// 新加/修改
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

//站点介绍
Route::post('admin/siteIntro/ajax_alist', 'Admin\SiteIntroController@ajax_alist');//ajax获得列表数据
Route::post('admin/siteIntro/ajax_del', 'Admin\SiteIntroController@ajax_del');// 删除
Route::post('admin/siteIntro/ajax_save', 'Admin\SiteIntroController@ajax_save');// 新加/修改
Route::post('admin/siteIntro/ajax_get_child', 'Admin\SiteIntroController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/siteIntro/ajax_get_areachild', 'Admin\SiteIntroController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/siteIntro/ajax_import_staff','Admin\SiteIntroController@ajax_import'); // 导入员工

Route::post('admin/siteIntro/import', 'Admin\SiteIntroController@import');// 导入excel
Route::post('admin/siteIntro/ajax_get_ids', 'Admin\SiteIntroController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

//标签[一级分类]
Route::post('admin/labels/ajax_alist', 'Admin\LabelsController@ajax_alist');//ajax获得列表数据
Route::post('admin/labels/ajax_del', 'Admin\LabelsController@ajax_del');// 删除
Route::post('admin/labels/ajax_save', 'Admin\LabelsController@ajax_save');// 新加/修改
Route::post('admin/labels/ajax_get_child', 'Admin\LabelsController@ajax_get_child');// 根据部门id,小组id获得子类员工数组[kv一维数组]
Route::post('admin/labels/ajax_get_areachild', 'Admin\LabelsController@ajax_get_areachild');// 根据区县id,街道id获得子类员工数组[kv一维数组]
Route::post('admin/labels/ajax_import_staff','Admin\LabelsController@ajax_import'); // 导入员工

Route::post('admin/labels/import', 'Admin\LabelsController@import');// 导入excel
Route::post('admin/labels/ajax_get_ids', 'Admin\LabelsController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

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

Route::post('city/prop/import', 'City\PropController@import');// 导入excel
Route::post('city/prop/ajax_get_ids', 'City\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('city/prop/ajax_selected', 'City\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('city/prop/ajax_selected_multi', 'City\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('city/shop/ajax_alist', 'City\ShopController@ajax_alist');//ajax获得列表数据
Route::post('city/shop/ajax_del', 'City\ShopController@ajax_del');// 删除
Route::any('city/shop/ajax_save', 'City\ShopController@ajax_save');// 新加/修改
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

Route::post('seller/prop/import', 'Seller\PropController@import');// 导入excel
Route::post('seller/prop/ajax_get_ids', 'Seller\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('seller/prop/ajax_selected', 'Seller\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('seller/prop/ajax_selected_multi', 'Seller\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('seller/shop/ajax_alist', 'Seller\ShopController@ajax_alist');//ajax获得列表数据
Route::post('seller/shop/ajax_del', 'Seller\ShopController@ajax_del');// 删除
Route::any('seller/shop/ajax_save', 'Seller\ShopController@ajax_save');// 新加/修改
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

Route::post('shop/prop/import', 'Shop\PropController@import');// 导入excel
Route::post('shop/prop/ajax_get_ids', 'Shop\PropController@ajax_get_ids');// 获得查询所有记录的id字符串，多个逗号分隔

Route::any('shop/prop/ajax_selected', 'Shop\PropController@ajax_selected');//ajax选择中记录/更新记录 -单选
Route::any('shop/prop/ajax_selected_multi', 'Shop\PropController@ajax_selected_multi');//ajax选择中记录/更新记录 -多选
//店铺
Route::any('shop/shop/ajax_alist', 'Shop\ShopController@ajax_alist');//ajax获得列表数据
Route::post('shop/shop/ajax_del', 'Shop\ShopController@ajax_del');// 删除
Route::any('shop/shop/ajax_save', 'Shop\ShopController@ajax_save');// 新加/修改
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