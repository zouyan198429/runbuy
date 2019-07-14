<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});


// Route::get('/test', 'IndexController@test');// 测试
//Route::get('/test2', 'IndexController@test2');// 测试
Route::get('/', 'IndexController@index');// 首页
//Route::get('reg', 'IndexController@reg');// 注册
//Route::get('login', 'IndexController@login');// 登陆
//Route::get('logout', 'IndexController@logout');// 注销
//Route::get('404', 'IndexController@err404');// 404错误

// layuiAdmin
Route::get('layui/index', 'Layui\IndexController@index');// index.html
Route::get('layui/iframe/layer/iframe', 'Layui\Iframe\LayerController@iframe');// iframe/layer/iframe.html layer iframe 示例
Route::get('layui/system/about', 'Layui\SystemController@about');// system/about.html 版本信息 --***
Route::get('layui/system/get', 'Layui\SystemController@get');// system/get.html 授权获得 layuiAdmin --***
Route::get('layui/system/more', 'Layui\SystemController@more');// system/more.html 更多面板的模板 --***
Route::get('layui/system/theme', 'Layui\SystemController@theme');// system/theme.html 主题设置模板 --***
// 主页
Route::get('layui/home/console', 'Layui\HomeController@console');// 控制台 home/console.html
Route::get('layui/home/homepage1', 'Layui\HomeController@homepage1');// 主页一 home/homepage1.html
Route::get('layui/home/homepage2', 'Layui\HomeController@homepage2');// 主页二 home/homepage2.html
// 组件
Route::get('layui/component/laytpl/index', 'Layui\Component\LaytplController@index');// component/laytpl/index.html  模板引擎  --***
// 栅格
Route::get('layui/component/grid/list', 'Layui\Component\GridController@list');// 等比例列表排列 component/grid/list.html
Route::get('layui/component/grid/mobile', 'Layui\Component\GridController@mobile');// 按移动端排列 component/grid/mobile.html
Route::get('layui/component/grid/mobile-pc', 'Layui\Component\GridController@mobilePc');// 移动桌面端组合 component/grid/mobile-pc.html
Route::get('layui/component/grid/all', 'Layui\Component\GridController@all');// 全端复杂组合 component/grid/all.html
Route::get('layui/component/grid/stack', 'Layui\Component\GridController@stack');// 低于桌面堆叠排列 component/grid/stack.html
Route::get('layui/component/grid/speed-dial', 'Layui\Component\GridController@speedDial');// 九宫格 component/grid/speed-dial.html

Route::get('layui/component/button/index', 'Layui\Component\ButtonController@index');// 按钮  component/button/index.html
// 表单
Route::get('layui/component/form/element', 'Layui\Component\FormController@element');// 表单元素 component/form/element.html
Route::get('layui/component/form/group', 'Layui\Component\FormController@group');// 表单组合 component/form/group.html

Route::get('layui/component/nav/index', 'Layui\Component\NavController@index');// 导航  component/nav/index.html
Route::get('layui/component/tabs/index', 'Layui\Component\TabsController@index');// 选项卡 component/tabs/index.html
Route::get('layui/component/progress/index', 'Layui\Component\ProgressController@index');// 进度条 component/progress/index.html
Route::get('layui/component/panel/index', 'Layui\Component\PanelController@index');// 面板 component/panel/index.html
Route::get('layui/component/badge/index', 'Layui\Component\BadgeController@index');// 徽章 component/badge/index.html
Route::get('layui/component/timeline/index', 'Layui\Component\TimelineController@index');// 时间线 component/timeline/index.html
Route::get('layui/component/anim/index', 'Layui\Component\AnimController@index');// 动画 component/anim/index.html
Route::get('layui/component/auxiliar/index', 'Layui\Component\AuxiliarController@index');// 辅助 component/auxiliar/index.html
// 通用弹层
Route::get('layui/component/layer/list', 'Layui\Component\LayerController@list');// 功能演示 component/layer/list.html
Route::get('layui/component/layer/special-demo', 'Layui\Component\LayerController@specialDemo');// 特殊示例 component/layer/special-demo.html
Route::get('layui/component/layer/theme', 'Layui\Component\LayerController@theme');// 风格定制 component/layer/theme.html
// 日期时间
Route::get('layui/component/laydate/index', 'Layui\Component\LaydateController@index');// component/laydate/index.html  日期组件 --***
Route::get('layui/component/laydate/demo1', 'Layui\Component\LaydateController@demo1');// 功能演示一 component/laydate/demo1.html
Route::get('layui/component/laydate/demo2', 'Layui\Component\LaydateController@demo2');// 功能演示二 component/laydate/demo2.html
Route::get('layui/component/laydate/theme', 'Layui\Component\LaydateController@theme');// 设定主题 component/laydate/theme.html
Route::get('layui/component/laydate/special-demo', 'Layui\Component\LaydateController@specialDemo');// 特殊示例 component/laydate/special-demo.html

Route::get('layui/component/table/static', 'Layui\Component\TableController@static');// 静态表格 component/table/static.html
// 数据表格
Route::get('layui/component/table/index', 'Layui\Component\TableController@index');// component/table/index.html  表格 --***
Route::get('layui/component/temp', 'Layui\Component\TableController@temp');// component/temp.html  简单用法 - 数据表格 --***
Route::get('layui/component/table/simple', 'Layui\Component\TableController@simple');// 简单数据表格 component/table/simple.html
Route::get('layui/component/table/auto', 'Layui\Component\TableController@auto');// 列宽自动分配 component/table/auto.html
Route::get('layui/component/table/data', 'Layui\Component\TableController@data');// 赋值已知数据 component/table/data.html
Route::get('layui/component/table/tostatic', 'Layui\Component\TableController@tostatic');// 转化静态表格 component/table/tostatic.html
Route::get('layui/component/table/page', 'Layui\Component\TableController@page');// 开启分页 component/table/page.html
Route::get('layui/component/table/resetPage', 'Layui\Component\TableController@resetPage');// 自定义分页 component/table/resetPage.html
Route::get('layui/component/table/toolbar', 'Layui\Component\TableController@toolbar');// 开启头部工具栏 component/table/toolbar.html
Route::get('layui/component/table/totalRow', 'Layui\Component\TableController@totalRow');// 开启合计行 component/table/totalRow.html
Route::get('layui/component/table/height', 'Layui\Component\TableController@height');// 高度最大适应 component/table/height.html
Route::get('layui/component/table/checkbox', 'Layui\Component\TableController@checkbox');// 开启复选框 component/table/checkbox.html
Route::get('layui/component/table/radio', 'Layui\Component\TableController@radio');// 开启单选框 component/table/radio.html
Route::get('layui/component/table/cellEdit', 'Layui\Component\TableController@cellEdit');// 开启单元格编辑 component/table/cellEdit.html
Route::get('layui/component/table/form', 'Layui\Component\TableController@form');// 加入表单元素 component/table/form.html
Route::get('layui/component/table/style', 'Layui\Component\TableController@style');// 设置单元格样式 component/table/style.html
Route::get('layui/component/table/fixed', 'Layui\Component\TableController@fixed');// 固定列 component/table/fixed.html
Route::get('layui/component/table/operate', 'Layui\Component\TableController@operate');// 数据操作 component/table/operate.html
Route::get('layui/component/table/parseData', 'Layui\Component\TableController@parseData');// 解析任意数据格式 component/table/parseData.html
Route::get('layui/component/table/onrow', 'Layui\Component\TableController@onrow');// 监听行事件 component/table/onrow.html
Route::get('layui/component/table/reload', 'Layui\Component\TableController@reload');// 数据表格的重载 component/table/reload.html
Route::get('layui/component/table/initSort', 'Layui\Component\TableController@initSort');// 设置初始排序 component/table/initSort.html
Route::get('layui/component/table/cellEvent', 'Layui\Component\TableController@cellEvent');// 监听单元格事件 component/table/cellEvent.html
Route::get('layui/component/table/thead', 'Layui\Component\TableController@thead');// 复杂表头 component/table/thead.html
// 分页
Route::get('layui/component/laypage/index', 'Layui\Component\LaypageController@index');// component/laypage/index.html  通用分页组件 --***
Route::get('layui/component/laypage/demo1', 'Layui\Component\LaypageController@demo1');// 功能演示一 component/laypage/demo1.html
Route::get('layui/component/laypage/demo2', 'Layui\Component\LaypageController@demo2');// 功能演示二 component/laypage/demo2.html
// 上传
Route::get('layui/component/upload/index', 'Layui\Component\UploadController@index');// component/upload/index.html 上传 --***
Route::get('layui/component/upload/demo1', 'Layui\Component\UploadController@demo1');// 功能演示一 component/upload/demo1.html
Route::get('layui/component/upload/demo2', 'Layui\Component\UploadController@demo2');// 功能演示二 component/upload/demo2.html

