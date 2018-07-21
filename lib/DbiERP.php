<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiERP extends BaseDbi
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
    
    public function getHospitalInfoList($IdList)
    {
        $sql = "select hospital_id, hospital_name, h.agency_id, a.agency_name, a.agency_tel, h.salesman_id, s.salesman_name
                from hospital as h
                left join agency as a on h.agency_id = a.agency_id
                left join salesman as s on h.salesman_id = s.salesman_id
                where hospital_id in ($IdList)";
        return $this->getDataAll($sql);
    }
    public function getHospitalDevice($hospitalId)
    {
        $sql = "select count(device_id) as quantity from device where hospital_id = $hospitalId";
        return $this->getDataString($sql);
    }
}
