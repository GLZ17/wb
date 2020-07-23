<?php


namespace common\model;

use \PDO;

trait Model
{
    private $pdo;
    private $sth;

    final private function connect()
    {
        $driver = 'mysql';
        $host = '127.0.0.1';
        $port = 3306;
        $db_name = 'db1';
        $charset = 'utf8';
        $username = 'root';
        $password = '';
        $dsn = "{$driver}:$host;port={$port};dbname={$db_name};charset=${charset}";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    final private function query($sql, $params = [])
    {
        $this->execute($sql, $params);
        return $this->sth->fetchAll(PDO::FETCH_ASSOC);
    }

    final private function executeLastInsertId($sql, $params = [])
    {
        $this->execute($sql, $params);
        return $this->pdo->lastInsertId();
    }

    final private function executeRowCount($sql, $params = [])
    {
        $this->execute($sql, $params);
        return $this->sth->rowCount();
    }

    final private function execute($sql, $params = [])
    {
        if (empty($this->pdo)) {
            $this->pdo = $this->connect();
        }
        $this->sth = $this->pdo->prepare($sql);
        return $this->sth->execute($params);
    }
}