Route::get('layui/component/colorpicker/index', 'Layui\Component\ColorpickerController@index');// 颜色选择器 component/colorpicker/index.html
Route::get('layui/component/slider/index', 'Layui\Component\SliderController@index');// 滑块组件 component/slider/index.html
Route::get('layui/component/rate/index', 'Layui\Component\RateController@index');// 评分 component/rate/index.html
Route::get('layui/component/carousel/index', 'Layui\Component\CarouselController@index');// 轮播 component/carousel/index.html
Route::get('layui/component/flow/index', 'Layui\Component\FlowController@index');// 流加载 component/flow/index.html
Route::get('layui/component/util/index', 'Layui\Component\UtilController@index');// 工具 component/util/index.html
Route::get('layui/component/code/index', 'Layui\Component\CodeController@index');// 代码修饰 component/code/index.html

// 页面
Route::get('layui/template/personalpage', 'Layui\TemplateController@personalpage');// 个人主页 template/personalpage.html
Route::get('layui/template/addresslist', 'Layui\TemplateController@addresslist');// 通讯录 template/addresslist.html
Route::get('layui/template/caller', 'Layui\TemplateController@caller');// 客户列表 template/caller.html
Route::get('layui/template/goodslist', 'Layui\TemplateController@goodslist');// 商品列表 template/goodslist.html
Route::get('layui/template/msgboard', 'Layui\TemplateController@msgboard');// 留言板 template/msgboard.html
Route::get('layui/template/search', 'Layui\TemplateController@search');// 搜索结果 template/search.html
Route::get('layui/template/temp', 'Layui\TemplateController@temp');// template/temp.html --***


Route::get('layui/user/reg', 'Layui\UserController@reg');// 注册 user/reg.html
Route::get('layui/user/login', 'Layui\UserController@login');// 登入 user/login.html
Route::get('layui/user/forget', 'Layui\UserController@forget');// 忘记密码 user/forget.html

Route::get('layui/template/tips/404', 'Layui\Template\TipsController@err404');// 404页面不存在 template/tips/404.html
Route::get('layui/template/tips/error', 'Layui\Template\TipsController@error');// 错误提示 template/tips/error.html
// 百度一下 //www.baidu.com/
// layui官网 //www.layui.com/
// layuiAdmin官网 //www.layui.com/admin/
// 应用
//    内容系统
Route::get('layui/app/content/list', 'Layui\App\ContentController@list');// 文章列表 app/content/list.html
Route::get('layui/app/content/tags', 'Layui\App\ContentController@tags');// 分类管理 app/content/tags.html
Route::get('layui/app/content/comment', 'Layui\App\ContentController@comment');// 评论管理 app/content/comment.html
Route::get('layui/app/content/contform', 'Layui\App\ContentController@contform');// app/content/contform.html  评论管理 iframe 框 --***
Route::get('layui/app/content/listform', 'Layui\App\ContentController@listform');// app/content/listform.html  文章管理 iframe 框 --***
Route::get('layui/app/content/tagsform', 'Layui\App\ContentController@tagsform');// app/content/tagsform.html  分类管理 iframe 框
//    社区系统
Route::get('layui/app/forum/list', 'Layui\App\ForumController@list');// 帖子列表 app/forum/list.html
Route::get('layui/app/forum/replys', 'Layui\App\ForumController@replys');// 回帖列表 app/forum/replys.html
Route::get('layui/app/forum/listform', 'Layui\App\ForumController@listform');// app/forum/listform.html  帖子管理 iframe 框 --***
Route::get('layui/app/forum/replysform', 'Layui\App\ForumController@replysform');// app/forum/replysform.html  回帖管理 iframe 框 --***

Route::get('layui/app/message/index', 'Layui\App\MessageController@index');// 消息中心 app/message/index.html
Route::get('layui/app/message/detail', 'Layui\App\MessageController@detail');// app/message/detail.html  消息详情标题 --***

Route::get('layui/app/workorder/list', 'Layui\App\WorkorderController@list');// 工单系统 app/workorder/list.html
Route::get('layui/app/workorder/listform', 'Layui\App\WorkorderController@listform');// app/workorder/listform.html 工单管理 iframe 框

Route::get('layui/app/mall/category', 'Layui\App\MallController@category');// app/mall/category.html  分类管理 --***
Route::get('layui/app/mall/list', 'Layui\App\MallController@list');// app/mall/list.html  商品列表 --***
Route::get('layui/app/mall/specs', 'Layui\App\MallController@specs');// app/mall/specs.html  规格管理 --***
//  高级
//    LayIM 通讯系统
Route::get('layui/senior/im/index', 'Layui\Senior\ImController@index');// senior/im/index.html  LayIM 社交聊天 --***
//    Echarts集成
Route::get('layui/senior/echarts/line', 'Layui\Senior\EchartsController@line');// 折线图 senior/echarts/line.html
Route::get('layui/senior/echarts/bar', 'Layui\Senior\EchartsController@bar');// 柱状图 senior/echarts/bar.html
Route::get('layui/senior/echarts/map', 'Layui\Senior\EchartsController@map');// 地图  senior/echarts/map.html
// 用户
Route::get('layui/user/user/list', 'Layui\User\UserController@list');// 网站用户 user/user/list.html
Route::get('layui/user/user/userform', 'Layui\User\UserController@userform');// user/user/userform.html  网站用户 iframe 框

Route::get('layui/user/administrators/list', 'Layui\User\AdministratorsController@list');// 后台管理员 user/administrators/list.html
Route::get('layui/user/administrators/role', 'Layui\User\AdministratorsController@role');// 角色管理 user/administrators/role.html
Route::get('layui/user/administrators/adminform', 'Layui\User\AdministratorsController@adminform');// user/administrators/adminform.html 管理员 iframe 框
Route::get('layui/user/administrators/roleform', 'Layui\User\AdministratorsController@roleform');// user/administrators/roleform.html 角色管理 iframe 框

