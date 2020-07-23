<?php


namespace common\model;

class Db
{
    use Model;

    protected $tableName;
    protected $field = '*';
    protected $params = [];
    protected $where = [];
    protected $group = '';
    protected $order = [];
    protected $limit = [];

    private $pdo;
    private $sth;

    public static function table($tableName)
    {
        return new Db($tableName);
    }

    private function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function field($field)
    {
        $this->field = $field;
        return $this;
    }

    public function andWhere($field, $operator, $value)
    {
        return $this->where('and', $field, $operator, $value);
    }

    public function orWhere($field, $operator, $value)
    {
        return $this->where('or', $field, $operator, $value);
    }

    private function where($action, $field, $operator, $value)
    {

        if (is_array($value)) {
            $placeholder = '(?' . str_repeat(',?', count($value) - 1) . ')';
            $this->params = array_merge($this->params, $value);
        } else {
            $placeholder = '?';
            array_push($this->params, $value);
        }
        $str = $field . ' ' . $operator . ' ' . $placeholder;
        if (!empty($this->where)) {
            $str = ' ' . $action . ' ' . $str;
        }
        array_push($this->where, $str);
        return $this;
    }

    public function group($field)
    {
        $this->group = $field;
        return $this;
    }

    public function order($field, $isAsc = true)
    {
        $str = $isAsc ? ' asc ' : ' desc ';
        array_push($this->order, $field . $str);
        return $this;
    }

    public function limit($pageIndex, $pageSize)
    {
        $this->limit = [($pageIndex - 1) * $pageSize, $pageSize];
        return $this;
    }

    private function retrieveWhere()
    {
        return ' where ' . implode(' ', $this->where);
    }

    public function select($isOnlyOne = false)
    {
        $sql = 'select ' . $this->field . ' from ' . $this->tableName;
        if (!empty($this->where)) {
            $sql = $sql . $this->retrieveWhere();
        }
        if (!empty($this->group)) {
            $sql = $sql . ' group by ' . $this->group;
        }
        if (!empty($this->order)) {
            $sql = $sql . ' order by ' . implode(', ', $this->order);
        }
        if (!empty($this->limit)) {
            list($start, $number) = $this->limit;
            $sql = $sql . ' limit ' . $start . ', ' . $number;
        }
        $queryData = $this->query($sql, $this->params);
        return $isOnlyOne && !empty($queryData) ? $queryData[0] : $queryData;
    }

    public function insert($data)
    {
        if (empty($data)) {
            return false;
        }
        $fields = array_keys($data);
        $params = array_values($data);
        $sql = 'insert into ' . $this->tableName
            . ' (' . implode(', ', $fields) .
            ') values (?' . str_repeat(',?', count($fields) - 1) . ')';
        return $this->executeLastInsertId($sql, $params);
    }

    public function update($data)
    {
        if (empty($data) || empty($this->where)) {
            return false;
        }
        $fields = array_keys($data);
        $params = array_merge(array_values($data), $this->params);
        $sql = 'update ' . $this->tableName
            . ' set ' . implode('=?, ', $fields) . '=? '
            . $this->retrieveWhere();
        return $this->executeRowCount($sql, $params);
    }

    public function delete()
    {
        $sql = 'delete from ' . $this->tableName;
        if (empty($this->where)) {
            return false;
        }
        $sql = $sql . $this->retrieveWhere();
        return $this->executeRowCount($sql, $this->params);
    }
}


