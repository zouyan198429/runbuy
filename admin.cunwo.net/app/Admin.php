<?php

namespace App;


class Admin extends User
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'users';
}
