<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

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
    public function getGuardianError($hospital = 0)
    {
        if (empty($hospital)) {
            $where = "e.notice_flag = 0";
        } else {
            $where = "regist_hospital_id = '$hospital'";
        }
        $sql = "select h.hospital_name, p.patient_name, e.guardian_id, e.create_time, content, e.notice_flag, t.report_hospital
                from guardian_error as e inner join guardian as g on e.guardian_id = g.guardian_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                left join hospital_tree as t on g.regist_hospital_id = t.hospital_id
                where $where";
        return $this->getDataAll($sql);
    }
    
    public function existedStudy($studyInstanceUID) {
        return $this->existData('study', "study_instance_uid = '$studyInstanceUID'");
    }
    public function existedStudyToStart($studyInstanceUID) {
        return $this->existData('study', "study_instance_uid = '$studyInstanceUID' and status = " . STATUS_BIND);
    }
    public function existedStudyToEnd($studyInstanceUID) {
        return $this->existData('study', "study_instance_uid = '$studyInstanceUID' and status = " . STATUS_START);
    }
    public function existedStudyToUnibind($studyInstanceUID) {
        return $this->existData('study', "study_instance_uid = '$studyInstanceUID' and status = " . STATUS_END);
    }
    public function existedStudyToDownload($studyInstanceUID) {
        return $this->existData('study', "study_instance_uid = '$studyInstanceUID' and status >= " . STATUS_UPLOAD);
    }
    public function existedStudyReported($studyInstanceUID) {
        return $this->existData('study', "study_instance_uid = '$studyInstanceUID' and status = " . STATUS_REPORT);
    }
    
    public function existedBind($recordNo) {
        return $this->existData('study', "record_no = '$recordNo' and status < " . STATUS_UNBIND);
    }
    public function bind($patientId, $patientName, $patientSex, $patientBirthday, $patientAge, $patientAgeUnit,
            $patientTel, $emergencyContact, $emergencyContactTel, $outpatientNo, $inpatientNo, $insuranceType,
            $caseNo, $studyId, $patientIDCard, $patientStature, $patientWeight, $patientAddress, $createBy, $createDate, 
            $recordNo, $recordModel, $leadType, $powerFrequencyFilter, $validTime, $company, $department, $hospitalName, 
            $hospitalAreaName, $registerDate, $registerUser, $formatNo, $recordTpe, 
            $studyInstancdUID, $studyDate, $accessionNumber, $operatingPhysician, $studyInformatin, $studyId, $studyDepartment, 
            $bingqu, $fanghao, $chuanghao, $shoujianyanyin, $linchuangzhenduan, $laiyuan, $invoiceNo, $shenqingmudi, 
            $seriesDate, $seriesTime, $studyDept, $studyRoom, $jianchayishi, $shenqingyishi, $studyHospitalName, 
            $appHospitalName, $studyHospitalAreaName, $departmentContact, $departmentContactTel, $bindTime)
    {
        $this->pdo->beginTransaction();
        if ($this->existData('patient', "patient_id = '$patientId'")) {
            $sql = "update patient set patient_name = '$patientName', patient_sex = '$patientSex', patient_birthday = '$patientBirthday', 
                    patient_age = '$patientAge', patient_age_unit = '$patientAgeUnit', patient_tel = '$patientTel', 
                    emergency_contact = '$emergencyContact', emergency_contact_tel = '$emergencyContactTel', outpatient_no = '$outpatientNo', 
                    inpatient_no = '$inpatientNo', insurance_type = '$insuranceType', case_no = '$caseNo', study_id = '$studyId', 
                    patient_id_card = '$patientIDCard', patient_stature = '$patientStature', patient_weight = '$patientWeight', 
                    patient_address = '$patientAddress', create_by = '$createBy', create_date = '$createDate'
                    where patient_id = '$patientId'";
            $ret = $this->updateData($sql);
        } else {
            $sql = "insert into patient (patient_id, patient_name, patient_sex, patient_birthday, patient_age, patient_age_unit, 
                    patient_tel, emergency_contact, emergency_contact_tel, outpatient_no, inpatient_no, insurance_type, 
                    case_no, study_id, patient_id_card, patient_stature, patient_weight, patient_address, create_by, create_date) 
                    values ('$patientId', '$patientName', '$patientSex', '$patientBirthday', '$patientAge', '$patientAgeUnit', '$patientTel', 
                    '$emergencyContact', '$emergencyContactTel', '$outpatientNo', '$inpatientNo', '$insuranceType', '$caseNo', 
                    '$studyId', '$patientIDCard', '$patientStature', '$patientWeight', '$patientAddress', '$createBy', '$createDate')";
            $ret = $this->insertData($sql);
        }
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        if ($this->existData('record', "record_no = '$recordNo'")) {
            $sql = "update record set record_model = '$recordModel', lead_type = '$leadType', power_frequency_filter = '$powerFrequencyFilter',
                    valid_time = '$validTime', company = '$company', department = '$department', hospital_name = '$hospitalName', 
                    hospital_area_name = '$hospitalAreaName', register_date = '$registerDate', register_user = '$registerUser', 
                    format_no = '$formatNo', record_type = '$recordTpe' where record_no = '$recordNo'";
            $ret = $this->updateData($sql);
        } else {
            $sql = "insert into record (record_no, record_model, lead_type, power_frequency_filter, valid_time, company, 
                    department, hospital_name, hospital_area_name, register_date, register_user, format_no, record_type) 
                    values ('$recordNo', '$recordModel', '$leadType', '$powerFrequencyFilter', '$validTime', '$company', 
                    '$department', '$hospitalName', '$hospitalAreaName', '$registerDate', '$registerUser', '$formatNo', '$recordTpe')";
            $ret = $this->insertData($sql);
        }
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        if ($this->existData('study', "study_instance_uid = '$studyInstancdUID'")) {
            //not happen
        } else {
            $sql = "insert into study (study_instance_uid, patient_id, study_date, accession_number, operating_physician, 
                    study_information, study_id, department, bingqu, fanghao, chuanghao, shoujianyanyin, linchuangzhenduan, 
                    laiyuan, invoice_no, shenqingmudi, series_date, series_time, study_dept, study_room, jianchayishi, shenqingyishi, 
                    hospital_name, app_hospital_name, hospital_area_name, department_contact, department_contact_tel, record_no, build_time) 
                    values ('$studyInstancdUID', '$patientId', '$studyDate', '$accessionNumber', '$operatingPhysician', '$studyInformatin', 
                    '$studyId', '$studyDepartment', '$bingqu', '$fanghao', '$chuanghao', '$shoujianyanyin', '$linchuangzhenduan', 
                    '$laiyuan', '$invoiceNo', '$shenqingmudi', '$seriesDate', '$seriesTime', '$studyDept', '$studyRoom', '$jianchayishi', 
                    '$shenqingyishi', '$studyHospitalName', '$appHospitalName', '$studyHospitalAreaName', '$departmentContact', 
                    '$departmentContactTel', '$recordNo', '$bindTime')";
            $ret = $this->insertData($sql);
        }
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return $ret;
    }
    
    public function start($id, $time)
    {
        $sql = 'update study set status = \'' . STATUS_START . "', start_time = '$time' where study_instance_uid = '$id'";
        return $this->updateData($sql);
    }
    public function stop($id, $time)
    {
        $sql = 'update study set status = \'' . STATUS_END . "', end_time = '$time' where study_instance_uid = '$id'";
        return $this->updateData($sql);
    }
    public function unbind($id)
    {
        $sql = 'update study set status = "' . STATUS_UNBIND . '" where study_instance_uid = "' . $id . '"';
        return $this->updateData($sql);
    }
    public function upload($id)
    {
        $sql = 'update study set status = "' . STATUS_UPLOAD . '" where study_instance_uid = "' . $id . '"';
        return $this->updateData($sql);
    }
    public function analysis($id)
    {
        $sql = 'update study set status = "' . STATUS_ANALYSIS . '" where study_instance_uid = "' . $id . '"';
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return $ret;
    }
    public function report($id, $doctor, $zhenduan, $jielun)
    {
        $this->pdo->beginTransaction();
        if ($this->existData('report', "study_instance_uid = '$id'")) {
            $sql = "update report set report_physician = '$doctor', report_date = now(), 
                    refer_physician = '$doctor', refer_date = now(), 
                    linchuangzhenduan = '$zhenduan', jianchajielun = '$jielun' 
                    where study_instance_uid = '$id'";
            $ret = $this->updateData($sql);
        } else {
            $sql = "insert into report (study_instance_uid, report_physician, refer_physician, linchuangzhenduan, jianchajielun)
            values ('$id', '$doctor', '$doctor', '$zhenduan', '$jielun')";
            $ret = $this->insertData($sql);
        }
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'update study set status = "' . STATUS_REPORT . '" where study_instance_uid = "' . $id . '"';
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return $ret;
    }
    public function backOutReport($id)
    {
        $sql = 'update study set status = "' . STATUS_BACK_OUT . '" where study_instance_uid = "' . $id . '"';
        return $this->updateData($sql);
    }
    
    public function getPatient($id)
    {
        $sql = "select p.*, s.status 
                from patient as p inner join study as s on p.patient_id = s.patient_id 
                where s.study_instance_uid = '$id'";
        return $this->getDataRow($sql);
    }
    public function getStudyReported($id)
    {
        $sql = "select p.patient_name, p.patient_sex, p.patient_age, p.patient_birthday, 
                s.study_instance_uid, s.bingqu, s.chuanghao, p.inpatient_no, p.outpatient_no, 
                s.hospital_name, s.shenqingyishi, s.department, s.start_time, s.end_time, 
                s.jianchayishi, r.report_date, r.report_physician, r.refer_date, r.refer_physician, 
                r.linchuangzhenduan, r.jianchajielun
                from study as s inner join patient as p on s.patient_id = p.patient_id 
                left join report as r on s.study_instance_uid = r.study_instance_uid
                where s.study_instance_uid = '$id' limit 1";
        return $this->getDataRow($sql);
    }
}
