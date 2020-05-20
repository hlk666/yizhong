<?php
class BaseDbi
{
    protected $pdo = null;
    protected $logFile = 'dbLog.txt';
    protected $sqlLog = 'sql.log';
    protected $server = '101.200.174.235';
    protected $db = 'ecg';
    protected $user = 'production';
    protected $pwd = 'YrGGCDL3RKJU6VQd';
    
    private function __construct() {}
    
    protected function init()
    {
        try {
            $this->pdo = new PDO('mysql:host=' . $this->server . ';dbname=' . $this->db, 
                    $this->user, $this->pwd);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('set names utf8');
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
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
            $stmt->execute($param);
            Logger::write($this->sqlLog, $stmt->queryString);
            return true;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    protected function countData($tableName, $where = '')
    {
        try {
            $sql = "select count(*) as count from $tableName where $where";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            Logger::write($this->sqlLog, $stmt->queryString);
            $ret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    protected function deleteData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            Logger::write($this->sqlLog, $stmt->queryString);
            return true;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
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
            Logger::write($this->sqlLog, $stmt->queryString);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage() . ".\r\n$sql\r\nTrace : " . $e->getTraceAsString());
            return false;
        }
    }
    protected function getDataAll($sql, array $param = array(), $isWriteSql = true)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            if ($isWriteSql) {
                Logger::write($this->sqlLog, $stmt->queryString);
            }
            $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage() . ".\r\n$sql\r\nTrace : " . $e->getTraceAsString());
            return VALUE_DB_ERROR;
        }
    }
    protected function getDataRow($sql, array $param = array(), $isWriteSql = true)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            if ($isWriteSql) {
                Logger::write($this->sqlLog, $stmt->queryString);
            }
            $ret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage() . ".\r\n$sql\r\nTrace : " . $e->getTraceAsString());
            return VALUE_DB_ERROR;
        }
    }
    protected function getDataString($sql, array $param = array(), $isWriteSql = true)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            if ($isWriteSql) {
                Logger::write($this->sqlLog, $stmt->queryString);
            }
            $ret = $stmt->fetchColumn();
            if (null === $ret || false === $ret) {
                return '';
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage() . ".\r\n$sql\r\nTrace : " . $e->getTraceAsString());
            return VALUE_DB_ERROR;
        }
    }
    protected function insertData($sql, array $param = array(), $isWriteSql = true)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            if ($isWriteSql) {
                Logger::write($this->sqlLog, $stmt->queryString);
            }
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage() . ".\r\n$sql\r\nTrace : " . $e->getTraceAsString());
            return VALUE_DB_ERROR;
        }
    }
    protected function updateData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            Logger::write($this->sqlLog, $stmt->queryString);
            return true;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage() . ".\r\n$sql\r\nTrace : " . $e->getTraceAsString());
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