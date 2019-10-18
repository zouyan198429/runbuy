<?php
namespace App\Api\Services;
// 创建服务
use App\Api\Actions\CreateUser;
use App\Api\Response;
use App\Models\Member;

class UserService
{
    public $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function register($data)
    {
        try {
            return Response::success ((new CreateUser())->execute ($data));
        } catch (\Exception $e) {
            return Response::error ($e->getMessage ());
        }
    }
}