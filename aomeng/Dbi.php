<?php
require_once 'Logger.php';
require_once 'BaseDbi.php';

class Dbi extends BaseDbi
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
    
    public function getHospital($startTime, $endTime)
    {
        $sql = 'select hospital_id, hospital_name, count(id) as count from aomeng where 1 ';
        if (null !== $startTime) {
            $sql .= " and start_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and start_time <= '$endTime' ";
        }
        $sql .= ' group by hospital_id';
        return $this->getDataAll($sql);
    }
    
    public function getPatient($hospitalId, $startTime, $endTime, $offset = 0, $rows = null)
    {
        $sql = 'select id, hospital_name, patient_name, start_time, end_time, device_id, doctor_name from aomeng where hospital_id = :hospital ';
        if (null !== $startTime) {
            $sql .= " and start_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and start_time <= '$endTime' ";
        }
        $sql .= ' order by start_time desc ';
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    
    public function addData($hospitalId, $hospitalName, $patientName, $startTime, $endTime, $deviceId, $doctorName)
    {
        $sql = 'insert into aomeng(hospital_id, hospital_name, patient_name, start_time, end_time, device_id, doctor_name)
                values(:hospital_id, :hospital_name, :patient_name, :start_time, :end_time, :device_id, :doctor_name)';
        $param = [':hospital_id' => $hospitalId, ':hospital_name' => $hospitalName, ':patient_name' => $patientName, 
                        ':start_time' => $startTime, ':end_time' => $endTime, ':device_id' => $deviceId, ':doctor_name' => $doctorName];
        return $this->insertData($sql, $param);
    }
}
