<?php
namespace App\Api\Actions;
// 创建动作
use App\Models\Member;
use App\Models\MemberData;

class CreateUser
{
    /**
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function execute(array $data)
    {

        $member           = new Member();
        $member->tel      = $data['tel'];
        $member->password = md5 ($data['password']);
        $result           = $member->save ();

        if (!$result) {
            throw new \Exception('注册失败');
        }

        $memberData            = new MemberData();
        $memberData->member_id = $member->id;
        $memberData->sex       = "2";
        $memberData->nick_name = "";
        $memberData->img       = "";
        $memberData->save ();

        return $member->id;
    }
}