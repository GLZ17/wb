<?php


namespace controller\user\common;

use http\Request;
use model\User as mUser;
use common\model\Db;
use common\utils\Openssl;

class Token
{
    use Request;
    use Openssl;

    protected function retrieveToken()
    {
        return apache_request_headers()[mUser::$tokenName] ?? '';
    }

    protected function validateToken()
    {
        $returnStatus = false;
        $token = $this->retrieveToken();
        if (!$token) {
            $returnData = [1101, 'validate/token 请先登录', ''];
            goto end;
        }
        $tokenData = $this->decodeToArray($this->decrypt($token));
        if (empty($tokenData)) {
            $returnData = [1102, 'validate/token token无效', ''];
            goto end;
        }
        list($returnStatus, $returnData) = $this->validateTokenData($tokenData);
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function validateTokenData($tokenData)
    {
        $returnStatus = false;
        $userInfo = Db::table(mUser::$tableName)
            ->andWhere(mUser::$id, '=', $tokenData[mUser::$id])
            ->andWhere(mUser::$loginTime, '=', $tokenData[mUser::$loginTime])
            ->select(true);
        if (empty($userInfo)) {
            $returnData = [1105, 'validate/token token失效', ''];
            goto end;
        }
        if ($this->isExpires($userInfo[mUser::$tokenExpiresTime])) {
            $returnData = [1106, 'validate/token token过期', ''];
            goto end;
        }
        list($status, $forbidData) = $this->workForbidLogin($userInfo);
        if(!$status){
            $returnData = $forbidData;
            goto end;
        }
        $returnData = $forbidData;
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    protected function workForbidLogin($userInfo)
    {
        $status = $userInfo[mUser::$status] !== mUser::$disStatus;;
        $resData = $status ? $userInfo : [1108, 'validate/token 用户禁止登录', ''];
        return $this->toPair($resData, $status);
    }

    protected function createToken($userInfo)
    {
        $tokenData = [
            mUser::$loginTime => $userInfo[mUser::$loginTime],
            mUser::$id => $userInfo[mUser::$id]
        ];
        $token = $this->encrypt($this->encodeFromArray($tokenData));
        if (empty($token)) {
            $resData = $this->toPair([1121, '创建token失败', '']);
        } else {
            $resData = $this->toPair($token, true);
        }
        return $resData;
    }
}