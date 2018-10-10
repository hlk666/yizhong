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
    
    public function apply($name, $sex, $birthYear, $tel, $department, $examinationName, $doctor,
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
        $sql = "insert into examination(department_id, patient_id, exam_name, doctor_id,
        case_id, hospitalization_id, outpatient_id, medical_insurance)
        values ('$department', '$patientId', '$examinationName', '$doctor',
        '$caseId', '$hospitalizationId', '$outpatientId', '$medicalInsuranceId')";
        $examinationId = $this->insertData($sql);
        if (VALUE_DB_ERROR === $examinationId) {
        $this->pdo->rollBack();
        return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return $examinationId;
    }
    
    //status:1=apply,2=order,3=register,4=examine,5=report,6=download
    public function getExamination($status, $name, $caseId, $hospitalizationId, $outpatientId, $medicalInsuranceId, 
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
        $sql = "update examination set `status` = 2, order_time = now() where examination_id = $examinationId";
        return $this->updateData($sql);
    }
    
    public function register($examinationId, $examDepartmentId, $roomId)
    {
        $sql = "update examination set `status` = 3, exam_department_id = '$examDepartmentId', roor_id = '$roomId' 
                where examination_id = '$examinationId'";
        return $this->updateData($sql);
    }
}
