
/**
 * 规则类型
 * params，传入的参数，object -- 特别说明：尽可能的用对象：因为对象要加上其它新的字段  appid、 noncestr 、timestamp
 * getType 1 大后台
 */
function getSignByObj(params, kAppKey, kAppSecret, getType) {
    switch(getType){
        case 1://1大后台
            adminAddParams(params, kAppKey);
            return getSign(params, kAppKey, kAppSecret, getType);
            break;
        default:
            return params;
    }

}

/*
 * @Author: chenjun
 * @Date:   2017-12-28 17:09:21
 * @Last Modified by:   0easy-23
 * @Last Modified time: 2017-12-29 10:09:23
 * 签名生成	
 * kAppKey,kAppSecret为常量，
 * params，传入的参数，string || object
 * getType 1 大后台
 * 需要借助md5.js
 * 规则：将所有参数字段按首字母排序， 拼接成key1 = value1 & key2 = value2的格式，再在末尾拼接上key = appSecret， 再做MD5加密生成sign
 */

function getSign(params, kAppKey, kAppSecret, getType) {
    console.log('----params----------',params);
    if (typeof params == "string") {
        return paramsStrSort(params, kAppKey, kAppSecret, getType);
    } else if (typeof params == "object") {
        var arr = [];
        for (var i in params) {
            // 跳过对象
            if(typeof params[i] == "object") continue;
            arr.push((i + "=" + params[i]));
        }
        return paramsStrSort(arr.join("&"), kAppKey, kAppSecret, getType);
    }
}

function paramsStrSort( paramsStr, kAppKey, kAppSecret, getType) {

    // var randomVal = get_unix_time_random(false,0,10000);// 随机数据
    // // 参数不为空
    // if(!judge_empty(paramsStr)){
    //     paramsStr = paramsStr + '&';
    // }

    // console.log('----paramsStr----------',paramsStr);
    // // var url = paramsStr + "&appKey=" + kAppKey;
    var url = paramsStr;
    // var url = paramsStr + "appid=" + kAppKey + "&noncestr=" + randomVal;
    // var urlStr = url.split("&").sort().join("&");
    var urlStr = url.split("&").sort().join("&");
    console.log('----urlStr----------',urlStr);
    var newUrl = '';
    var sign = '';
    switch(getType){
        case 1://1大后台
            // var newUrl = urlStr + '&key=' + kAppSecret;
            newUrl = kAppSecret + urlStr + kAppSecret;
            // return md5(newUrl);
            sign = sha1(newUrl);
            break;
        default:
            // var newUrl = urlStr + '&key=' + kAppSecret;
            newUrl = kAppSecret + urlStr + kAppSecret;
            // return md5(newUrl);
            sign = sha1(newUrl);
            break;
    }
    console.log('----newUrl----------',newUrl);
    console.log('----sign----------',sign);
    return sign;
}

//~~~~~~~~~~~~~~~~~~~~具体~~接口的特殊参数~~~~~~~~~~~~~~~~~~~~~~
// 为参数对象加新的参数
// paramsObj 原参数对象
// kAppKey 项目id
function adminAddParams(paramsObj, kAppKey){
    var randomVal = md5(get_unix_time_random(false,0,10000));// 随机数据
    paramsObj['noncestr'] = randomVal;// 生成随机数（防重放）
    paramsObj['appid'] = kAppKey;
    paramsObj['timestamp'] = get_unix_time('',false);
}