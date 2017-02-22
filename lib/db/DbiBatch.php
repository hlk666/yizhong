<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiBatch extends BaseDbi
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
    
    public function getDiffEcgIdFrom()
    {
        $sql = 'select ecg_id from difference';
        return $this->getDataString($sql);
    }
    public function getDiffEcgIdTo()
    {
        $sql = 'select max(guardian_id) as guardian_id 
                from guardian where regist_time < date_add(now(), interval -30 day);';
        return $this->getDataString($sql);
    }
    public function moveData($tableFrom, $tableTo, $field, $fieldFrom, $fieldTo)
    {
        $this->pdo->beginTransaction();
        
        $sql = "insert into `$tableTo` select * from `$tableFrom` where $field > $fieldFrom and $field $filed <= $fieldTo";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'update difference set ' . $tableFrom . '_id = :idTo';
        $param = [':idTo' => $fieldTo];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = "delete from $tableFrom where $field > $fieldFrom and $field $filed <= $fieldTo";
        $ret = $this->deleteData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return true;
    }
    /*
    public function existedDeviceHospital($deviceId, $hospitalId)
    {
        return $this->existData('device', " device_id = $deviceId and hospital_id = $hospitalId ");
    }
    public function getConsultationRequest($hospitalId, $allFlag, $requestHospital, $startTime, $endTime)
    {
        $sql = 'select consultation_id, h.hospital_name, guardian_id as patient_id, ecg_id, 
                request_message, request_time, response_message, response_time
                from consultation as c left join hospital as h on c.request_hospital_id = h.hospital_id
                where response_hospital_id = :hospital_id ';
        if (null !== $requestHospital) {
            $sql .= ' and request_hospital_id = ' . $requestHospital;
        }
        if (null !== $startTime) {
            $sql .= " and request_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and request_time <= '$endTime' ";
        }
        $sql .= ' order by consultation_id desc ';
        
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }*/
}