// 设置
//    系统设置
Route::get('layui/set/system/website', 'Layui\Set\SystemController@website');// 网站设置 set/system/website.html
Route::get('layui/set/system/email', 'Layui\Set\SystemController@email');// 邮件服务 set/system/email.html
//    我的设置
Route::get('layui/set/user/info', 'Layui\Set\UserController@info');// 基本资料 set/user/info.html
Route::get('layui/set/user/password', 'Layui\Set\UserController@password');// 修改密码 set/user/password.html
// 授权  //www.layui.com/admin/#get

// ----大后台
// Admin
Route::get('admin/index', 'Admin\IndexController@index');// 首页
Route::get('admin', 'Admin\IndexController@index');
Route::get('admin/login', 'Admin\IndexController@login');//login.html 登录
Route::get('admin/logout', 'Admin\IndexController@logout');// 注销
Route::get('admin/password', 'Admin\IndexController@password');//psdmodify.html 个人信息-修改密码
Route::get('admin/info', 'Admin\IndexController@info');//myinfo.html 个人信息--显示
// 腾讯地图
Route::get('admin/qqMaps/latLngSelect', 'Admin\QQMapsController@latLngSelect');// 经纬度选择


// 后台--管理员
Route::get('admin/staff', 'Admin\StaffController@index');// 列表
Route::get('admin/staff/add/{id}', 'Admin\StaffController@add');// 添加
// Route::get('admin/staff/select', 'Admin\StaffController@select');// 选择-弹窗
Route::get('admin/staff/export', 'Admin\StaffController@export');// 导出
Route::get('admin/staff/import_template', 'Admin\StaffController@import_template');// 导入模版

// 加盟商--管理员
Route::get('admin/staffPartner', 'Admin\StaffPartnerController@index');// 列表
Route::get('admin/staffPartner/add/{id}', 'Admin\StaffPartnerController@add');// 添加
// Route::get('admin/staffPartner/select', 'Admin\StaffPartnerController@select');// 选择-弹窗
Route::get('admin/staffPartner/export', 'Admin\StaffPartnerController@export');// 导出
Route::get('admin/staffPartner/import_template', 'Admin\StaffPartnerController@import_template');// 导入模版

// 商家--管理员
Route::get('admin/staffSeller', 'Admin\StaffSellerController@index');// 列表
Route::get('admin/staffSeller/add/{id}', 'Admin\StaffSellerController@add');// 添加
// Route::get('admin/staffSeller/select', 'Admin\StaffSellerController@select');// 选择-弹窗
Route::get('admin/staffSeller/export', 'Admin\StaffSellerController@export');// 导出
Route::get('admin/staffSeller/import_template', 'Admin\StaffSellerController@import_template');// 导入模版

// 店铺--管理员
Route::get('admin/staffShop', 'Admin\StaffShopController@index');// 列表
Route::get('admin/staffShop/add/{id}', 'Admin\StaffShopController@add');// 添加
// Route::get('admin/staffShop/select', 'Admin\StaffShopController@select');// 选择-弹窗
Route::get('admin/staffShop/export', 'Admin\StaffShopController@export');// 导出
Route::get('admin/staffShop/import_template', 'Admin\StaffShopController@import_template');// 导入模版

// 跑腿--管理员
Route::get('admin/staffRun', 'Admin\StaffRunController@index');// 列表
Route::get('admin/staffRun/add/{id}', 'Admin\StaffRunController@add');// 添加
// Route::get('admin/staffRun/select', 'Admin\StaffRunController@select');// 选择-弹窗
Route::get('admin/staffRun/export', 'Admin\StaffRunController@export');// 导出
Route::get('admin/staffRun/import_template', 'Admin\StaffRunController@import_template');// 导入模版

// 用户--管理员
Route::get('admin/staffUser', 'Admin\StaffUserController@index');// 列表
Route::get('admin/staffUser/add/{id}', 'Admin\StaffUserController@add');// 添加
// Route::get('admin/staffUser/select', 'Admin\StaffUserController@select');// 选择-弹窗
Route::get('admin/staffUser/export', 'Admin\StaffUserController@export');// 导出
Route::get('admin/staffUser/import_template', 'Admin\StaffUserController@import_template');// 导入模版

// 城市
Route::get('admin/city', 'Admin\CityController@index');// 列表
Route::get('admin/city/add/{id}', 'Admin\CityController@add');// 添加
Route::get('admin/city/select', 'Admin\CityController@select');// 选择-弹窗
Route::get('admin/city/export', 'Admin\CityController@export');// 导出
Route::get('admin/city/import_template', 'Admin\CityController@import_template');// 导入模版

// 代理
Route::get('admin/cityPartner', 'Admin\CityPartnerController@index');// 列表
Route::get('admin/cityPartner/add/{id}', 'Admin\CityPartnerController@add');// 添加
Route::get('admin/cityPartner/select', 'Admin\CityPartnerController@select');// 选择-弹窗
Route::get('admin/cityPartner/export', 'Admin\CityPartnerController@export');// 导出
Route::get('admin/cityPartner/import_template', 'Admin\CityPartnerController@import_template');// 导入模版

// 商家
Route::get('admin/seller', 'Admin\SellerController@index');// 列表
Route::get('admin/seller/add/{id}', 'Admin\SellerController@add');// 添加
Route::get('admin/seller/select', 'Admin\SellerController@select');// 选择-弹窗
Route::get('admin/seller/export', 'Admin\SellerController@export');// 导出
Route::get('admin/seller/import_template', 'Admin\SellerController@import_template');// 导入模版

// 店铺分类
Route::get('admin/shopType', 'Admin\ShopTypeController@index');// 列表
Route::get('admin/shopType/add/{id}', 'Admin\ShopTypeController@add');// 添加
// Route::get('admin/shopType/select', 'Admin\ShopTypeController@select');// 选择-弹窗
Route::get('admin/shopType/export', 'Admin\ShopTypeController@export');// 导出
Route::get('admin/shopType/import_template', 'Admin\ShopTypeController@import_template');// 导入模版

// 店铺商品属性
Route::get('admin/prop', 'Admin\PropController@index');// 列表
Route::get('admin/prop/add/{id}', 'Admin\PropController@add');// 添加
Route::get('admin/prop/select', 'Admin\PropController@select');// 选择-弹窗
Route::get('admin/prop/export', 'Admin\PropController@export');// 导出
Route::get('admin/prop/import_template', 'Admin\PropController@import_template');// 导入模版

// 店铺
Route::get('admin/shop', 'Admin\ShopController@index');// 列表
Route::get('admin/shop/add/{id}', 'Admin\ShopController@add');// 添加
Route::get('admin/shop/select', 'Admin\ShopController@select');// 选择-弹窗
Route::get('admin/shop/export', 'Admin\ShopController@export');// 导出
Route::get('admin/shop/import_template', 'Admin\ShopController@import_template');// 导入模版

