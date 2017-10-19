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
    
    public function existedHospital($hospital)
    {
        return $this->existData('hospital', ['hospital_id' => $hospital]);
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
    
    public function getDiagnosisByPatient($patientId)
    {
        $sql = 'select distinct diagnosis_id, create_time from patient_diagnosis where patient_id = :id';
        $param = [':id' => $patientId];
        return $this->getDataAll($sql, $param);
    }
    
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, parent_flag, sms_tel
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalByPatient($guardianId)
    {
        $sql = 'select regist_hospital_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
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
    public function getHospitalConfig($guardianId)
    {
        $sql = 'select regist_hospital_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        $hospitalId = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId) {
            return VALUE_DB_ERROR;
        }
        
        $sql = 'select t.hospital_id, analysis_hospital, report_hospital, title_hospital, 
                h.hospital_name as title_hospital_name, h.comment, h.display_check, double_title as `double`
                from hospital_tree as t inner join hospital as h on title_hospital = h.hospital_id
                where t.hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        $hospitalConfig = $this->getDataRow($sql, $param);
        if (VALUE_DB_ERROR === $hospitalConfig) {
            return VALUE_DB_ERROR;
        }
        
        if (empty($hospitalConfig)) {
            $hospitalInfo = $this->getHospitalInfo($hospitalId);
            if (VALUE_DB_ERROR === $hospitalInfo) {
                return VALUE_DB_ERROR;
            }
            $hospitalConfig = ['hospital_id' => $hospitalId, 'analysis_hospital' => $hospitalId,  'report_hospital' => $hospitalId, 
                            'title_hospital' => $hospitalId, 'title_hospital_name' => $hospitalInfo['hospital_name'], 'double' => '0'];
        }
        
        return $hospitalConfig;
    }
    public function getHospitalConfigAll()
    {
        $sql = 'select * from hospital_tree';
        return $this->getDataAll($sql);
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
        $sql = "select distinct pd.patient_id, p.patient_name, p.birth_year, p.sex, p.tel
                from patient as p inner join patient_diagnosis as pd on p.patient_id = pd.patient_id
                where pd.diagnosis_id in $diagnosisList order by pd.patient_id desc";
        return $this->getDataAll($sql);
    }
    public function getPatientLastHospital($patientId, $startTime, $endTime)
    {
        $sql = 'select h.hospital_name, g.regist_time
                from guardian as g inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where patient_id = :id ';
        if (!empty($startTime)) {
            $sql .= " and g.regist_time >= '$startTime' ";
        }
        if (!empty($endTime)) {
            $sql .= " and g.regist_time <= '$endTime' ";
        }
        $sql .= ' order by guardian_id desc limit 1;';
        $param = array(':id' => $patientId);
        return $this->getDataRow($sql, $param);
    }
    
    public function getHospitals($hospitalId)
    {
        $sql = 'select distinct hospital_id from hospital_tree where report_hospital = :hospital';
        $param = array(':hospital' => $hospitalId);
        return $this->getDataAll($sql, $param);
    }
    
    public function getPatientsNeedFollow()
    {
        $sql = 'select d.guardian_id as patient_id, p.patient_name, h.hospital_id, h.hospital_name, 
                upload_time, download_end_time as download_time, d.moved_hospital,
                case d.status when 2 then "已上传" when 3 then "已下载" else "" end as status
                from guardian_data as d inner join guardian as g on d.guardian_id = g.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where d.status in (2, 3) and d.upload_time >= SUBDATE(now(),INTERVAL 7 DAY)
                order by upload_time';
        return $this->getDataAll($sql);
    }
    
    public function getPatients($hospitalIdList, $currentHospital, $offset, $rows, $patientName = null, 
            $startTime = null, $endTime = null, $status = null, $hbiDoctor = null, $reportDoctor = null)
    {
        $sql = "select h.hospital_id, h.hospital_name, h.tel as hospital_tel,
                d.status, a1.real_name as hbi_doctor, a2.real_name as report_doctor, a3.real_name as download_doctor, 
                g.guardian_id as patient_id, start_time, end_time, g.device_id, sickroom, hospitalization_id, 
                regist_doctor_name as doctor_name,
                patient_name as name, birth_year, sex, p.tel, d.upload_time, d.report_time, d.moved_hospital, is_heavy
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                left join hospital as h on g.regist_hospital_id = h.hospital_id
                left join guardian_data as d on g.guardian_id = d.guardian_id
                left join account as a1 on d.hbi_doctor = a1.account_id
                left join account as a2 on d.report_doctor = a2.account_id
                left join account as a3 on d.download_doctor = a3.account_id
                where (regist_hospital_id in ($hospitalIdList) or d.moved_hospital = $currentHospital) ";
        if (null !== $patientName) {
            $sql .= " and patient_name = '$patientName' ";
        }
        if (null !== $startTime) {
            $sql .= " and start_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and start_time <= '$endTime' ";
        }
        if (null !== $status) {
            $sql .= " and d.status in ($status) ";
        }
        if (null !== $hbiDoctor) {
            $sql .= " and a1.real_name = '$hbiDoctor'";
        }
        if (null !== $reportDoctor) {
            $sql .= " and a2.real_name = '$reportDoctor'";
        }
        $sql .= " order by g.guardian_id desc limit $offset, $rows";
        return $this->getDataAll($sql);
    }
    public function getPatientsByIdForAnalytics($patientIdList)
    {
        $sql = "select h.hospital_name, h.tel as hospital_tel, device_id, guardian_id as patient_id, 
                start_time, end_time, blood_pressure, tentative_diagnose, medical_history,
                patient_name as name, birth_year, sex, p.tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 left join hospital as h on g.regist_hospital_id = h.hospital_id
                 where guardian_id in ($patientIdList)";
        return $this->getDataAll($sql);
    }
    public function getPatientsMoved($hospitalId, $offset, $rows, $patientName = null, $startTime = null, $endTime = null,
            $status = null, $hbiDoctor = null, $reportDoctor = null)
    {
        $sql = 'select m1.guardian_id from history_move_data as m1 
                left join history_move_data as m2 on m1.hospital_from = m2.hospital_to and m1.hospital_to = m2.hospital_from
                where m1.hospital_from = :hospital and m2.guardian_id is null order by m1.guardian_id desc';
        $param = [':hospital' => $hospitalId];
        $guardians = $this->getDataAll($sql, $param);
        if (VALUE_DB_ERROR === $guardians) {
            return VALUE_DB_ERROR;
        }
        
        if (empty($guardians)) {
            return array();
        }
        $guardianList = '';
        foreach ($guardians as $guardian) {
            $guardianList .= $guardian['guardian_id'] . ',';
        }
        $guardianList = substr($guardianList, 0, -1);
        /*
        $sql = "select h.hospital_id, h.hospital_name, d.status, a1.real_name as hbi_doctor, a2.real_name as report_doctor, a3.real_name as download_doctor,
        g.guardian_id as patient_id, start_time, end_time, reported, g.device_id, sickroom, hospitalization_id,
        patient_name as name, birth_year, sex, p.tel, d.upload_time, d.report_time
        from guardian as g left join patient as p on g.patient_id = p.patient_id
        left join hospital as h on g.regist_hospital_id = h.hospital_id
        left join guardian_data as d on g.guardian_id = d.guardian_id
        left join account as a1 on d.hbi_doctor = a1.account_id
        left join account as a2 on d.report_doctor = a2.account_id
        left join account as a3 on d.download_doctor = a3.account_id
        where g.guardian_id in ($guardianList) ";
        if (null !== $patientName) {
            $sql .= " and patient_name = '$patientName' ";
        }
        if (null !== $startTime) {
            $sql .= " and start_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and start_time <= '$endTime' ";
        }
        if (null !== $status) {
            $sql .= " and d.status in ($status) ";
        }
        if (null !== $hbiDoctor) {
            $sql .= " and a1.real_name = '$hbiDoctor'";
        }
        if (null !== $reportDoctor) {
            $sql .= " and a2.real_name = '$reportDoctor'";
        }
        $sql .= " order by g.guardian_id desc limit $offset, $rows";
        */
        $sql = "select p.patient_id, patient_name, h.move_time
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                left join history_move_data as h on g.guardian_id = h.guardian_id
                where g.guardian_id in ($guardianList) ";
        return $this->getDataAll($sql);
    }
    public function addGuardianData($guardianId, $url, $deviceType = 0)
    {
        $status = $this->getDataStatus($guardianId);
        if ($status == 4 || $status == 5) {
            $set = 'set url = :url, upload_time = now(), device_type = :device';
        } else {
            $set = 'set url = :url, upload_time = now(), device_type = :device, status = 2';
        }
        $sql = "update guardian_data $set where guardian_id = :guardian_id";
        $param = [':guardian_id' => $guardianId, ':url' => $url, ':device' => $deviceType];
        return $this->updateData($sql, $param);
    }
    public function moveData($guardianId, $hospitalFrom, $hospitalTo, $operator)
    {
        $this->pdo->beginTransaction();
        $sql = 'insert into history_move_data(guardian_id, hospital_from, hospital_to, move_operator)
                values (:guardian, :from, :to, :operator)';
        $param = [':guardian' => $guardianId, ':from' => $hospitalFrom, ':to' => $hospitalTo, ':operator' => $operator];
        $hospitalId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'update guardian_data set moved_hospital = :to where guardian_id = :guardian';
        $param = [':guardian' => $guardianId, ':to' => $hospitalTo];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return true;
    }
    public function setHeavy($patientId)
    {
        $sql = "update guardian_data set is_heavy = 1 where guardian_id = :guardian_id";
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function uploadReport($guardianId, $file)
    {
        $sql = 'update guardian set reported = 1, report_file = :file where guardian_id = :guardian';
        $param = [':file' => $file, ':guardian' => $guardianId];
        return $this->updateData($sql, $param);
    }
    
    private function getDataStatus($guardianId)
    {
        $sql = 'select status from guardian_data where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getReportDoctor($guardianId)
    {
        $sql = 'select report_doctor from guardian_data 
                where guardian_id = :guardian and status = 5 limit 1;';
        $param = [':guardian' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function setDataStatus($guardianId, $statusName, $hbiDoctor, $reportDoctor)
    {
        $status = $this->getDataStatus($guardianId);
        if ($statusName == 'hbi') {
            if ($status == 5) {
                $set = 'set hbi_doctor = ' . $hbiDoctor;
            } else {
                $set = 'set status = 4, hbi_doctor = ' . $hbiDoctor;
            }
        } elseif ($statusName == 'report') {
            $set = 'set status = 5, report_time = now(), report_doctor = ' . $reportDoctor;
        } else {
            return VALUE_DB_ERROR;
        }
        
        $sql = "update guardian_data $set where guardian_id = :guardian";
        $param = [':guardian' => $guardianId];
        return $this->updateData($sql, $param);
    }
}
