<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiWX extends BaseDbi
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
    
    public function getDoctorByOpenId($openId)
    {
        $sql = 'select account_id as doctor_id, real_name as doctor_name, type, password, hospital_id
                from account where open_id = :open_id limit 1';
        $param = [':open_id' => $openId];
        return $this->getDataRow($sql, $param);
    }
    public function getDoctorByUser($user)
    {
        $sql = 'select account_id as doctor_id, real_name as doctor_name, type, password, hospital_id
                from account where login_name = :user limit 1';
        $param = [':user' => $user];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, parent_flag, sms_tel, upload_flag
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function updateOpenId($user, $openId)
    {
        $sql = 'update account set open_id = :open_id where login_name = :user';
        $param = [':user' => $user, ':open_id' => $openId];
        return $this->updateData($sql, $param);
    }

}
