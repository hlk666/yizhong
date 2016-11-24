<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiYlt extends BaseDbi
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
    
    public function getGuardians($identityCard, $name, $tel)
    {
        $sql = 'select distinct patient_name, guardian_id 
                from patient as p inner join guardian as g on p.patient_id = g.patient_id 
                where 1';
        $param = array();
        if (null != $identityCard) {
            $sql .= " and creator = :identity";
            $param[':identity'] = $identityCard;
        }
        if (null != $name) {
            $sql .= " and patient_name = :name";
            $param[':name'] = $name;
        }
        if (null != $tel) {
            $sql .= " and tel = :tel";
            $param[':tel'] = $tel;
        }
        
        return $this->getDataAll($sql, $param);
    }
    public function getEcgs($guardianStr, $startTime, $endTime)
    {
        $sql = "select ecg_id, alert_flag, create_time as alert_time, data_path from ecg where guardian_id in $guardianStr";
        if (null != $startTime) {
            $sql .= " and create_time >= '$startTime' ";
        }
        if (null != $endTime) {
            $sql .= " and create_time <= '$endTime' ";
        }
        
        return $this->getDataAll($sql);
    }
}
