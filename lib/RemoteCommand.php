<?php
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'Dbi.php';

class RemoteCommand
{
    //private static $logFile = 'commandLog.txt';
    private static $error = array();
    const CMD_STATUS_EXIST = 1;
    const CMD_STATUS_NULL = 0;
    
    private static function setError($code, $message)
    {
        self::$error['code'] = $code;
        self::$error['message'] = $message;
    }
    
    public static function getError()
    {
        return json_encode(self::$error);
    }
    
    public static function getSuccess()
    {
        return json_encode(array());
    }
    
    /**
     * @todo add check post. change static to common object.
     */
    public static function validate($param)
    {
        if (!isset($param['id'])) {
            self::setError(1, 'patientId is required.');
            return false;
        }
        
        $id = trim($param['id']);
        if (empty($id)) {
            self::setError(1, 'patientId is required.');
            return false;
        }
        
        return true;
    }
    
    public static function getStatus($patientId)
    {
        $ret = Dbi::getDbi()->getStatus($patientId);
        if ($ret == VALUE_DB_ERROR) {
            self::setDBError();
            return VALUE_COMMON_ERROR;
        }
    }
    
    /**
     * @todo add method in dbi.
     */
    public static function getCommand($patientId)
    {
        $ret = Dbi::getDbi()->getCommand($patientId);
        if ($ret == VALUE_DB_ERROR) {
            self::setDBError();
            return VALUE_COMMON_ERROR;
        }
    }
    
    /**
     * @todo write command log.
     */
    public static function setCommand($patientId, $commands = array())
    {
        $dbi = Dbi::getDbi();
        $rows = $dbi->getStatus($patientId);
        
        if ($rows == VALUE_DB_ERROR) {
            self::setDBError();
            return VALUE_COMMON_ERROR;
        }
        
        if (empty($rows)) {
            $ret = $dbi->addCommand($patientId, self::CMD_STATUS_EXIST);
        } elseif ($rows[0]['status'] == self::CMD_STATUS_EXIST) {
            return;
        } else {
            $ret = $dbi->updateCommand($patientId, self::CMD_STATUS_EXIST);
        }
        
        if ($ret == VALUE_DB_ERROR) {
            self::setDBError();
            return VALUE_COMMON_ERROR;
        }
    }
    
    public static function delCommand($patientId)
    {
        $ret = Dbi::getDbi()->delCommand($patientId);
        if ($ret == VALUE_DB_ERROR) {
            self::setDBError();
            return VALUE_COMMON_ERROR;
        }
    }
    
    private static function setDBError()
    {
        self::setError(2, 'DB error.');
    }
}