<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-01-16
 * Time: 17:27
 */

namespace App\Services\Map;


class Map
{

    /**
     * [PHP Code] 根据经纬度计算两点之间的记录
     * @param $lat1 纬度1
     * @param $lng1 经度1
     * @param $lat2 纬度2
     * @param $lng2 经度2
     * @return float 单位(米)
     */
    public static function  getDistance($lat1, $lng1, $lat2, $lng2)
    {
        //地球半径
        $R = 6378137;

        //将角度转为弧度
        $radLat1 = deg2rad($lat1);
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);

        //结果
        $s = acos(cos($radLat1) * cos($radLat2) * cos($radLng1 - $radLng2)
                + sin($radLat1) * sin($radLat2)) * $R;

        //精度
        $s = round($s * 10000)/10000;

        return  round($s);
    }
}