// 商品
Route::get('admin/shopGoods', 'Admin\ShopGoodsController@index');// 列表
Route::get('admin/shopGoods/add/{id}', 'Admin\ShopGoodsController@add');// 添加
Route::get('admin/shopGoods/select', 'Admin\ShopGoodsController@select');// 选择-弹窗
Route::get('admin/shopGoods/export', 'Admin\ShopGoodsController@export');// 导出
Route::get('admin/shopGoods/import_template', 'Admin\ShopGoodsController@import_template');// 导入模版

// 店铺商品分类[一级分类]
Route::get('admin/shopGoodsType', 'Admin\ShopGoodsTypeController@index');// 列表
Route::get('admin/shopGoodsType/add/{id}', 'Admin\ShopGoodsTypeController@add');// 添加
// Route::get('admin/shopGoodsType/select', 'Admin\ShopGoodsTypeController@select');// 选择-弹窗
Route::get('admin/shopGoodsType/export', 'Admin\ShopGoodsTypeController@export');// 导出
Route::get('admin/shopGoodsType/import_template', 'Admin\ShopGoodsTypeController@import_template');// 导入模版


// 店铺营业时间
Route::get('admin/shopOpenTime', 'Admin\ShopOpenTimeController@index');// 列表
Route::get('admin/shopOpenTime/add/{id}', 'Admin\ShopOpenTimeController@add');// 添加
// Route::get('admin/shopOpenTime/select', 'Admin\ShopOpenTimeController@select');// 选择-弹窗
Route::get('admin/shopOpenTime/export', 'Admin\ShopOpenTimeController@export');// 导出
Route::get('admin/shopOpenTime/import_template', 'Admin\ShopOpenTimeController@import_template');// 导入模版


// 站点介绍
Route::get('admin/siteIntro', 'Admin\SiteIntroController@index');// 列表
Route::get('admin/siteIntro/add/{id}', 'Admin\SiteIntroController@add');// 添加
// Route::get('admin/siteIntro/select', 'Admin\SiteIntroController@select');// 选择-弹窗
Route::get('admin/siteIntro/export', 'Admin\SiteIntroController@export');// 导出
Route::get('admin/siteIntro/import_template', 'Admin\SiteIntroController@import_template');// 导入模版


// 站点介绍-跑腿人员
Route::get('admin/siteIntroRuner', 'Admin\SiteIntroRunerController@index');// 列表
Route::get('admin/siteIntroRuner/add/{id}', 'Admin\SiteIntroRunerController@add');// 添加
// Route::get('admin/siteIntroRuner/select', 'Admin\SiteIntroRunerController@select');// 选择-弹窗
Route::get('admin/siteIntroRuner/export', 'Admin\SiteIntroRunerController@export');// 导出
Route::get('admin/siteIntroRuner/import_template', 'Admin\SiteIntroRunerController@import_template');// 导入模版


// 标签[一级分类]
Route::get('admin/labels', 'Admin\LabelsController@index');// 列表
Route::get('admin/labels/add/{id}', 'Admin\LabelsController@add');// 添加
// Route::get('admin/labels/select', 'Admin\LabelsController@select');// 选择-弹窗
Route::get('admin/labels/export', 'Admin\LabelsController@export');// 导出
Route::get('admin/labels/import_template', 'Admin\LabelsController@import_template');// 导入模版

// 地址
Route::get('admin/commonAddr', 'Admin\CommonAddrController@index');// 列表
Route::get('admin/commonAddr/add/{id}', 'Admin\CommonAddrController@add');// 添加
// Route::get('admin/commonAddr/select', 'Admin\CommonAddrController@select');// 选择-弹窗
Route::get('admin/commonAddr/export', 'Admin\CommonAddrController@export');// 导出
Route::get('admin/commonAddr/import_template', 'Admin\CommonAddrController@import_template');// 导入模版


// 公告
Route::get('admin/notice', 'Admin\NoticeController@index');// 列表
Route::get('admin/notice/add/{id}', 'Admin\NoticeController@add');// 添加
// Route::get('admin/notice/select', 'Admin\NoticeController@select');// 选择-弹窗
Route::get('admin/notice/export', 'Admin\NoticeController@export');// 导出
Route::get('admin/notice/import_template', 'Admin\NoticeController@import_template');// 导入模版

// 收费标准
Route::get('admin/feeScale', 'Admin\FeeScaleController@index');// 列表
Route::get('admin/feeScale/add/{id}', 'Admin\FeeScaleController@add');// 添加
// Route::get('admin/feeScale/select', 'Admin\FeeScaleController@select');// 选择-弹窗
Route::get('admin/feeScale/export', 'Admin\FeeScaleController@export');// 导出
Route::get('admin/feeScale/import_template', 'Admin\FeeScaleController@import_template');// 导入模版

// 收费标准-时间段
Route::get('admin/feeScaleTime', 'Admin\FeeScaleTimeController@index');// 列表
Route::get('admin/feeScaleTime/add/{id}', 'Admin\FeeScaleTimeController@add');// 添加
Route::get('admin/feeScaleTime/addBath/{city_site_id}', 'Admin\FeeScaleTimeController@addBath');// 添加--按城市批量
// Route::get('admin/feeScaleTime/select', 'Admin\FeeScaleTimeController@select');// 选择-弹窗
Route::get('admin/feeScaleTime/export', 'Admin\FeeScaleTimeController@export');// 导出
Route::get('admin/feeScaleTime/import_template', 'Admin\FeeScaleTimeController@import_template');// 导入模版

// 订单
Route::get('admin/order', 'Admin\OrdersController@index');// 列表
Route::get('admin/order/add/{id}', 'Admin\OrdersController@add');// 添加
// Route::get('admin/order/select', 'Admin\OrdersController@select');// 选择-弹窗
Route::get('admin/order/export', 'Admin\OrdersController@export');// 导出
Route::get('admin/order/import_template', 'Admin\OrdersController@import_template');// 导入模版
Route::get('admin/order/countOrders', 'Admin\OrdersController@countOrders');// 统计-订单数量

// ----城市代理商
// City
Route::get('city/index', 'City\IndexController@index');// 首页
Route::get('city', 'City\IndexController@index');
Route::get('city/login', 'City\IndexController@login');//login.html 登录
Route::get('city/logout', 'City\IndexController@logout');// 注销
Route::get('city/password', 'City\IndexController@password');//psdmodify.html 个人信息-修改密码
Route::get('city/info', 'City\IndexController@info');//myinfo.html 个人信息--显示
// 腾讯地图
Route::get('city/qqMaps/latLngSelect', 'City\QQMapsController@latLngSelect');// 经纬度选择

// 后台--管理员
//Route::get('city/staff', 'City\StaffController@index');// 列表
//Route::get('city/staff/add/{id}', 'City\StaffController@add');// 添加
//// Route::get('city/staff/select', 'City\StaffController@select');// 选择-弹窗
//Route::get('city/staff/export', 'City\StaffController@export');// 导出
//Route::get('city/staff/import_template', 'City\StaffController@import_template');// 导入模版

// 加盟商--管理员
Route::get('city/staffPartner', 'City\StaffPartnerController@index');// 列表
Route::get('city/staffPartner/add/{id}', 'City\StaffPartnerController@add');// 添加
// Route::get('city/staffPartner/select', 'City\StaffPartnerController@select');// 选择-弹窗
Route::get('city/staffPartner/export', 'City\StaffPartnerController@export');// 导出
Route::get('city/staffPartner/import_template', 'City\StaffPartnerController@import_template');// 导入模版

