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
    public function checkHospitalType($hospitalId)
    {
        return $this->existData(hospital_tree, "report_hospital = $hospitalId");
    }
    public function clearLogin($doctorId)
    {
        $sql = "update account set open_id = '' where account_id = $doctorId";
        return $this->updateData($sql);
    }
    public function getDoctorByOpenId($openId)
    {
        $sql = "select account_id as doctor_id, real_name as doctor_name, type, password, hospital_id
                from account where open_id = '$openId' limit 1";
        return $this->getDataRow($sql);
    }
    public function getDoctorByUser($user)
    {
        $sql = "select account_id as doctor_id, real_name as doctor_name, type, password, hospital_id
                from account where login_name = '$user' limit 1";
        return $this->getDataRow($sql);
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
        $sql = "select hospital_id, hospital_name, address, tel, parent_flag, sms_tel, upload_flag
                from hospital where hospital_id = $hospitalId limit 1";
        return $this->getDataRow($sql);
    }
    public function getPatientReport($hospitalId)
    {
        $sql = "select g.guardian_id, p.patient_name, g.start_time from guardian as g 
                inner join patient as p on g.patient_id = p.patient_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                inner join hospital_tree as t on g.regist_hospital_id = t.hospital_id
                where t.report_hospital = $hospitalId and d.status = 4 and g.start_time > date_add(now(), interval -7 day)";
        return $this->getDataAll($sql);
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
        $sql = "update account set open_id = '$openId' where login_name = '$user'";
        return $this->updateData($sql);
    }

}
