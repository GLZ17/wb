<?php


namespace controller\user\common;

use common\model\Db;
use model\Access as mAccess;
use common\validate\User as vUser;

class Exclude extends Token
{
    public function accessPrepare($fields)
    {
//        限制访问频率
        $returnStatus = false;
        list($status, $accessInfo) = $this->validateFrequency();
        if (!$status) {
            $returnData = $accessInfo;
            goto end;
        }
//        处理token
        if ($this->retrieveToken()) {
            list($returnStatus, $returnData) = $this->workToken();
            goto end;
        }
//        获取指定的key的数据
        list($status, $fieldsData) = $this->retrieveRequestData($fields);
        if (!$status) {
            $returnData = $fieldsData;
            goto end;
        }
//        校验数据
        list($status, $validateData) = vUser::validate($fieldsData);
        if (!$status) {
            $returnData = $validateData;
            goto end;
        }
        $returnData = $validateData;
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function workToken()
    {
        list($status, $userInfo) = $this->validateToken();
        if (!$status) {
            $returnData = $userInfo;
        } else {
            $returnData = [0, '登录成功', ''];
        }
        end:
        return $this->toPair($returnData);
    }

    private function validateFrequency()
    {
        $returnStatus = false;
        list($status, $accessInfo) = $this->retrieveAccessInfo();
        if (!$status) {
            $returnData = $accessInfo;
            goto end;
        }
        list($returnStatus, $returnData) = $this->updateAccessCount($accessInfo);
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    protected function retrieveAccessId()
    {
        list($status, $accessInfo) = $this->retrieveAccessInfo();
        $resData = $status ? $accessInfo[mAccess::$id] : $accessInfo;
        return $this->toPair($resData, $status);
    }

    private function retrieveAccessInfo()
    {
        $returnStatus = false;
        $tableName = mAccess::$tableName;
        $fieldIp = mAccess::$ip;
        $ip = $this->ip();
        $accessInfo = Db::table($tableName)
            ->andWhere($fieldIp, '=', $ip)
            ->select(true);
        if (!empty($accessInfo)) {
            $returnStatus = true;
            $returnData = $accessInfo;
            goto end;
        }
        $accessData = [
            $fieldIp => $ip,
            mAccess::$expiresTime => $this->timestamp() + mAccess::$expires,
            mAccess::$count => mAccess::$defCount
        ];
        $accessId = Db::table($tableName)->insert($accessData);
        if (!$accessId) {
            $returnData = [1201, 'access 添加失败', ''];
            goto end;
        }
        $accessData[mAccess::$id] = $accessId;
        $returnData = $accessData;
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function updateAccessCount($accessInfo)
    {
        $returnStatus = false;
        $filedCount = mAccess::$count;
        $filedExpireTime = mAccess::$expiresTime;
        $count = $accessInfo[$filedCount] + 1;
        $expireTime = $accessInfo[$filedExpireTime];
        if ($this->isExpires($expireTime)) {
            $count = 1;
            $expireTime = $this->timestamp() + mAccess::$expires;
        }
        if ($count > mAccess::$maxCount) {
            $returnData = [1202, 'access 禁止频繁访问接口', ''];
            goto end;
        }
        $num = Db::table(mAccess::$tableName)
            ->andWhere(mAccess::$id, '=', $accessInfo[mAccess::$id])
            ->update([
                $filedCount => $count,
                $filedExpireTime => $expireTime
            ]);
        if (!$num) {
            $returnData = [1203, 'access 更新失败', ''];
            goto end;
        }
        $returnData = $accessInfo;
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }
}