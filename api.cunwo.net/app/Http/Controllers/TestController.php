<?php

namespace App\Http\Controllers;

//use App\Models\Resource;
//use App\Models\SiteNews;
//use App\Models\test\Comment;
//use App\Models\test\Post;
use App\Business\DB\RunBuy\CityDBBusiness;
use App\Business\DB\RunBuy\LrChinaCityDBBusiness;
// use App\Models\LrChinaCity;
use App\Services\GetPingYing;
use App\Services\Map\Map;
use App\Services\pyClass;
use Illuminate\Http\Request;

class TestController extends CompController
{

    /**
     * geoHash
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function geoHash(Request $request)
    {
        $log = 117.031689;
        $lat = 36.65396;
        // php geohash类
        // $hash = GeoHash::encode($log,$lat);// wwe0x0euu12
        // vd($hash);
        // $nearHash  = GeoHash::expand('wwe0x0');// 附近8个
        // pr($nearHash);
        // $point = GeoHash::decode('wwe0x0');
        // pr($point);

        // php geoHash扩展
        // $hash = geohash_encode($lat, $log, 12);// wwe0x0euu12h
        // vd($hash);
        // $nearHash  = geohash_neighbors('wwe0x0');
        // pr($nearHash);
        // $point = geohash_decode('wwe0x0');
        // pr($point);

        // 自已写的方法
        // $hash = Map::encode_geohash($lat, $log, 12);// wwe0x0euu12h
        // vd($hash);
        // $point = geohash_decode('wwe0x0');
        // pr($point);

        // 获得周边的四方形坐标
        // $squrePoint = Map::returnSquarePoint($log, $lat,0.5);
        // pr($squrePoint);
        // foreach($squrePoint as $k => $v){
        //     $squrePoint[$k]['getDistance']= Map::getDistance($lat, $log, $v['lat'], $v['lng']);
        //     $squrePoint[$k]['getDistanceM']= Map::getDistanceM($log,$lat, $v['lng'], $v['lat']);
        //     $squrePoint[$k]['getDistanceKM']= Map::getDistanceKM($lat, $log, $v['lat'], $v['lng']);
        // }
        // pr($squrePoint);
        $hashs = Map::getGeoHashs($lat, $log);
        pr($hashs);
        echo 'test';
        // return view('test');
    }

    /**
     * uber H3  有问题，暂不用
     *
     * @param Request $request
     * @return mixed
     * @author zouyan(305463219@qq.com)
     */
    public function h3(Request $request)
    {
        $lat = 57.3615593;// 纬度
        $log = -122.0553238;// 经度
        echo '<br/> geoToH3: <br/>';
        $index = geoToH3($lat,$log,10);
//        var_dump($index);
         pr($index,false);
        // $index = h3ToLong($index);
        // pr(h3ToString($index),false);

        // var_dump($index, h3ToLong($index));
       // echo '<br/> h3ToGeo: <br/>';
//        var_dump(h3ToGeo($index));
        pr(h3ToGeo($index),false);

        echo '<br/> h3ToGeoBoundary: <br/>';
//        var_dump(h3ToGeoBoundary($index));
        pr(h3ToGeoBoundary($index),false);

        echo '<br/> h3GetResolution: <br/>';
//        var_dump(h3GetResolution($index));
        pr(h3GetResolution($index),false);

        echo '<br/> h3GetBaseCell: <br/>';
//        var_dump(h3GetBaseCell($index));
        pr(h3GetBaseCell($index),false);

        echo '<br/> h3ToString: <br/>';
//        var_dump(h3ToString($index, "hello world"));
        pr(h3ToString($index, "hello world"),false);

        echo '<br/> h3IsValid: <br/>';
//        var_dump(h3IsValid($index));
        pr(h3IsValid($index),false);

        echo '<br/> h3IsResClassIII: <br/>';
//        var_dump(h3IsResClassIII($index));
        pr(h3IsResClassIII($index),false);

        echo '<br/> h3IsPentagon: <br/>';
//        var_dump(h3IsPentagon($index));
        pr(h3IsPentagon($index),false);

        echo '<br/> kRing: <br/>';
//        var_dump(kRing($index, 5));
        pr(kRing($index, 5),false);

        echo '<br/> maxKringSize: <br/>';
//        var_dump(maxKringSize(5));
        pr(maxKringSize(5),false);

        echo '<br/> kRingDistances: <br/>';
//        var_dump(kRingDistances($index, 5));
        pr(kRingDistances($index, 5),false);

        echo '<br/> hexRange: <br/>';
//        var_dump(hexRange($index, 5));
        pr(hexRange($index, 5),false);

        echo '<br/> hexRangeDistances: <br/>';
//        var_dump(hexRangeDistances($index, 5));
        pr(hexRangeDistances($index, 5),false);

        echo '<br/> geoToH3: <br/>';
        $index1 = geoToH3(341.689167, -173.044444, 10);
        pr($index1,false);

        echo '<br/> hexRanges: <br/>';
//        var_dump(hexRanges([$index, $index1], 5));
        pr(hexRanges([$index, $index1], 5),false);

        echo '<br/> hexRing: <br/>';
//        var_dump(hexRing($index, 5));
        pr(hexRing($index, 5),false);

        echo '<br/> h3Distance: <br/>';
//        var_dump(h3Distance($index, $index1));
        pr(h3Distance($index, $index1),false);

        echo '<br/> h3ToParent: <br/>';
//        var_dump(h3ToParent($index, 5));
        pr(h3ToParent($index, 5),false);

        echo '<br/> h3ToChildren: <br/>';
//        var_dump(h3ToChildren($index, 2));
        pr(h3ToChildren($index, 2),false);

        echo '<br/> maxH3ToChildrenSize: <br/>';
//        var_dump(maxH3ToChildrenSize($index, 2));
        pr(maxH3ToChildrenSize($index, 2),false);

        echo '<br/> degsToRads: <br/>';
//        var_dump($rads = degsToRads(40.689167));
        $rads = degsToRads(40.689167);
        pr($rads,false);

        echo '<br/> radsToDegs: <br/>';
//        var_dump(radsToDegs($rads));
        pr(radsToDegs($rads),false);

        echo '<br/> hexAreaKm2: <br/>';
//        var_dump(hexAreaKm2(10));
        pr(hexAreaKm2(10),false);

        echo '<br/> hexAreaM2: <br/>';
//        var_dump(hexAreaM2(10));
        pr(hexAreaM2(10),false);

        echo '<br/> edgeLengthKm: <br/>';
//        var_dump(edgeLengthKm(10));
        pr(edgeLengthKm(10),false);

        echo '<br/> edgeLengthM: <br/>';
//        var_dump(edgeLengthM(10));
        pr(edgeLengthM(10),false);

        echo '<br/> numHexagons: <br/>';
//        var_dump(numHexagons(2));
        pr(numHexagons(2),false);


        echo '<br/> h3IndexesAreNeighbors: <br/>';
//        var_dump(h3IndexesAreNeighbors($index, $index1));
        pr(h3IndexesAreNeighbors($index, $index1),false);
        echo '<br/> getH3UnidirectionalEdge: <br/>';
//        var_dump(getH3UnidirectionalEdge($index, $index1));
        pr(getH3UnidirectionalEdge($index, $index1),false);
        echo '<br/> h3UnidirectionalEdgeIsValid: <br/>';
//        var_dump(h3UnidirectionalEdgeIsValid($index));
        pr(h3UnidirectionalEdgeIsValid($index),false);
        echo '<br/> getOriginH3IndexFromUnidirectionalEdge: <br/>';
//        var_dump(getOriginH3IndexFromUnidirectionalEdge($index));
        pr(getOriginH3IndexFromUnidirectionalEdge($index),false);
        echo '<br/> getDestinationH3IndexFromUnidirectionalEdge: <br/>';
//        var_dump(getDestinationH3IndexFromUnidirectionalEdge($index));
        pr(getDestinationH3IndexFromUnidirectionalEdge($index),false);
        echo '<br/> getH3IndexesFromUnidirectionalEdge: <br/>';
//        var_dump(getH3IndexesFromUnidirectionalEdge($index));
        pr(getH3IndexesFromUnidirectionalEdge($index),false);
        echo '<br/> getH3UnidirectionalEdgesFromHexagon: <br/>';
//        var_dump(getH3UnidirectionalEdgesFromHexagon($index));
        pr(getH3UnidirectionalEdgesFromHexagon($index),false);
        echo '<br/> getH3UnidirectionalEdgeBoundary: <br/>';
//        var_dump(getH3UnidirectionalEdgeBoundary($index));
        pr(getH3UnidirectionalEdgeBoundary($index),false);

        echo '<br/> h3Compact: <br/>';
//        var_dump($compacts = h3Compact([$index, $index1]));
        $compacts = h3Compact([$index, $index1]);
        pr($compacts,false);
        echo '<br/> uncompact: <br/>';
//        var_dump(uncompact($compacts, 2));
        pr(uncompact($compacts, 2),false);
        echo '<br/> maxUncompactSize: <br/>';
//        var_dump(maxUncompactSize($compacts, 2));
        pr(maxUncompactSize($compacts, 2),false);
    }
}