// 商家--管理员
Route::get('city/staffSeller', 'City\StaffSellerController@index');// 列表
Route::get('city/staffSeller/add/{id}', 'City\StaffSellerController@add');// 添加
// Route::get('city/staffSeller/select', 'City\StaffSellerController@select');// 选择-弹窗
Route::get('city/staffSeller/export', 'City\StaffSellerController@export');// 导出
Route::get('city/staffSeller/import_template', 'City\StaffSellerController@import_template');// 导入模版

// 店铺--管理员
Route::get('city/staffShop', 'City\StaffShopController@index');// 列表
Route::get('city/staffShop/add/{id}', 'City\StaffShopController@add');// 添加
// Route::get('city/staffShop/select', 'City\StaffShopController@select');// 选择-弹窗
Route::get('city/staffShop/export', 'City\StaffShopController@export');// 导出
Route::get('city/staffShop/import_template', 'City\StaffShopController@import_template');// 导入模版

// 跑腿--管理员
Route::get('city/staffRun', 'City\StaffRunController@index');// 列表
Route::get('city/staffRun/add/{id}', 'City\StaffRunController@add');// 添加
// Route::get('city/staffRun/select', 'City\StaffRunController@select');// 选择-弹窗
Route::get('city/staffRun/export', 'City\StaffRunController@export');// 导出
Route::get('city/staffRun/import_template', 'City\StaffRunController@import_template');// 导入模版

// 用户--管理员
Route::get('city/staffUser', 'City\StaffUserController@index');// 列表
Route::get('city/staffUser/add/{id}', 'City\StaffUserController@add');// 添加
// Route::get('city/staffUser/select', 'City\StaffUserController@select');// 选择-弹窗
Route::get('city/staffUser/export', 'City\StaffUserController@export');// 导出
Route::get('city/staffUser/import_template', 'City\StaffUserController@import_template');// 导入模版

// 城市
//Route::get('city/city', 'City\CityController@index');// 列表
//Route::get('city/city/add/{id}', 'City\CityController@add');// 添加
//Route::get('city/city/select', 'City\CityController@select');// 选择-弹窗
//Route::get('city/city/export', 'City\CityController@export');// 导出
//Route::get('city/city/import_template', 'City\CityController@import_template');// 导入模版

// 代理
//Route::get('city/cityPartner', 'City\CityPartnerController@index');// 列表
//Route::get('city/cityPartner/add/{id}', 'City\CityPartnerController@add');// 添加
//Route::get('city/cityPartner/select', 'City\CityPartnerController@select');// 选择-弹窗
//Route::get('city/cityPartner/export', 'City\CityPartnerController@export');// 导出
//Route::get('city/cityPartner/import_template', 'City\CityPartnerController@import_template');// 导入模版

// 商家
Route::get('city/seller', 'City\SellerController@index');// 列表
Route::get('city/seller/add/{id}', 'City\SellerController@add');// 添加
Route::get('city/seller/select', 'City\SellerController@select');// 选择-弹窗
Route::get('city/seller/export', 'City\SellerController@export');// 导出
Route::get('city/seller/import_template', 'City\SellerController@import_template');// 导入模版

// 店铺分类
//Route::get('city/shopType', 'City\ShopTypeController@index');// 列表
//Route::get('city/shopType/add/{id}', 'City\ShopTypeController@add');// 添加
//// Route::get('city/shopType/select', 'City\ShopTypeController@select');// 选择-弹窗
//Route::get('city/shopType/export', 'City\ShopTypeController@export');// 导出
//Route::get('city/shopType/import_template', 'City\ShopTypeController@import_template');// 导入模版

// 店铺商品属性
Route::get('city/prop', 'City\PropController@index');// 列表
Route::get('city/prop/add/{id}', 'City\PropController@add');// 添加
Route::get('city/prop/select', 'City\PropController@select');// 选择-弹窗
Route::get('city/prop/export', 'City\PropController@export');// 导出
Route::get('city/prop/import_template', 'City\PropController@import_template');// 导入模版

// 店铺
Route::get('city/shop', 'City\ShopController@index');// 列表
Route::get('city/shop/add/{id}', 'City\ShopController@add');// 添加
Route::get('city/shop/select', 'City\ShopController@select');// 选择-弹窗
Route::get('city/shop/export', 'City\ShopController@export');// 导出
Route::get('city/shop/import_template', 'City\ShopController@import_template');// 导入模版

// 商品
Route::get('city/shopGoods', 'City\ShopGoodsController@index');// 列表
Route::get('city/shopGoods/add/{id}', 'City\ShopGoodsController@add');// 添加
Route::get('city/shopGoods/select', 'City\ShopGoodsController@select');// 选择-弹窗
Route::get('city/shopGoods/export', 'City\ShopGoodsController@export');// 导出
Route::get('city/shopGoods/import_template', 'City\ShopGoodsController@import_template');// 导入模版

// 店铺商品分类[一级分类]
Route::get('city/shopGoodsType', 'City\ShopGoodsTypeController@index');// 列表
Route::get('city/shopGoodsType/add/{id}', 'City\ShopGoodsTypeController@add');// 添加
// Route::get('city/shopGoodsType/select', 'City\ShopGoodsTypeController@select');// 选择-弹窗
Route::get('city/shopGoodsType/export', 'City\ShopGoodsTypeController@export');// 导出
Route::get('city/shopGoodsType/import_template', 'City\ShopGoodsTypeController@import_template');// 导入模版


// 店铺营业时间
Route::get('city/shopOpenTime', 'City\ShopOpenTimeController@index');// 列表
Route::get('city/shopOpenTime/add/{id}', 'City\ShopOpenTimeController@add');// 添加
// Route::get('city/shopOpenTime/select', 'City\ShopOpenTimeController@select');// 选择-弹窗
Route::get('city/shopOpenTime/export', 'City\ShopOpenTimeController@export');// 导出
Route::get('city/shopOpenTime/import_template', 'City\ShopOpenTimeController@import_template');// 导入模版


// 站点介绍
//Route::get('city/siteIntro', 'City\SiteIntroController@index');// 列表
//Route::get('city/siteIntro/add/{id}', 'City\SiteIntroController@add');// 添加
//// Route::get('city/siteIntro/select', 'City\SiteIntroController@select');// 选择-弹窗
//Route::get('city/siteIntro/export', 'City\SiteIntroController@export');// 导出
//Route::get('city/siteIntro/import_template', 'City\SiteIntroController@import_template');// 导入模版

// 标签[一级分类]
//Route::get('city/labels', 'City\LabelsController@index');// 列表
//Route::get('city/labels/add/{id}', 'City\LabelsController@add');// 添加
//// Route::get('city/labels/select', 'City\LabelsController@select');// 选择-弹窗
//Route::get('city/labels/export', 'City\LabelsController@export');// 导出
//Route::get('city/labels/import_template', 'City\LabelsController@import_template');// 导入模版

// 公告
Route::get('city/notice', 'City\NoticeController@index');// 列表
Route::get('city/notice/add/{id}', 'City\NoticeController@add');// 添加
// Route::get('city/notice/select', 'City\NoticeController@select');// 选择-弹窗
Route::get('city/notice/export', 'City\NoticeController@export');// 导出
Route::get('city/notice/import_template', 'City\NoticeController@import_template');// 导入模版

