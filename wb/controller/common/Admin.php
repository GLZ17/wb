<?php


namespace controller\common;

use common\model\Db;
use http\Request;

class Admin extends Base
{
    use Request;

    protected static $pageSize = 20;
    protected static $pageIndex = 1;
    protected static $pageSizeName = 'pageSize';
    protected static $pageIndexName = 'pageIndex';
    protected static $totalName = 'total';
    protected static $pageDataName = 'pageData';
    protected static $totalPageName = 'totalPage';

    protected function retrievePageInfo($total)
    {
        $returnStatus = false;
        $pageIndexName = static::$pageIndexName;
        $pageSizeName = static::$pageSizeName;
        list($status, $pageIndex) = $this->retrievePageParameter($pageIndexName, static::$pageIndex);
        if (!$status) {
            $returnData = $pageIndex;
            goto end;
        }
        list($status, $pageSize) = $this->retrievePageParameter($pageSizeName, static::$pageSize);
        if (!$status) {
            $returnData = $pageSize;
            goto end;
        }
        $returnData = $this->combinePageInfo($total, $pageIndex, $pageSize);
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function retrievePageParameter($name, $defValue)
    {
        $returnStatus = true;
        $value = $_GET[$name] ?? $defValue;
        if ($value === $defValue) {
            $returnData = $defValue;
            goto end;
        }
        if (preg_match('/^[\d]+$/', $value)) {
            $returnData = max(1, +$value);
            goto end;
        }
        $returnStatus = false;
        $returnData = [3001, 'page参数错误', ''];
        end:
        return $this->toPair($returnData, $returnStatus);
    }

    private function combinePageInfo($total, $pageIndex, $pageSize)
    {
        $totalPage = 1;
        if ($pageSize >= $total) {
            $pageIndex = 1;
            goto end;
        }
        $totalPage = ceil($total / $pageSize);
        if ($totalPage <= $pageIndex) {
            $pageIndex = $totalPage;
            goto end;
        }
        end:
        return [
            static::$totalPageName => $totalPage,
            static::$totalName => $total,
            static::$pageIndexName => $pageIndex,
            static::$pageSizeName => $pageSize
        ];
    }

    protected function baseRemove($tableName, $idName, $id, $updateData)
    {
        $returnStatus = false;
        $menuInfo = Db::table($tableName)
            ->andWhere($idName, '=', $id)
            ->select(true);
        if (empty($menuInfo)) {
            $returnData = [3002, 'admin 数据无效', ''];
            goto end;
        }
        $num = Db::table($tableName)
            ->andWhere($idName, '=', $id)
            ->update($updateData);
        if (!$num) {
            $returnData = [3003, 'admin 数据删除失败', ''];
            goto end;
        }
        $returnData = [0, 'admin 数据删除成功', ''];
        $returnStatus = true;
        end:
        return $this->toPair($returnData, $returnStatus);
    }
}