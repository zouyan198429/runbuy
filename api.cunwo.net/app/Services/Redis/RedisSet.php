<?php

namespace App\Services\Redis;


class RedisSet
{
    // set 集合操作  sadd增加set集合元素， 返回true， 重复返回false
//    Redis::sadd('set1', 'ab');
//
//    Redis::sadd('set1', 'cd');
//
//    Redis::sadd('set1', 'ef');
//
//    //srem 移除指定元素
//
//    Redis::srem('set1', 'cd'); // 删除'cd'元素
//    //spop 弹出首元素
//
//    Redis::spop('set1'); // 返回 'ab'
//    //smove 移动当前set集合的指定元素到另一个set集合
//
//
//
//    Redis::sadd('set2', '123');
//
//    Redis::smove('set1', 'set2', 'ab'); // 移动'set1'中的'ab'到'set2', 返回true or false；此时 'set1'集合不存在 'ab' 这个值
//
//    //scard 返回当前set表元素个数
//
//    Redis::scard('set2'); // 返回 2
//    //sismember 判断元素是否属于当前set集合
//
//    Redis::sismember('set2', '123'); // 返回 true or false
//    //smembers 返回当前set集合的所有元素
//
//    Redis::smembers('set2'); // 返回 array('123','ab')
//    //sinter/sunion/sdiff 返回两个表中元素的交集/并集/补集
//
//
//
//    Redis::sadd('set1', 'ab') ;
//
//    Redis::sinter('set2', 'set1') ; //返回array('ab')
//
//    //sinterstore/sunionstore/sdiffstore 将两个表交集/并集/补集元素 copy 到第三个表中
//
//
//
//    Redis::set('foo', 0);
//
//    Redis::sinterstore('foo', 'set1'); // 等同于将'set1'的内容copy到'foo'中，并将'foo'转为set表
//
//    Redis::sinterstore('foo', array('set1', 'set2')); // 将'set1'和'set2'中相同的元素 copy 到'foo'表中, 覆盖'foo'原有内容
//
//    //srandmember 返回表中一个随机元素
//
//    Redis::srandmember('set1') ;
}