<?php


namespace controller\admin;

use controller\common\Admin;
use common\model\Db;
use model\Menu as mMenu;
use common\validate\Menu as vMenu;

class Menu extends Admin
{
    protected static $pageSize = 2;

    public function index()
    {
        $returnStatus = false;
        list($status, $pageInfo) = $this->retrievePageInfo($this->retrieveTotalValue());
        if (!$status) {
            $returnData = $pageInfo;
            goto end;
        }
        $pageData = Db::table(mMenu::$tableName)
            ->andWhere(mMenu::$status, '=', mMenu::$enStatus)
            ->limit($pageInfo[static::$pageIndexName], $pageInfo[static::$pageSizeName])
            ->select();
        $returnStatus = true;
        $pageInfo[static::$pageDataName] = $pageData;
        $returnData = [0, 'menu 获取成功', $pageInfo];
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    public function retrieveTotalValue()
    {
        $fieldName = 'count';
        $countData = Db::table(mMenu::$tableName)
            ->field('count(*) as ' . $fieldName)
            ->andWhere(mMenu::$status, '=', mMenu::$enStatus)
            ->select(true);
        return +$countData[$fieldName];
    }

    public function update()
    {

    }

    public function create()
    {
        $fields = [];
        $returnStatus = false;
        list($status, $fieldsData) = $this->retrieveRequestData($fields);
        if (!$status) {
            $returnData = $fieldsData;
            goto end;
        }
        list($status, $validateData) = vMenu::validate($fieldsData);
        if (!$status) {
            $returnData = $validateData;
            goto end;
        }
        list($status, $menuInfo) = $this->insertMenu($fieldsData);
        if (!$status) {
            $returnData = $menuInfo;
            goto end;
        }
        $returnData = [0, 'menu 添加成功', $menuInfo[mMenu::$id]];
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function insertMenu($menuData)
    {
        $returnStatus = false;
        $menuInfo = Db::table(mMenu::$tableName)
            ->andWhere(mMenu::$name, '=', $menuData[mMenu::$name])
            ->select(true);
        if (!empty($menuInfo)) {
            $returnData = [3101, 'menu 名称已被使用', ''];
            goto end;
        }
        $menuData[mMenu::$status] = mMenu::$enStatus;
        $menuId = Db::table(mMenu::$tableName)->insert($menuData);
        if (!$menuId) {
            $returnData = [3102, 'menu 添加失败', ''];
            goto end;
        }
        $menuData[mMenu::$id] = $menuId;
        $returnData = $menuData;
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    public function remove($userInfo, $params)
    {
        return $this->baseRemove(
            mMenu::$tableName,
            mMenu::$id,
            $params[0],
            [mMenu::$status => mMenu::$disStatus],
        );
    }
}