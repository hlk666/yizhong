<?php
require_once PATH_ROOT . 'lib/util/HpLogger.php';
class BaseDbi
{
    protected $pdo = null;
    protected $logFile = 'dbLog.txt';
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
    protected function backupData($table, $bkTable, $keyName, $keyValue)
    {
        try {
            $sql = "insert into $bkTable select *, now() from $table where $keyName = $keyValue";
            $stmt = $this->pdo->prepare($sql);
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
            return true;
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function countData($tableName, $where = '')
    {
        try {
            $sql = "select count(*) as count from $tableName where $where";
            $stmt = $this->pdo->prepare($sql);
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute();
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute();
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
            $ret = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($ret);
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
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
            if (DEBUG_MODE) {
                HpLogger::write($stmt->queryString, 'sql.log');
            }
            $stmt->execute($param);
            return true;
        } catch (Exception $e) {
            HpLogger::write($e->getMessage(), $this->logFile);
            return VALUE_DB_ERROR;
        }
    }
    protected function updateTableByKey($table, $keyName, $keyValue, array $data)
    {
        $sql = "update $table set ";
        foreach ($data as $key => $value) {
            if ($value == 'null') {
                $sql .= $key . ' = ' . $value . ',';
            } else {
                $sql .= $key . ' = "' . $value . '",';
            }
        }
        $sql = substr($sql, 0, -1);
        $sql .= " where $keyName = :key";
        $param = [':key' => $keyValue];
        return $this->updateData($sql, $param);
    }
}