// 收费标准
Route::get('city/feeScale', 'City\FeeScaleController@index');// 列表
Route::get('city/feeScale/add/{id}', 'City\FeeScaleController@add');// 添加
// Route::get('city/feeScale/select', 'City\FeeScaleController@select');// 选择-弹窗
Route::get('city/feeScale/export', 'City\FeeScaleController@export');// 导出
Route::get('city/feeScale/import_template', 'City\FeeScaleController@import_template');// 导入模版

// 收费标准-时间段
Route::get('city/feeScaleTime', 'City\FeeScaleTimeController@index');// 列表
Route::get('city/feeScaleTime/add/{id}', 'City\FeeScaleTimeController@add');// 添加
Route::get('city/feeScaleTime/addBath/{city_site_id}', 'City\FeeScaleTimeController@addBath');// 添加--按城市批量
// Route::get('city/feeScaleTime/select', 'City\FeeScaleTimeController@select');// 选择-弹窗
Route::get('city/feeScaleTime/export', 'City\FeeScaleTimeController@export');// 导出
Route::get('city/feeScaleTime/import_template', 'City\FeeScaleTimeController@import_template');// 导入模版

// 订单
Route::get('city/order', 'City\OrdersController@index');// 列表
Route::get('city/order/add/{id}', 'City\OrdersController@add');// 添加
// Route::get('city/order/select', 'City\OrdersController@select');// 选择-弹窗
Route::get('city/order/export', 'City\OrdersController@export');// 导出
Route::get('city/order/import_template', 'City\OrdersController@import_template');// 导入模版
Route::get('city/order/countOrders', 'City\OrdersController@countOrders');// 统计-订单数量


// ----商家后台
// Seller
Route::get('seller/index', 'Seller\IndexController@index');// 首页
Route::get('seller', 'Seller\IndexController@index');
Route::get('seller/login', 'Seller\IndexController@login');//login.html 登录
Route::get('seller/logout', 'Seller\IndexController@logout');// 注销
Route::get('seller/password', 'Seller\IndexController@password');//psdmodify.html 个人信息-修改密码
Route::get('seller/info', 'Seller\IndexController@info');//myinfo.html 个人信息--显示
// 腾讯地图
Route::get('seller/qqMaps/latLngSelect', 'Seller\QQMapsController@latLngSelect');// 经纬度选择

// 后台--管理员
//Route::get('seller/staff', 'Seller\StaffController@index');// 列表
//Route::get('seller/staff/add/{id}', 'Seller\StaffController@add');// 添加
//// Route::get('seller/staff/select', 'Seller\StaffController@select');// 选择-弹窗
//Route::get('seller/staff/export', 'Seller\StaffController@export');// 导出
//Route::get('seller/staff/import_template', 'Seller\StaffController@import_template');// 导入模版

// 加盟商--管理员
//Route::get('seller/staffPartner', 'Seller\StaffPartnerController@index');// 列表
//Route::get('seller/staffPartner/add/{id}', 'Seller\StaffPartnerController@add');// 添加
//// Route::get('seller/staffPartner/select', 'Seller\StaffPartnerController@select');// 选择-弹窗
//Route::get('seller/staffPartner/export', 'Seller\StaffPartnerController@export');// 导出
//Route::get('seller/staffPartner/import_template', 'Seller\StaffPartnerController@import_template');// 导入模版

// 商家--管理员
Route::get('seller/staffSeller', 'Seller\StaffSellerController@index');// 列表
Route::get('seller/staffSeller/add/{id}', 'Seller\StaffSellerController@add');// 添加
// Route::get('seller/staffSeller/select', 'Seller\StaffSellerController@select');// 选择-弹窗
Route::get('seller/staffSeller/export', 'Seller\StaffSellerController@export');// 导出
Route::get('seller/staffSeller/import_template', 'Seller\StaffSellerController@import_template');// 导入模版

// 店铺--管理员
Route::get('seller/staffShop', 'Seller\StaffShopController@index');// 列表
Route::get('seller/staffShop/add/{id}', 'Seller\StaffShopController@add');// 添加
// Route::get('seller/staffShop/select', 'Seller\StaffShopController@select');// 选择-弹窗
Route::get('seller/staffShop/export', 'Seller\StaffShopController@export');// 导出
Route::get('seller/staffShop/import_template', 'Seller\StaffShopController@import_template');// 导入模版

// 跑腿--管理员
//Route::get('seller/staffRun', 'Seller\StaffRunController@index');// 列表
//Route::get('seller/staffRun/add/{id}', 'Seller\StaffRunController@add');// 添加
//// Route::get('seller/staffRun/select', 'Seller\StaffRunController@select');// 选择-弹窗
//Route::get('seller/staffRun/export', 'Seller\StaffRunController@export');// 导出
//Route::get('seller/staffRun/import_template', 'Seller\StaffRunController@import_template');// 导入模版

// 用户--管理员
//Route::get('seller/staffUser', 'Seller\StaffUserController@index');// 列表
//Route::get('seller/staffUser/add/{id}', 'Seller\StaffUserController@add');// 添加
//// Route::get('seller/staffUser/select', 'Seller\StaffUserController@select');// 选择-弹窗
//Route::get('seller/staffUser/export', 'Seller\StaffUserController@export');// 导出
//Route::get('seller/staffUser/import_template', 'Seller\StaffUserController@import_template');// 导入模版

// 城市
//Route::get('seller/city', 'Seller\CityController@index');// 列表
//Route::get('seller/city/add/{id}', 'Seller\CityController@add');// 添加
//Route::get('seller/city/select', 'Seller\CityController@select');// 选择-弹窗
//Route::get('seller/city/export', 'Seller\CityController@export');// 导出
//Route::get('seller/city/import_template', 'Seller\CityController@import_template');// 导入模版

// 代理
//Route::get('seller/cityPartner', 'Seller\CityPartnerController@index');// 列表
//Route::get('seller/cityPartner/add/{id}', 'Seller\CityPartnerController@add');// 添加
//Route::get('seller/cityPartner/select', 'Seller\CityPartnerController@select');// 选择-弹窗
//Route::get('seller/cityPartner/export', 'Seller\CityPartnerController@export');// 导出
//Route::get('seller/cityPartner/import_template', 'Seller\CityPartnerController@import_template');// 导入模版

// 商家
//Route::get('seller/seller', 'Seller\SellerController@index');// 列表
//Route::get('seller/seller/add/{id}', 'Seller\SellerController@add');// 添加
//Route::get('seller/seller/select', 'Seller\SellerController@select');// 选择-弹窗
//Route::get('seller/seller/export', 'Seller\SellerController@export');// 导出
//Route::get('seller/seller/import_template', 'Seller\SellerController@import_template');// 导入模版

// 店铺分类
//Route::get('seller/shopType', 'Seller\ShopTypeController@index');// 列表
//Route::get('seller/shopType/add/{id}', 'Seller\ShopTypeController@add');// 添加
//// Route::get('seller/shopType/select', 'Seller\ShopTypeController@select');// 选择-弹窗
//Route::get('seller/shopType/export', 'Seller\ShopTypeController@export');// 导出
//Route::get('seller/shopType/import_template', 'Seller\ShopTypeController@import_template');// 导入模版

