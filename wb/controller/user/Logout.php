<?php


namespace controller\user;


use common\model\Db;
use model\User as mUser;
use common\utils\Format;

class Logout
{
    use Format;

    public function index($userInfo)
    {
        $num = Db::table(mUser::$tableName)
            ->andWhere(mUser::$id, '=', $userInfo[mUser::$id])
            ->update([mUser::$tokenExpiresTime => 0]);
        if ($num) {
            $resData = $this->toPair([0, '退出登录成功', ''], true);
        } else {
            $resData = $this->toPair([2201, 'logout 更新expires_time失败', '']);
        }
        return $resData;
    }
}