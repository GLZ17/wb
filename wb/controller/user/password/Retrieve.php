<?php


namespace controller\user\password;

use controller\user\common\Exclude;
use common\model\Db;
use model\User as mUser;

class Retrieve extends Exclude
{
    public function index()
    {
        $returnStatus = false;
        $filedPassword = mUser::$password;
        $filedUsername = mUser::$username;
        $fieldEmail = mUser::$email;
        $fieldSerial = mUser::$serial;
        $fields = [
            $filedUsername, $fieldEmail,
            $filedPassword, $fieldSerial
        ];
//        访问前置处理
        list($status, $fieldsData) = $this->accessPrepare($fields);
        if (!$status) {
            $returnData = $fieldsData;
            goto end;
        }
//        密码加密
        list($status, $hash) = $this->passwordHash($fieldsData[$filedPassword]);
        if (!$status) {
            $returnData = $hash;
            goto end;
        }
        $userInfo = Db::table(mUser::$tableName)
            ->andWhere($filedUsername, '=', $fieldsData[$filedUsername])
            ->andWhere($fieldEmail, '=', $fieldsData[$fieldEmail])
            ->andWhere($fieldSerial, '=', $fieldsData[$fieldSerial])
            ->select(true);
        if (empty($userInfo)) {
            $returnData = [2501, 'password/retrieve 序列号失效', ''];
            goto end;
        }
        list($status, $forbidData) = $this->workForbidLogin($userInfo);
        if (!$status) {
            $returnData = $forbidData;
            goto end;
        }
        if ($this->isExpires($userInfo[mUser::$serialExpiresTime])) {
            $returnData = [2502, 'password/retrieve 序列号过期', ''];
            goto end;
        }
        $updateData = [
            mUser::$password => $hash,
            mUser::$serialExpiresTime => 0
        ];
        $num = Db::table(mUser::$tableName)
            ->andWhere(mUser::$id, '=', $userInfo[mUser::$id])
            ->update($updateData);
        if (!$num) {
            $returnData = [2503, 'password/retrieve 密码更新失败', ''];
            goto end;
        }
        $returnData = [0, '密码修改成功', ''];
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }
}