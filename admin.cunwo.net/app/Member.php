<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Member extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * 获取会储存到 jwt 声明中的标识，其实就是要我们返回标识用户表的主键字段名称，这里是返回的是主键 'id',
     * @return mixed
     */
    public function getJWTIdentifier()
    {

         return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * 返回一个键值数组，其中包含要添加到jwt的任何自定义声明。
     * 返回包含要添加到 jwt 声明中的自定义键值对数组，这里返回空数组，没有添加任何自定义信息。
     * @return array
     */
    public function getJWTCustomClaims()
    {

//        $tokenData = [
//            'userData' =>[
//                'name' => 1,
//                'email' => '我是邹燕aa',
//                'password' => '我是邹燕bb',
//            ]
//        ];
      // return ['id' => 8, 'userData' =>'aaaaaa'];//$tokenData;
        return [];
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
