<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiEcgn extends BaseDbi
{
    private static $instance;
    
    protected function __construct()
    {
        $this->db = 'ecgn';
        $this->init();
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function addHospital($name, $tel)
    {
        $sql = "insert into hospital (hospital_name, tel) values ('$name', '$tel')";
        $id = $this->insertData($sql);
        if (VALUE_DB_ERROR === $id) {
            return VALUE_DB_ERROR;
        }
        return $id;
    }
    
    public function apply($name, $sex, $birthYear, $tel, $applyDepartment, $examinationName, $doctor, $examDepartmentId,
            $caseId, $hospitalizationId, $outpatientId, $medicalInsuranceId)
    {
        $sql = "select patient_id from patient where patient_name = '$name' and birth_year = '$birthYear' and tel = '$tel' limit 1";
        $patientId = $this->getDataString($sql);
        if (VALUE_DB_ERROR === $patientId) {
            return VALUE_DB_ERROR;
        }
        $this->pdo->beginTransaction();
        //if patient not existed, add to patient table.
        if ('' == $patientId) {
            $sql = "insert into patient(patient_name, sex, birth_year, tel) values('$name', '$sex', '$birthYear', '$tel')";
            $patientId = $this->insertData($sql);
            if (VALUE_DB_ERROR === $patientId) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        $sql = "insert into examination(apply_department_id, patient_id, exam_name, doctor_id, exam_department_id,
        case_id, hospitalization_id, outpatient_id, medical_insurance)
        values ('$applyDepartment', '$patientId', '$examinationName', '$doctor', '$examDepartmentId',
        '$caseId', '$hospitalizationId', '$outpatientId', '$medicalInsuranceId')";
        $examinationId = $this->insertData($sql);
        if (VALUE_DB_ERROR === $examinationId) {
        $this->pdo->rollBack();
        return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return $examinationId;
    }
    
    public function deleteHospital($id)
    {
        $sql = "delete from hospital where hospital_id = '$id'";
        return $this->deleteData($sql);
    }
    public function deleteExamination($id)
    {
        $sql = "delete from examination where examination_id = '$id'";
        return $this->deleteData($sql);
    }
    
    public function diagnose($examinationId, $doctorId, $text, $value)
    {
        $sql = "update examination set `status` = 5, diagnosis_doctor_id = '$doctorId', diagnosis_time = now(),  
                diagnosis_text = '$text', diagnosis_value = '$value' where examination_id = '$examinationId'";
        return $this->updateData($sql);
    }
    
    public function editExamination($id, array $data)
    {
        return $this->updateTableByKey('examination', 'examination_id', $id, $data);
    }
    public function editHospital($id, array $data)
    {
        return $this->updateTableByKey('hospital', 'hospital_id', $id, $data);
    }
    public function editPatient($id, array $data)
    {
        return $this->updateTableByKey('patient', 'patient_id', $id, $data);
    }
    
    public function examine($examinationId, $doctorId, $path)
    {
        $sql = "update examination set `status` = 4, exam_doctor_id = '$doctorId', exam_time = now(),  exam_path = '$path'
        where examination_id = '$examinationId'";
        return $this->updateData($sql);
    }
    
    public function existedExamination($id)
    {
        return $this->existData('examination', "examination_id = '$id'");
    }
    public function existedHospital($id)
    {
        return $this->existData('hospital', "hospital_id = '$id'");
    }
    public function existedPatient($id)
    {
        return $this->existData('patient', "patient_id = '$id'");
    }
    
    //status:1=apply,2=order,3=register,4=examine,5=report,6=download
    public function getExamination($status, $name, $caseId, $hospitalizationId, $outpatientId, $medicalInsuranceId, $roomId,
            $applyStartTime, $applyEndTime, $orderStartTime, $orderEndTime)
    {
        $sql = "select e.*, p.* from examination as e inner join patient as p on e.patient_id = p.patient_id where `status` = $status";
        if (!empty($name)) {
            $sql .= " and p.patient_name like '%$name%'";
        }
        if (!empty($caseId)) {
            $sql .= " and case_id = '$caseId'";
        }
        if (!empty($hospitalizationId)) {
            $sql .= " and hospitalization_id = '$hospitalizationId'";
        }
        if (!empty($outpatientId)) {
            $sql .= " and outpatient_id = '$outpatientId'";
        }
        if (!empty($medicalInsuranceId)) {
            $sql .= " and medical_insurance = '$medicalInsuranceId'";
        }
        if (!empty($roomId)) {
            $sql .= " and room_id = '$roomId'";
        }
        if (!empty($applyStartTime)) {
            $sql .= " and apply_time >= '$applyStartTime'";
        }
        if (!empty($applyEndTime)) {
            $sql .= " and apply_time <= '$applyEndTime'";
        }
        if (!empty($orderStartTime)) {
            $sql .= " and order_time >= '$orderStartTime'";
        }
        if (!empty($orderEndTime)) {
            $sql .= " and order_time <= '$orderEndTime'";
        }
        return $this->getDataAll($sql);
    }
    public function getExaminationInfo($id)
    {
        $sql = "select e.*, p.*, d.diagnosis_department_id from examination as e 
                inner join patient as p on e.patient_id = p.patient_id 
                inner join department as d on e.exam_department_id = d.department_id
                where e.examination_id = $id";
        return $this->getDataRow($sql);
    }
    public function getHospital($name, $tel)
    {
        $sql = "select * from hospital where 1";
        if (!empty($name)) {
            $sql .= " and hospital_name like '%$name%'";
        }
        if (!empty($tel)) {
            $sql .= " and tel like '%$tel%'";
        }
        return $this->getDataAll($sql);
    }
    
    public function login($departmentId, $loginName, $tel)
    {
        $sql = "select doctor_id, real_name as name, type, password, hospital_id, department_id
                from doctor where department_id = '$departmentId'";
        if (!empty($loginName)) {
            $sql .= " and login_name = '$loginName'";
        }
        if (!empty($tel)) {
            $sql .= " and tel = '$tel'";
        }
        $sql .= ' limit 1';
        return $this->getDataRow($sql);
    }
    
    public function order($examinationId, $orderTime)
    {
        $sql = "update examination set `status` = 2, order_time = '$orderTime' where examination_id = $examinationId";
        return $this->updateData($sql);
    }
    
    public function register($examinationId, $examDepartmentId, $roomId)
    {
        $sql = "update examination set `status` = 3, exam_department_id = '$examDepartmentId', room_id = '$roomId' 
                where examination_id = '$examinationId'";
        return $this->updateData($sql);
    }
}
