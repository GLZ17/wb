<?php


namespace controller\user;

use controller\user\common\Exclude;
use common\model\Db;
use model\User as mUser;
use model\Group as mGroup;
use model\Menu as mMenu;
use model\Role as mRole;

class Register extends Exclude
{
    public function index()
    {
        $returnStatus = false;
        $filedPassword = mUser::$password;
        $fields = [mUser::$username, $filedPassword, mUser::$email];
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
        $fieldsData[$filedPassword] = $hash;
//        注册
        list($status, $userInfo) = $this->register($fieldsData);
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
        $returnData = [0, '注册成功', [
            mUser::$tokenName => $token,
            mUser::$username => $userInfo[mUser::$username]
        ]];
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function register($userData)
    {
        if ($this->retrieveTotal(mUser::$tableName, mUser::$id)) {
            $resData = $this->registerDefaultUser($userData);
        } else {
            $resData = $this->registerSuperUser($userData);
        }
        return $resData;
    }

    public function retrieveTotal($tableName, $fieldName)
    {
        $name = 'total';
        return Db::table($tableName)
            ->field('count(' . $fieldName . ') as ' . $name)
            ->select(true)[$name];
    }

    private function registerDefaultUser($userData)
    {
        $returnStatus = false;
        $tableName = mUser::$tableName;
        $userInfo = Db::table($tableName)
            ->andWhere(mUser::$username, '=', $userData[mUser::$username])
            ->select();
        if (!empty($userInfo)) {
            $returnData = [2001, 'register 用户名已存在,请使用其它名字', ''];
            goto end;
        }
        $userInfo = Db::table($tableName)
            ->andWhere(mUser::$email, '=', $userData[mUser::$email])
            ->select();
        if (!empty($userInfo)) {
            $returnData = [2002, 'register 邮箱已使用,请使用其它邮箱', ''];
            goto end;
        }
        list($status, $accessId) = $this->retrieveAccessId();
        if (!$status) {
            $returnData = $accessId;
            goto end;
        }
        $now = $this->timestamp();
        $userData = array_merge($userData, [
            mUser::$status => mUser::$enStatus,
            mUser::$roleId => mUser::$defRoleId,
            mUser::$accessId => $accessId,
            mUser::$serial => $this->createUniqueString(9),
            mUser::$serialExpiresTime => $now + mUser::$serialExpires,
            mUser::$loginTime => $now,
            mUser::$tokenExpiresTime => $now + mUser::$tokenExpires
        ]);
        $userId = Db::table($tableName)->insert($userData);
        if (!$userId) {
            $returnData = [2003, 'register 用户添加失败', ''];
            goto end;
        }
        $userData[mUser::$id] = $userId;
        $returnData = $userData;
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function registerSuperUser($userData)
    {
        $weight = 1;
        $returnStatus = false;
        list($status, $menuInfo) = $this->retrieveMenuInfo($weight);
        if (!$status) {
            $returnData = $menuInfo;
            goto end;
        }
        list($status, $groupInfo) = $this->retrieveGroupInfo($weight, $menuInfo[mMenu::$id]);
        if (!$status) {
            $returnData = $groupInfo;
            goto end;
        }
        list($status, $roleInfo) = $this->retrieveRoleInfo($weight, $groupInfo[mGroup::$id]);
        if (!$status) {
            $returnData = $roleInfo;
            goto end;
        }
        list($returnStatus, $returnData) = $this->insertRow($weight, $groupInfo[mGroup::$id]);
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function retrieveMenuInfo($weight)
    {
        return $this->retrieveInfo(
            mMenu::$tableName,
            mMenu::$name,
            mMenu::$id,
            [
                mMenu::$status => mMenu::$enStatus,
                mMenu::$order_no => mMenu::$defOrderNo,
                mMenu::$weights => $weight,
                mMenu::$path => '/menu',
                mMenu::$name => '菜单管理'
            ]
        );
    }

    private function retrieveGroupInfo($weights, $menuId)
    {
        return $this->retrieveInfo(
            mGroup::$tableName,
            mGroup::$name,
            mGroup::$id,
            [
                mGroup::$status => mGroup::$enStatus,
                mGroup::$order_no => mGroup::$defOrderNo,
                mGroup::$weights => $weights,
                mGroup::$name => '站长',
                mGroup::$menuIds => $menuId
            ]
        );
    }

    private function retrieveRoleInfo($weights, $groupId)
    {
        return $this->retrieveInfo(
            mRole::$tableName,
            mRole::$name,
            mRole::$id,
            [
                mRole::$status => mRole::$enStatus,
                mRole::$order_no => mRole::$defOrderNo,
                mRole::$weights => $weights,
                mRole::$name => '站长',
                mRole::$groupId => $groupId,
                mRole::$apiIds => ''
            ]
        );
    }

    private function retrieveInfo($tableName, $fieldName, $fieldId, $data)
    {
        $returnStatus = false;
        $info = Db::table($tableName)
            ->andWhere($fieldName, '=', $data[$fieldName])
            ->select(true);
        if (!empty($info)) {
            $returnData = $info;
            $returnStatus = true;
            goto end;
        }
        $id = Db::table($tableName)->insert($data);
        if (!$id) {
            $returnData = [2005, 'register 添加' . ltrim($tableName, 'wb_') . '失败', ''];
            goto end;
        }
        $groupData[$fieldId] = $id;
        $returnStatus = true;
        $returnData = $groupData;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function insertRow($tableName, $fieldId, $data, $errorMessage)
    {
        $id = Db::table($tableName)->insert($data);
        if ($id) {
            $data[$fieldId] = $id;
            $resData = $this->toPair($data, true);
        } else {
            $resData = $this->toPair([1101, $errorMessage, '']);
        }
        return $resData;
    }
}