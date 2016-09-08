<?php
require_once PATH_LIB . 'BaseDbi.php';

class DbiMaster extends BaseDbi
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
    
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, parent_flag, sms_tel
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalTree($guardianId)
    {
        $sql = 'select regist_hospital_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        $hospitalId = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId || '' == $hospitalId) {
            return array();
        }
        
        $sql = 'select hospital_id, analysis_hospital, report_hospital from hospital_tree
                where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
}
