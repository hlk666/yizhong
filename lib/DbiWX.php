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
    public function getEcgs($guardianId, $readStatus)
    {
        $sql = 'select ecg_id from difference';
        $diffEcgId = $this->getDataString($sql);
        if (VALUE_DB_ERROR === $diffEcgId) {
            return VALUE_DB_ERROR;
        }
        if ($guardianId > $diffEcgId) {
            $table = 'ecg';
        } else {
            $table = 'ecg_history';
        }
    
        $sql = "select ecg_id, alert_flag, create_time, read_status, data_path, mark
        from $table where guardian_id = $guardianId ";
        if ($readStatus != null) {
        $sql .= " and read_status = $readStatus ";
        }
        $sql .= " order by mark desc, ecg_id desc";
        return $this->getDataAll($sql);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, parent_flag, sms_tel, upload_flag
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatientEcg($hospitalId)
    {
        $sql = "select guardian_id, patient_name, regist_time
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                where regist_hospital_id = '$hospitalId' and g.status = 1";
        return $this->getDataAll($sql);
    }
    public function getPatientInfoByGuardian($guardianId)
    {
        $sql = "select p.patient_name, p.sex, p.birth_year, p.tel, p.address
                from patient as p inner join guardian as g on p.patient_id = g.patient_id 
                where g.guardian_id = $guardianId";
        return $this->getDataRow($sql);
    }
    public function updateOpenId($user, $openId)
    {
        $sql = 'update account set open_id = :open_id where login_name = :user';
        $param = [':user' => $user, ':open_id' => $openId];
        return $this->updateData($sql, $param);
    }

}
