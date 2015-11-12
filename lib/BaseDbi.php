<?php
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'Logger.php';

class BaseDbi
{
    protected $logFile = 'dbLog.txt';
    
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
}