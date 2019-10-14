<?php
namespace App\Services\Redis;


class RedisList
{
//    //rpush/rpushx 有序列表操作,从队列后插入元素；lpush/lpushx 和 rpush/rpushx 的区别是插入到队列的头部,同上,'x'含义是只对已存在的 key 进行操作
//
//    Redis::rpush('fooList', 'bar1'); // 返回列表长度 1
//    Redis::lpush('fooList', 'bar2'); // 返回列表长度 2
//    Redis::rpushx('fooList', 'bar3'); // 返回 3, rpushx只对已存在的队列做添加,否则返回 0
//
//
//    //llen返回当前列表长度
//    var_dump(Redis::llen('fooList')); //返回3
//
//
//    //lrange 返回队列中一个区间的元素
//    var_dump(Redis::lrange ('fooList', 0, 1)); // 返回数组包含第 0 个至第 1 个, 共2个元素
//    var_dump(Redis::lrange ('fooList', 0, -1)); //返回第0个至倒数第一个, 相当于返回所有元素
//
//    //lindex 返回指定顺序位置的 list 元素
//    var_dump(Redis::lindex('fooList', 1)); // 返回'bar1
//
//    //lset 修改队列中指定位置的value
//    Redis::lset('fooList', 1, '123'); // 修改位置 1 的元素, 返回 true
//
//    //lrem 删除队列中左起指定数量的字符
//    Redis::lrem('fooList', 1, '_') ; // 删除队列中左起(右起使用-1) 1个 字符'_'(若有)
//
//    //lpop/rpop 类似栈结构地弹出(并删除)最左或最右的一个元素
//    Redis::lpop('fooList') ; // 返回 'bar0'
//
//    Redis::rpop('fooList') ; // 返回 'bar2'
//
//    //ltrim队列修改，保留左边起若干元素，其余删除
//    Redis::ltrim('fooList', 0, 1) ; // 保留左边起第 0 个至第 1 个元素
//
//    //rpoplpush 从一个队列中 pop 出元素并 push 到另一个队列
//
//    Redis::rpush('list1', 'ab0');
//
//    Redis::rpush('list1', 'ab1');
//
//    Redis::rpush('list2', 'ab2');
//
//    Redis::rpush('list2', 'ab3');
//
//    Redis::rpoplpush('list1', 'list2'); // 结果list1 =>array('ab0'), list2 =>array('ab1','ab2','ab3')
//
//    Redis::rpoplpush('list2', 'list2'); // 也适用于同一个队列, 把最后一个元素移到头部 list2 =>array('ab3','ab1','ab2')
//
//
//    //linsert在队列的中间指定元素前或后插入元素
//    Redis::linsert('list2', 'before', 'ab1', '123'); //表示在元素 'ab1' 之前插入 '123'
//
//    Redis::linsert('list2', 'after', 'ab1', '456'); //表示在元素 'ab1' 之后插入 '456'
//
//
//    //blpop/brpop 阻塞并等待一个列队不为空时，再pop出最左或最右的一个元素（这个功能在php以外可以说非常好用）
//    Redis::blpop('list3', 10) ; // 如果 list3 为空则一直等待,直到不为空时将第一元素弹出, 10 秒后超时
//
}