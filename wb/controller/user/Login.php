<?php


namespace controller\user;

use controller\user\common\Exclude;
use common\model\Db;
use model\User as mUser;

class Login extends Exclude
{
    public function index()
    {
        $returnStatus = false;
        $fields = [mUser::$username, mUser::$password];
//        访问前置处理
        list($status, $fieldsData) = $this->accessPrepare($fields);
        if (!$status) {
            $returnData = $fieldsData;
            goto end;
        }
//        验证登录
        list($status, $userInfo) = $this->login($fieldsData);
        if (!$status) {
            $returnData = $userInfo;
            goto end;
        }
//        更新登录信息
        list($status, $userInfo) = $this->updateLoginInfo($userInfo);
        if (!$status) {
            $returnData = $userInfo;
            goto end;
        }
//        创建token
        list($status, $token) = $this->createToken($userInfo);
        if (!$status) {
            $returnData = $token;
            goto end;
        }
        $returnData = [0, '登录成功', [
            mUser::$tokenName => $token,
            mUser::$username => $userInfo[mUser::$username]
        ]];
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }


    private function login($userData)
    {
        $returnStatus = false;
        $userInfo = Db::table(mUser::$tableName)
            ->andWhere(mUser::$username, '=', $userData[mUser::$username])
            ->select(true);
        if (empty($userInfo)) {
            $returnData = [2101, 'login 用户名不存在', ''];
            goto end;
        }
        if (!$this->isPasswordHash($userData[mUser::$password], $userInfo[mUser::$password])) {
            $returnData = [2102, 'login 密码不正确', ''];
            goto end;
        }
        list($status, $forbidData) = $this->workForbidLogin($userInfo);
        if(!$status){
            $returnData = $forbidData;
            goto end;
        }
        $returnStatus = true;
        $returnData = $forbidData;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function updateLoginInfo($userInfo)
    {
        $returnStatus = false;
        list($status, $accessId) = $this->retrieveAccessId();
        if (!$status) {
            $returnData = $accessId;
            goto end;
        }
        $now = $this->timestamp();
        $updateData = [
            mUser::$loginTime=>$now,
            mUser::$tokenExpiresTime=>$now + mUser::$tokenExpires,
            mUser::$accessId=>$accessId
        ];
        $num = Db::table(mUser::$tableName)
            ->andWhere(mUser::$id, '=', $userInfo[mUser::$id])
            ->update($updateData);
        if (!$num) {
            $returnData = [2105, 'login 更新登录信息失败', ''];
            goto end;
        }
        $returnStatus = true;
        $returnData = array_merge($userInfo, $updateData);
        end:
        return $this->toPair($returnData, $returnStatus);
    }
}