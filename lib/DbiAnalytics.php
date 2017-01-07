<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiAnalytics extends BaseDbi
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
    
    public function addPatientDiagnosis($patientId, $diagnosisId)
    {
        $sql = 'select 1 from patient_diagnosis where patient_id = :patient and diagnosis_id = :diagnosis limit 1';
        $param = [':patient' => $patientId, ':diagnosis' => $diagnosisId];
        $ret = $this->getDataRow($sql, $param);
        if (VALUE_DB_ERROR === $ret || !empty($ret)) {
            return;
        }
        
        $sql = 'insert into patient_diagnosis (patient_id, diagnosis_id) values (:patient, :diagnosis)';
        return $this->insertData($sql, $param);
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
    public function getPatient($guardianId)
    {
        $sql = 'select guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 where guardian_id = :guardian_id';
        $param = array(':guardian_id' => $guardianId);
        return $this->getDataRow($sql, $param);
    }
    public function getPatientByDiagnosis($diagnosisList)
    {
        $sql = 'select distinct pd.patient_id, p.patient_name, p.birth_year, p.sex, p.tel
                from patient as p inner join patient_diagnosis as pd on p.patient_id = pd.patient_id
                where pd.diagnosis_id in ' . $diagnosisList;
        return $this->getDataAll($sql);
    }
    
    public function getHospitals($hospitalId)
    {
        $sql = 'select hospital_id from hospital_tree where analysis_hospital = :hospital';
        $param = array(':hospital' => $hospitalId);
        return $this->getDataAll($sql, $param);
    }
    
    public function getPatients($hospitalIdList)
    {
        $sql = "select h.hospital_name, guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, p.tel, reported
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                left join hospital as h on g.regist_hospital_id = h.hospital_id
                where regist_hospital_id in ($hospitalIdList) order by guardian_id desc ";
        return $this->getDataAll($sql);
    }
    public function getPatientsByIdForAnalytics($patientIdList)
    {
        $sql = "select h.hospital_name, guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, p.tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 left join hospital as h on g.regist_hospital_id = h.hospital_id
                 where guardian_id in ($patientIdList)";
        return $this->getDataAll($sql);
    }
    
    public function addGuardianData($guardianId, $url)
    {
        if ($this->existData('guardian_data', 'guardian_id = ' . $guardianId)) {
            $sql = 'update guardian_data set url = :url, upload_time = now() where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId, ':url' => $url];
            return $this->updateData($sql, $param);
        }
        else {
            $sql = 'insert into guardian_data (guardian_id, url) values (:guardian_id, :url)';
            $param = [':guardian_id' => $guardianId, ':url' => $url ];
            return $this->insertData($sql, $param);
        }
    }
    public function uploadReport($guardianId, $file)
    {
        $sql = 'update guardian set reported = 1, report_file = :file where guardian_id = :guardian';
        $param = [':file' => $file, ':guardian' => $guardianId];
        return $this->updateData($sql, $param);
    }
}