// 店铺商品属性
Route::get('seller/prop', 'Seller\PropController@index');// 列表
Route::get('seller/prop/add/{id}', 'Seller\PropController@add');// 添加
Route::get('seller/prop/select', 'Seller\PropController@select');// 选择-弹窗
Route::get('seller/prop/export', 'Seller\PropController@export');// 导出
Route::get('seller/prop/import_template', 'Seller\PropController@import_template');// 导入模版

// 店铺
Route::get('seller/shop', 'Seller\ShopController@index');// 列表
Route::get('seller/shop/add/{id}', 'Seller\ShopController@add');// 添加
Route::get('seller/shop/select', 'Seller\ShopController@select');// 选择-弹窗
Route::get('seller/shop/export', 'Seller\ShopController@export');// 导出
Route::get('seller/shop/import_template', 'Seller\ShopController@import_template');// 导入模版

// 商品
Route::get('seller/shopGoods', 'Seller\ShopGoodsController@index');// 列表
Route::get('seller/shopGoods/add/{id}', 'Seller\ShopGoodsController@add');// 添加
Route::get('seller/shopGoods/select', 'Seller\ShopGoodsController@select');// 选择-弹窗
Route::get('seller/shopGoods/export', 'Seller\ShopGoodsController@export');// 导出
Route::get('seller/shopGoods/import_template', 'Seller\ShopGoodsController@import_template');// 导入模版

// 店铺商品分类[一级分类]
Route::get('seller/shopGoodsType', 'Seller\ShopGoodsTypeController@index');// 列表
Route::get('seller/shopGoodsType/add/{id}', 'Seller\ShopGoodsTypeController@add');// 添加
// Route::get('seller/shopGoodsType/select', 'Seller\ShopGoodsTypeController@select');// 选择-弹窗
Route::get('seller/shopGoodsType/export', 'Seller\ShopGoodsTypeController@export');// 导出
Route::get('seller/shopGoodsType/import_template', 'Seller\ShopGoodsTypeController@import_template');// 导入模版

// 店铺营业时间
Route::get('seller/shopOpenTime', 'Seller\ShopOpenTimeController@index');// 列表
Route::get('seller/shopOpenTime/add/{id}', 'Seller\ShopOpenTimeController@add');// 添加
// Route::get('seller/shopOpenTime/select', 'Seller\ShopOpenTimeController@select');// 选择-弹窗
Route::get('seller/shopOpenTime/export', 'Seller\ShopOpenTimeController@export');// 导出
Route::get('seller/shopOpenTime/import_template', 'Seller\ShopOpenTimeController@import_template');// 导入模版


// 站点介绍
//Route::get('seller/siteIntro', 'Seller\SiteIntroController@index');// 列表
//Route::get('seller/siteIntro/add/{id}', 'Seller\SiteIntroController@add');// 添加
//// Route::get('seller/siteIntro/select', 'Seller\SiteIntroController@select');// 选择-弹窗
//Route::get('seller/siteIntro/export', 'Seller\SiteIntroController@export');// 导出
//Route::get('seller/siteIntro/import_template', 'Seller\SiteIntroController@import_template');// 导入模版

// 标签[一级分类]
//Route::get('seller/labels', 'Seller\LabelsController@index');// 列表
//Route::get('seller/labels/add/{id}', 'Seller\LabelsController@add');// 添加
//// Route::get('seller/labels/select', 'Seller\LabelsController@select');// 选择-弹窗
//Route::get('seller/labels/export', 'Seller\LabelsController@export');// 导出
//Route::get('seller/labels/import_template', 'Seller\LabelsController@import_template');// 导入模版

// 公告
//Route::get('seller/notice', 'Seller\NoticeController@index');// 列表
//Route::get('seller/notice/add/{id}', 'Seller\NoticeController@add');// 添加
//// Route::get('seller/notice/select', 'Seller\NoticeController@select');// 选择-弹窗
//Route::get('seller/notice/export', 'Seller\NoticeController@export');// 导出
//Route::get('seller/notice/import_template', 'Seller\NoticeController@import_template');// 导入模版

// 订单
Route::get('seller/order', 'Seller\OrdersController@index');// 列表
Route::get('seller/order/add/{id}', 'Seller\OrdersController@add');// 添加
// Route::get('seller/order/select', 'Seller\OrdersController@select');// 选择-弹窗
Route::get('seller/order/export', 'Seller\OrdersController@export');// 导出
Route::get('seller/order/import_template', 'Seller\OrdersController@import_template');// 导入模版



// ----店铺后台
// Seller
Route::get('shop/index', 'Shop\IndexController@index');// 首页
Route::get('shop', 'Shop\IndexController@index');
Route::get('shop/login', 'Shop\IndexController@login');//login.html 登录
Route::get('shop/logout', 'Shop\IndexController@logout');// 注销
Route::get('shop/password', 'Shop\IndexController@password');//psdmodify.html 个人信息-修改密码
Route::get('shop/info', 'Shop\IndexController@info');//myinfo.html 个人信息--显示
// 腾讯地图
Route::get('shop/qqMaps/latLngSelect', 'Shop\QQMapsController@latLngSelect');// 经纬度选择

// 后台--管理员
//Route::get('shop/staff', 'Shop\StaffController@index');// 列表
//Route::get('shop/staff/add/{id}', 'Shop\StaffController@add');// 添加
//// Route::get('shop/staff/select', 'Shop\StaffController@select');// 选择-弹窗
//Route::get('shop/staff/export', 'Shop\StaffController@export');// 导出
//Route::get('shop/staff/import_template', 'Shop\StaffController@import_template');// 导入模版

// 加盟商--管理员
//Route::get('shop/staffPartner', 'Shop\StaffPartnerController@index');// 列表
//Route::get('shop/staffPartner/add/{id}', 'Shop\StaffPartnerController@add');// 添加
//// Route::get('shop/staffPartner/select', 'Shop\StaffPartnerController@select');// 选择-弹窗
//Route::get('shop/staffPartner/export', 'Shop\StaffPartnerController@export');// 导出
//Route::get('shop/staffPartner/import_template', 'Shop\StaffPartnerController@import_template');// 导入模版

// 商家--管理员
//Route::get('shop/staffSeller', 'Shop\StaffSellerController@index');// 列表
//Route::get('shop/staffSeller/add/{id}', 'Shop\StaffSellerController@add');// 添加
//// Route::get('shop/staffSeller/select', 'Shop\StaffSellerController@select');// 选择-弹窗
//Route::get('shop/staffSeller/export', 'Shop\StaffSellerController@export');// 导出
//Route::get('shop/staffSeller/import_template', 'Shop\StaffSellerController@import_template');// 导入模版

// 店铺--管理员
Route::get('shop/staffShop', 'Shop\StaffShopController@index');// 列表
Route::get('shop/staffShop/add/{id}', 'Shop\StaffShopController@add');// 添加
// Route::get('shop/staffShop/select', 'Shop\StaffShopController@select');// 选择-弹窗
Route::get('shop/staffShop/export', 'Shop\StaffShopController@export');// 导出
Route::get('shop/staffShop/import_template', 'Shop\StaffShopController@import_template');// 导入模版

