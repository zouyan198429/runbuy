<?php

namespace App\Models\RunBuy;

use App\Models\BaseModel;

class BasePublicModel extends BaseModel
{


    //------- 多对多的多态关联-----开始------------------

    /**
     * 获取指定***模块所有图片资源[二维对象]
     */
    public function siteResources()
    {
        return $this->morphToMany(
            'App\Models\Resource'//资源对象
            ,'module' // 关系名称-注意：这个值必须是表中 ***_type 的星号部分，暂时还没有指定***_type 这个字段
            ,'resource_module'// 关系表名称
        // ,'module_id'// 关系表中的与新闻表主键对应的字段
        // ,'resource_id'// 关系表中的与资源对象主键对应的字段
        // ,'id'// 主表新闻主键字段名
        // ,'id'// 资源对象主键字段名
        // ,$inverse 参数 flase[默认]，module_type 可以在 AppServiceProvider 中指定段名; true： 必须用App\Models\Resource
        )->withPivot('id', 'company_id', 'operate_staff_id', 'operate_staff_id_history' )->withTimestamps();// ->withPivot('notice', 'id')
    }

    // 同步修改图片资源关系-
    /**
     * 获取指定***模块所有图片资源[二维对象]
     *       $siteNew = SiteNews::find(1);
     *       $siteNew->siteResources()->sync([1, 2]);
     *          的封装
     * 模块 单条的对象  SiteNews::find(1)->updateResourceByResourceIds([1,2,3]);
     * @param array $resourceIds 需要操作的资源id数组,空数组：代表删除
     */
    public function updateResourceByResourceIds($resourceIds = [])
    {
        $this->siteResources()->sync($resourceIds);
    }

    //------- 多对多的多态关联-----结束------------------

    //---------一对多--------开始----------

    /**
     * 获取**模块[如相册]所属的公司 - 一维
     * 公司对其它，1：n的反向
     */
//    public function CompanyInfo()
//    {
//        return $this->belongsTo('App\Models\RunBuy\Company', 'company_id', 'id');
//    }

    /**
     * 获取操作***表的员工-一维
     */
    public function oprateStaff()
    {
        return $this->belongsTo('App\Models\RunBuy\Staff', 'operate_staff_id', 'id');
    }

    /**
     * 获取操作***表员工的历史-一维
     */
    public function oprateStaffHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\StaffHistory', 'operate_staff_id_history', 'id');
    }

    /**
     * 获取**模块[如家事记录]所属的生产单元 - 一维
     * 生产单元对其它，1：n的反向
     */
//    public function companyProUnit()
//    {
//        return $this->belongsTo('App\Models\RunBuy\CompanyProUnit', 'pro_unit_id', 'id');
//    }

    /**
     * 获取**模块的操作人员所属的帐号 - 一维
     * 帐号对其它模块操作人，1：n的反向
     */
//    public function companyAccount()
//    {
//        return $this->belongsTo('App\Models\RunBuy\CompanyAccounts', 'account_id', 'id');
//    }

    /**
     * 获取**模块province_id 对应的省
     */
    public function province()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'province_id', 'id');
    }

    /**
     * 获取**模块province_id 对应的省历史
     */
    public function provinceHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityHistory', 'province_id_history', 'id');
    }

    /**
     * 获取**模块city_id 对应的市
     */
    public function city()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'city_id', 'id');
    }

    /**
     * 获取**模块city_id 对应的市历史
     */
    public function cityHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityHistory', 'city_id_history', 'id');
    }

    /**
     * 获取**模块area_id 对应的城市[包括县乡]
     */
    public function area()
    {
        return $this->belongsTo('App\Models\RunBuy\City', 'area_id', 'id');
    }

    /**
     * 获取**模块area_id 对应的城市[包括县乡]历史
     */
    public function areaHistory()
    {
        return $this->belongsTo('App\Models\RunBuy\CityHistory', 'area_id_history', 'id');
    }
    //---------一对多--------结束----------

}
