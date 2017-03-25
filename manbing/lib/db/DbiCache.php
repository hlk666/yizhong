<?php
require_once PATH_ROOT . 'lib/db/BaseDbi.php';

class DbiCache extends BaseDbi
{
    private static $instance;
    
    protected function __construct()
    {
        $this->init();
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function backupCache($category, $type, $id, $message)
    {
        $sql = 'insert into history_file_cache (category, type, id, message) values (:category, :type, :id, :message)';
        $param = [':category' => $category, ':type' => $type, ':id' => $id, ':message' => $message];
        return $this->insertData($sql, $param);
    }
}
