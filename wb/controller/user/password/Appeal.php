<?php


namespace controller\user\password;

use controller\user\common\Exclude;
use common\model\Db;
use common\mail\Mail as cMail;
use model\User as mUser;


class Appeal extends Exclude
{
    public function index()
    {
        $returnStatus = false;
        $fields = [mUser::$username, mUser::$email];
//        访问前置处理
        list($status, $fieldsData) = $this->accessPrepare($fields);
        if (!$status) {
            $returnData = $fieldsData;
            goto end;
        }
        $userInfo = Db::table(mUser::$tableName)
            ->andWhere(mUser::$username, '=', $fieldsData[mUser::$username])
            ->andWhere(mUser::$email, '=', $fieldsData[mUser::$email])
            ->select(true);
        if (empty($userInfo)) {
            $returnData = [2301, 'password/appeal 用户名和邮箱不匹配', ''];
            goto end;
        }
        list($status, $forbidData) = $this->workForbidLogin($userInfo);
        if (!$status) {
            $returnData = $forbidData;
            goto end;
        }
        $updateData = [
            mUser::$serial=>$this->createUniqueString(9),
            mUser::$serialExpiresTime=>$this->timestamp() + mUser::$serialExpires
        ];
        $num = Db::table(mUser::$tableName)
            ->andWhere(mUser::$id, '=', $userInfo[mUser::$id])
            ->update($updateData);
        if (!$num) {
            $returnData = [2302, 'password/appeal 更新serial失败', ''];
            goto end;
        }
        if (!cMail::sendSeries($updateData[mUser::$serial])) {
            $returnData = [2303, 'password/appeal 邮件发送失败', ''];
            goto end;
        }
        $returnData = [0, 'serial己发送到邮箱,注意查收', ''];
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }
}