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
    public function addAdvice($patientId, $advice, $doctorId)
    {
        $set = "advice = '$advice'";
        if (!empty($doctorId)) {
            $set .= ", status = 5, report_doctor = $doctorId, report_time = now()";
        }
        $sql = "update guardian_data set $set where guardian_id = $patientId";
        return $this->updateData($sql);
    }
    public function getCheckText($guardianId)
    {
        $sql = "select check_text from guardian_data where guardian_id = $guardianId limit 1";
        return $this->getDataString($sql);
    }
    public function getDiagnosisByPatient($patientId)
    {
        $sql = 'select distinct diagnosis_id, create_time from patient_diagnosis where patient_id = :id';
        $param = [':id' => $patientId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardianForChronic($id)
    {
        $sql = "select doctor_id as patient_id, ifnull(guardian_result, '') as result from guardian where guardian_id = $id limit 1";
        return $this->getDataRow($sql);
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
        
        $sql = 'select t.hospital_id, analysis_hospital, report_hospital, title1 as title_hospital, 
                h1.hospital_name as title_hospital_name, h1.comment, h1.display_check, 
                h1.report_must_check, 0 as `double`, title2, h2.hospital_name as title2_name
                from hospital_tree as t left join hospital as h1 on t.title1 = h1.hospital_id
                left join hospital as h2 on t.title2 = h2.hospital_id
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
                            'title_hospital' => $hospitalId, 'title_hospital_name' => $hospitalInfo['hospital_name'], 'double' => '0',
                            'title2' => '0', 'title2_name' => ''
            ];
        }
        
        return $hospitalConfig;
    }
    public function getHospitalConfigList($hospitals)
    {
        $sql = "select t.hospital_id, analysis_hospital, report_hospital, title1 as title_hospital,
                h1.hospital_name as title_hospital_name, h1.comment, h1.display_check, h1.report_must_check,
                0 as `double`, title2, h2.hospital_name as title2_name
                from hospital_tree as t left join hospital as h1 on t.title1 = h1.hospital_id
                left join hospital as h2 on t.title2 = h2.hospital_id
                where t.hospital_id in ($hospitals)";
        $hospitalConfig = $this->getDataAll($sql);
        if (VALUE_DB_ERROR === $hospitalConfig) {
            return VALUE_DB_ERROR;
        }
        return $hospitalConfig;
    }
    public function getHospitalConfigAll()
    {
        $sql = 'select t.*, h.report_must_check from hospital_tree as t inner join hospital as h on t.hospital_id = h.hospital_id';
        return $this->getDataAll($sql);
    }
    public function getHospitals($hospitalId)
    {
        $sql = 'select distinct hospital_id from hospital_tree where report_hospital = :hospital';
        $param = array(':hospital' => $hospitalId);
        return $this->getDataAll($sql, $param);
    }
    public function getPatient($guardianId)
    {
        $sql = 'select guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 where guardian_id = :guardian_id';
        $param = array(':guardian_id' => $guardianId);
        return $this->getDataRow($sql, $param);
    }
    public function getPatientOneData($guardianId)
    {
        $sql = "select * from guardian_data where guardian_id = '$guardianId'";
        return $this->getDataRow($sql);
    }
    public function getPatientByDiagnosis($diagnosisList)
    {
        $sql = "select distinct pd.patient_id, p.patient_name, p.birth_year, p.sex, p.tel
                from patient as p inner join patient_diagnosis as pd on p.patient_id = pd.patient_id
                where pd.diagnosis_id in $diagnosisList order by pd.patient_id desc";
        return $this->getDataAll($sql);
    }
    public function getPatientLast($deviceId)
    {
        $sql = "select g.guardian_id, g.start_time, p.patient_name 
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                where device_id = $deviceId order by guardian_id desc limit 1";
        return $this->getDataRow($sql);
    }
    
    public function getPatientsNeedFollow()
    {
        $sql = 'select d.guardian_id as patient_id, p.patient_name, h.hospital_id, h.hospital_name, 
                upload_time, download_end_time as download_time, d.moved_hospital,
                case d.status when 2 then "已上传" when 3 then "已下载" when 4 then "已分析" when 6 then "已分配" else "" end as status
                from guardian_data as d inner join guardian as g on d.guardian_id = g.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where d.status in (2, 3, 4, 6) and d.upload_time >= SUBDATE(now(),INTERVAL 7 DAY)
                order by upload_time';
        return $this->getDataAll($sql);
    }
    
    public function getPatients($hospitalIdList, $currentHospital, $offset, $rows, $patientName = null, 
            $startTime = null, $endTime = null, $status = null, $hbiDoctor = null, $reportDoctor = null, $text = null)
    {
        $sql = "select h.hospital_id, h.hospital_name, h.tel as hospital_tel,
                d.status, d.upload_time, d.type, 
                a1.real_name as hbi_doctor, a2.real_name as report_doctor, a3.real_name as download_doctor, 
                g.guardian_id as patient_id, start_time, end_time, g.device_id, sickroom, hospitalization_id, 
                regist_doctor_name as doctor_name, a1.account_id as hbi_doctor_id, a2.account_id as report_doctor_id,
                patient_name as name, birth_year, sex, p.tel, d.upload_time, d.report_time, d.moved_hospital, is_heavy,
                d.advice
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                left join hospital as h on g.regist_hospital_id = h.hospital_id
                left join guardian_data as d on g.guardian_id = d.guardian_id
                left join account as a1 on d.hbi_doctor = a1.account_id
                left join account as a2 on d.report_doctor = a2.account_id
                left join account as a3 on d.download_doctor = a3.account_id
                where (regist_hospital_id in ($hospitalIdList) or d.moved_hospital = $currentHospital) and url <> '' ";
        if (!empty($text)) {
            $sql .= " and g.guardian_id not in ($text) ";
        }
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
        $sql = "select h.hospital_id, h.hospital_name, h.tel as hospital_tel, g.device_id, g.guardian_id as patient_id, 
                g.start_time, g.end_time, blood_pressure, tentative_diagnose, medical_history, guardian_result, 
                patient_name as name, birth_year, sex, p.tel, reported, d.advice
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                left join guardian_data as d on g.guardian_id = d.guardian_id
                left join hospital as h on g.regist_hospital_id = h.hospital_id
                where g.guardian_id in ($patientIdList)";
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
    public function getReportHospitalByPatient($guardianId)
    {
        $sql = "select t.report_hospital
                from hospital_tree as t inner join guardian as g on t.hospital_id = g.regist_hospital_id
                where g.guardian_id = '$guardianId'";
        return $this->getDataString($sql);
    }
    public function getTelContent($hospitalId, $guardianId, $startTime, $endTime)
    {
        $sql = "select t.guardian_id, t.hospital_name, p.patient_name, t.doctor_name, t.content, t.create_time 
                from guardian_tel_content as t inner join guardian as g on t.guardian_id = g.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                where hospital_id = $hospitalId ";
        if (null !== $guardianId) {
            $sql .= " and t.guardian_id = $guardianId ";
        }
        if (null !== $startTime) {
            $sql .= " and t.create_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and t.create_time <= '$endTime' ";
        }
        $sql .= ' order by t.guardian_id desc';
        return $this->getDataAll($sql);
    }
    public function getRecentpatient()
    {
        $sql = 'select guardian_id from guardian_data where is_heavy = 1 and report_time >= DATE_ADD(now(),INTERVAL -2 DAY)';
        return $this->getDataAll($sql);
    }
    public function addGuardianData($guardianId, $url, $deviceType = 0)
    {
        $status = $this->getDataStatus($guardianId);
        if ($status == 4 || $status == 5 || $status == 6 || $status == 8 || $status == 9) {
            $set = 'set url = :url, upload_time = now(), device_type = :device';
        } else {
            $set = 'set url = :url, upload_time = now(), device_type = :device, status = 2';
        }
        $sql = "update guardian_data $set where guardian_id = :guardian_id";
        $param = [':guardian_id' => $guardianId, ':url' => $url, ':device' => $deviceType];
        return $this->updateData($sql, $param);
    }
    public function isQianyi($guardianId)
    {
        $sql = "select 1 from guardian as g inner join hospital_tree as t on g.regist_hospital_id = t.hospital_id
                left join qianyi_data as q on g.guardian_id = q.guardian_id
                where g.regist_hospital_id <> 3 and g.regist_time > '2018-01-01' and t.title1 = 3 
                and g.guardian_id = $guardianId and q.guardian_id is null limit 1";
        $ret = $this->getDataString($sql);
        if (!empty($ret)) {
            return true;
        }
        return false;
    }
    public function saveQianyiData($guardianId)
    {
        $sql = "insert into qianyi_data (guardian_id) values ($guardianId)";
        return $this->insertData($sql);
    }
    public function moveData($guardianId, $hospitalFrom, $hospitalTo, $operator, $type = '0')
    {
        $this->pdo->beginTransaction();
        $sql = "insert into history_move_data(guardian_id, hospital_from, hospital_to, move_operator, type)
                values ('$guardianId', '$hospitalFrom', '$hospitalTo', '$operator', '$type')";
        $hospitalId = $this->insertData($sql);
        if (VALUE_DB_ERROR === $hospitalId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = "update guardian_data set moved_hospital = '$hospitalTo', type = '$type', status = 2, download_doctor = 0
                where guardian_id = '$guardianId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return true;
    }
    public function setCheckText($guardianId, $text)
    {
        $sql = "update guardian_data set check_text = '$text' where guardian_id = $guardianId";
        return $this->updateData($sql);
    }
    public function setHeavy($patientId)
    {
        $sql = "update guardian set reported = 1 where guardian_id = :guardian_id";
        $param = [':guardian_id' => $patientId];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $sql = "update guardian_data set is_heavy = 1 where guardian_id = :guardian_id";
        $param = [':guardian_id' => $patientId];
        return $this->updateData($sql, $param);
    }
    public function setTelContent($guardianId, $hospitalId, $hospitalName, $doctorName, $content)
    {
        $sql = 'insert into guardian_tel_content (guardian_id, hospital_id, hospital_name, doctor_name, content)
                values (:guardian, :hospital_id, :hospital_name, :doctor, :content)';
        $param = [':guardian' => $guardianId, ':hospital_id' => $hospitalId, ':hospital_name' => $hospitalName, 
                        ':doctor' => $doctorName, ':content' => $content];
        return $this->insertData($sql, $param);
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
                $set = 'set status = 4, report_time = now(), hbi_doctor = ' . $hbiDoctor;
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
    public function setPool($guardianId, $status, $user)
    {
        $oldStatus = $this->getDataStatus($guardianId);
        $set = "status = $status";
        if ($oldStatus == 5) {
            $set .= ', report_doctor = 0';
        }
        if ($status == 6) {
            $set .= ', download_doctor = ' . $user;
        }
        
        $sql = "update guardian_data set $set where guardian_id = $guardianId";
        return $this->updateData($sql);
    }
}