// 跑腿--管理员
//Route::get('shop/staffRun', 'Shop\StaffRunController@index');// 列表
//Route::get('shop/staffRun/add/{id}', 'Shop\StaffRunController@add');// 添加
//// Route::get('shop/staffRun/select', 'Shop\StaffRunController@select');// 选择-弹窗
//Route::get('shop/staffRun/export', 'Shop\StaffRunController@export');// 导出
//Route::get('shop/staffRun/import_template', 'Shop\StaffRunController@import_template');// 导入模版

// 用户--管理员
//Route::get('shop/staffUser', 'Shop\StaffUserController@index');// 列表
//Route::get('shop/staffUser/add/{id}', 'Shop\StaffUserController@add');// 添加
//// Route::get('shop/staffUser/select', 'Shop\StaffUserController@select');// 选择-弹窗
//Route::get('shop/staffUser/export', 'Shop\StaffUserController@export');// 导出
//Route::get('shop/staffUser/import_template', 'Shop\StaffUserController@import_template');// 导入模版

// 城市
//Route::get('shop/city', 'Shop\CityController@index');// 列表
//Route::get('shop/city/add/{id}', 'Shop\CityController@add');// 添加
//Route::get('shop/city/select', 'Shop\CityController@select');// 选择-弹窗
//Route::get('shop/city/export', 'Shop\CityController@export');// 导出
//Route::get('shop/city/import_template', 'Shop\CityController@import_template');// 导入模版

// 代理
//Route::get('shop/cityPartner', 'Shop\CityPartnerController@index');// 列表
//Route::get('shop/cityPartner/add/{id}', 'Shop\CityPartnerController@add');// 添加
//Route::get('shop/cityPartner/select', 'Shop\CityPartnerController@select');// 选择-弹窗
//Route::get('shop/cityPartner/export', 'Shop\CityPartnerController@export');// 导出
//Route::get('shop/cityPartner/import_template', 'Shop\CityPartnerController@import_template');// 导入模版

// 商家
//Route::get('shop/seller', 'Shop\SellerController@index');// 列表
//Route::get('shop/seller/add/{id}', 'Shop\SellerController@add');// 添加
//Route::get('shop/seller/select', 'Shop\SellerController@select');// 选择-弹窗
//Route::get('shop/seller/export', 'Shop\SellerController@export');// 导出
//Route::get('shop/seller/import_template', 'Shop\SellerController@import_template');// 导入模版

// 店铺分类
//Route::get('shop/shopType', 'Shop\ShopTypeController@index');// 列表
//Route::get('shop/shopType/add/{id}', 'Shop\ShopTypeController@add');// 添加
//// Route::get('shop/shopType/select', 'Shop\ShopTypeController@select');// 选择-弹窗
//Route::get('shop/shopType/export', 'Shop\ShopTypeController@export');// 导出
//Route::get('shop/shopType/import_template', 'Shop\ShopTypeController@import_template');// 导入模版

// 店铺商品属性
Route::get('shop/prop', 'Shop\PropController@index');// 列表
Route::get('shop/prop/add/{id}', 'Shop\PropController@add');// 添加
Route::get('shop/prop/select', 'Shop\PropController@select');// 选择-弹窗
Route::get('shop/prop/export', 'Shop\PropController@export');// 导出
Route::get('shop/prop/import_template', 'Shop\PropController@import_template');// 导入模版

// 店铺
Route::get('shop/shop', 'Shop\ShopController@index');// 列表
Route::get('shop/shop/add/{id}', 'Shop\ShopController@add');// 添加
Route::get('shop/shop/select', 'Shop\ShopController@select');// 选择-弹窗
Route::get('shop/shop/export', 'Shop\ShopController@export');// 导出
Route::get('shop/shop/import_template', 'Shop\ShopController@import_template');// 导入模版

// 商品
Route::get('shop/shopGoods', 'Shop\ShopGoodsController@index');// 列表
Route::get('shop/shopGoods/add/{id}', 'Shop\ShopGoodsController@add');// 添加
Route::get('shop/shopGoods/select', 'Shop\ShopGoodsController@select');// 选择-弹窗
Route::get('shop/shopGoods/export', 'Shop\ShopGoodsController@export');// 导出
Route::get('shop/shopGoods/import_template', 'Shop\ShopGoodsController@import_template');// 导入模版

// 店铺商品分类[一级分类]
Route::get('shop/shopGoodsType', 'Shop\ShopGoodsTypeController@index');// 列表
Route::get('shop/shopGoodsType/add/{id}', 'Shop\ShopGoodsTypeController@add');// 添加
// Route::get('shop/shopGoodsType/select', 'Shop\ShopGoodsTypeController@select');// 选择-弹窗
Route::get('shop/shopGoodsType/export', 'Shop\ShopGoodsTypeController@export');// 导出
Route::get('shop/shopGoodsType/import_template', 'Shop\ShopGoodsTypeController@import_template');// 导入模版

// 店铺营业时间
Route::get('shop/shopOpenTime', 'Shop\ShopOpenTimeController@index');// 列表
Route::get('shop/shopOpenTime/add/{id}', 'Shop\ShopOpenTimeController@add');// 添加
// Route::get('shop/shopOpenTime/select', 'Shop\ShopOpenTimeController@select');// 选择-弹窗
Route::get('shop/shopOpenTime/export', 'Shop\ShopOpenTimeController@export');// 导出
Route::get('shop/shopOpenTime/import_template', 'Shop\ShopOpenTimeController@import_template');// 导入模版


// 站点介绍
//Route::get('shop/siteIntro', 'Shop\SiteIntroController@index');// 列表
//Route::get('shop/siteIntro/add/{id}', 'Shop\SiteIntroController@add');// 添加
//// Route::get('shop/siteIntro/select', 'Shop\SiteIntroController@select');// 选择-弹窗
//Route::get('shop/siteIntro/export', 'Shop\SiteIntroController@export');// 导出
//Route::get('shop/siteIntro/import_template', 'Shop\SiteIntroController@import_template');// 导入模版

// 标签[一级分类]
//Route::get('shop/labels', 'Shop\LabelsController@index');// 列表
//Route::get('shop/labels/add/{id}', 'Shop\LabelsController@add');// 添加
//// Route::get('shop/labels/select', 'Shop\LabelsController@select');// 选择-弹窗
//Route::get('shop/labels/export', 'Shop\LabelsController@export');// 导出
//Route::get('shop/labels/import_template', 'Shop\LabelsController@import_template');// 导入模版

// 公告
//Route::get('shop/notice', 'Shop\NoticeController@index');// 列表
//Route::get('shop/notice/add/{id}', 'Shop\NoticeController@add');// 添加
//// Route::get('shop/notice/select', 'Shop\NoticeController@select');// 选择-弹窗
//Route::get('shop/notice/export', 'Shop\NoticeController@export');// 导出
//Route::get('shop/notice/import_template', 'Shop\NoticeController@import_template');// 导入模版

// 订单
Route::get('shop/order', 'Shop\OrdersController@index');// 列表
Route::get('shop/order/add/{id}', 'Shop\OrdersController@add');// 添加
// Route::get('shop/order/select', 'Shop\OrdersController@select');// 选择-弹窗
Route::get('shop/order/export', 'Shop\OrdersController@export');// 导出
Route::get('shop/order/import_template', 'Shop\OrdersController@import_template');// 导入模版
