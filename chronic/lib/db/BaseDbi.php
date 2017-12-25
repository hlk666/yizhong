<?php
require_once PATH_ROOT . 'lib/util/HpLogger.php';
class BaseDbi
{
    protected $pdo = null;
    protected $logFile = 'db.log';
    protected $server = DB_SERVER;
    protected $db = DB_DataBase;
    protected $user = DB_USER;
    protected $pwd = DB_PASSWORD;
    
    private function __construct() {}
    
    protected function init()
    {
        try {
            $this->pdo = new PDO('mysql:host=' . $this->server . ';dbname=' . $this->db, 
                    $this->user, $this->pwd);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('set names utf8');
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
        }
    }
    
    //must override in child class.
    public static function getDbi()
    {
        return null;
    }
    public function beginTran()
    {
        $this->pdo->beginTransaction();
    }
    
    public function rollBack()
    {
        $this->pdo->rollBack();
    }
    
    public function commit()
    {
        $this->pdo->commit();
    }
    protected function countData($tableName, $where = '')
    {
        try {
            $sql = "select count(*) as count from $tableName where $where";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            HpLogger::write($stmt->queryString, 'sql.log');
            $ret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function deleteData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            HpLogger::write($stmt->queryString, 'sql.log');
            return true;
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function existData($tableName, $where) {
        try {
            $sql = "select 1 from $tableName where 1 ";
            if (is_array($where) && !empty($where)) {
                foreach ($where as $key => $value) {
                    $sql .= " and $key = \"$value\"";
                }
            }
            if (is_string($where) && $where != '') {
                $sql .= ' and ' . $where;
            }
            $sql .= ' limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            HpLogger::write($stmt->queryString, 'sql.log');
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return false;
        }
    }
    protected function getDataAll($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            HpLogger::write($stmt->queryString, 'sql.log');
            $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function getDataRow($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            HpLogger::write($stmt->queryString, 'sql.log');
            $ret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function getDataString($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            HpLogger::write($stmt->queryString, 'sql.log');
            $ret = $stmt->fetchColumn();
            if (null === $ret) {
                return '';
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function insertData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            HpLogger::write($stmt->queryString, 'sql.log');
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function updateData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            HpLogger::write($stmt->queryString, 'sql.log');
            return true;
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function updateTableByKey($table, $keyName, $keyValue, array $data)
    {
        $sql = 'update ' . $table . ' set ';
        foreach ($data as $key => $value) {
            if ($value == 'null') {
                $sql .= $key . ' = ' . $value . ',';
            } else {
                $sql .= $key . ' = \'' . $value . '\',';
            }
        }
        $sql = substr($sql, 0, -1);
        $sql .= " where $keyName = '$keyValue'";
        return $this->updateData($sql);
    }
}