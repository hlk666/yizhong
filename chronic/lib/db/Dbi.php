<?php
require_once PATH_ROOT . 'lib/db/BaseDbi.php';

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
    
    public function addCase($departmentId, $patientId, $diagnosis, $chiefComplaint, 
            $presentIllness, $pastIllness, $allergies, $smoking, $drinking, $bodyExamination, array $chronicLable)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'insert into `case` (department_id, patient_id, diagnosis, chief_complaint, 
                present_illness, past_illness, allergies, smoking, drinking, body_examination)
                values (:department_id, :patient_id, :diagnosis, :chief_complaint, :present_illness, 
                :past_illness, :allergies, :smoking, :drinking, :body_examination)';
        $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':diagnosis' => $diagnosis,
                        ':chief_complaint' => $chiefComplaint, ':present_illness' => $presentIllness, 
                        ':past_illness' => $pastIllness, ':allergies' => $allergies, ':smoking' => $smoking, 
                        ':drinking' => $drinking, ':body_examination' => $bodyExamination];
        $caseId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $caseId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        foreach ($chronicLable as $chronicId) {
            $sql = 'insert into chronic_patient (chronic_id, patient_id) values (:chronic_id, :patient_id)';
            $param = [':chronic_id' => $chronicId, ':patient_id' => $patientId];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        
        $this->pdo->commit();
        return $caseId;
    }
    public function addChronic($name)
    {
        $sql = 'insert into chronic (name) values (:name)';
        $param = [':name' => $name];
        return $this->insertData($sql, $param);
    }
    public function addChronicPatient($chronicId, $patientId)
    {
        if ($this->existData('chronic_patient', "chronic_id = $chronicId and patient_id = $patientId")) {
            return;
        }
        $sql = 'insert into chronic_patient (chronic_id, patient_id) values (:chronic_id, :patient_id)';
        $param = [':chronic_id' => $chronicId, ':patient_id' => $patientId];
        return $this->insertData($sql, $param);
    }
    public function addHospital($name, $level, $tel, $area, $province, $city, $address)
    {
        $sql = 'insert into hospital (name, level, tel, area, province, city, address)
                values (:name, :level, :tel, :area, :province, :city, :address)';
        $param = [':name' => $name, ':level' => $level, ':tel' => $tel, 
                        ':area' => $area, ':province' => $province, ':city' => $city, ':address' => $address];
        return $this->insertData($sql, $param);
    }
    public function addDoctor($loginName, $realName, $password, $type, $tel, $phone, $departmentId)
    {
        $sql = 'insert into doctor (login_name, real_name, password, type, tel, phone, department_id)
                values (:login_name, :real_name, :password, :type, :tel, :phone, :department_id)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':password' => $password, 
                        ':type' => $type, ':tel' => $tel, ':phone' => $phone, ':department_id' => $departmentId];
        return $this->insertData($sql, $param);
    }
    public function addDepartment($hospitalId, $name, $tel)
    {
        $sql = 'insert into department (hospital_id, name, tel)
                values (:hospial, :name, :tel)';
        $param = [':hospial' => $hospitalId, ':name' => $name,  ':tel' => $tel];
        return $this->insertData($sql, $param);
    }
    public function addOutpatient($departmentId, $patientId, $chiefComplaint, $descption, 
            $medicineHistory, $medicineAdvice, $examination, $examinationList, $diagnosis, $doctorId)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'insert into outpatient (department_id, patient_id, 
                chief_complaint, descption, medicine_history, medicine_advice, examination, diagnosis, doctor_id)
                values (:department_id, :patient_id, 
                :chief_complaint, :descption, :medicine_history, :medicine_advice, :examination, :diagnosis, :doctor_id)';
        $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':chief_complaint' => $chiefComplaint,
                        ':descption' => $descption, ':medicine_history' => $$medicineHistory, ':medicine_advice' => $medicineAdvice, 
                        ':examination' => $examination,  ':diagnosis' => $diagnosis, ':doctor_id' => $doctorId];
        $outpatientId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $outpatientId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        foreach ($examinationList as $exam) {
            $sql = 'insert into examinatin_patient (department_id, patient_id, type, examination_id, examination_value)
                values (:department_id, :patient_id, :type, :examination_id, :examination_value)';
            $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':type' => 'outpatient',
                            ':examination_id' => $exam[0], ':examination_value' => $exam[1]];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        
        $this->pdo->commit();
        return $outpatientId;
    }
    public function addPatient($identityCard, $name, $birthYear, $sex, $tel, $address, 
            $ethnic, $nativePlace, $hospitalization, $familyName, $familyTel, $departmentId)
    {
        $sql = 'insert into patient (identity_card, name, birth_year, sex, tel, address, ethnic, native_place, 
                hospitalization, family_name, family_tel, department1)
                values (:identity_card, :name, :birth_year, :sex, :tel, :address, :ethnic, 
                :native_place, :hospitalization, :family_name, :family_tel, :deparment1)';
        $param = [':identity_card' => $identityCard, ':name' => $name, ':birth_year' => $birthYear,
                        ':sex' => $sex, ':tel' => $tel, ':address' => $address, ':ethnic' => $ethnic, 
                        ':native_place' => $nativePlace, ':hospitalization' => $hospitalization, 
                        ':family_name' => $familyName, ':family_tel' => $familyTel, ':deparment1' => $departmentId];
        return $this->insertData($sql, $param);
    }
    public function deleteCase($caseId)
    {
        $sql = 'delete from `case` where id = :id';
        $param = [':id' => $caseId];
        return $this->deleteData($sql, $param);
    }
    public function deleteChronic($chronicId)
    {
        $sql = 'delete from chronic where chronic_id = :chronic_id';
        $param = [':chronic_id' => $chronicId];
        return $this->deleteData($sql, $param);
    }
    public function deleteChronicPatient($chronicId, $patientId)
    {
        $sql = 'delete from chronic_patient where chronic_id = :chronic_id and patient_id = :patient_id';
        $param = [':chronic_id' => $chronicId, ':patient_id' => $patientId];
        return $this->deleteData($sql, $param);
    }
    public function deleteDepartment($departmentId)
    {
        $sql = 'delete from department where id = :id';
        $param = [':id' => $departmentId];
        return $this->deleteData($sql, $param);
    }
    public function deleteDoctor($doctorId)
    {
        $sql = 'delete from doctor where id = :id';
        $param = [':id' => $doctorId];
        return $this->deleteData($sql, $param);
    }
    public function deleteHospital($hospitalId)
    {
        $sql = 'delete from hospital where id = :id';
        $param = [':id' => $hospitalId];
        return $this->deleteData($sql, $param);
    }
    public function deletePatient($patientId, $departmentId)
    {
        $sql = 'select department1, department2, department3, department_once from patient where id = :id limit 1';
        $param = [':id' => $patientId];
        $ret = $this->getDataRow($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $data = array();
        if ($ret['department1'] == $departmentId) {
            $data['department1'] = '0';
        }
        if ($ret['department2'] == $departmentId) {
            $data['department2'] = '0';
        }
        if ($ret['department3'] == $departmentId) {
            $data['department3'] = '0';
        }
        if ($ret['department_once'] == $departmentId) {
            $data['department_once'] = '0';
        }
        
        return $this->updateTableByKey('patient', 'id', $patientId, $data);
    }
    public function editCase($caseId, array $data)
    {
        return $this->updateTableByKey('`case`', 'id', $caseId, $data);
    }
    public function editChronic($id, $name)
    {
        $sql = 'update chronic set name = :name where id = :id';
        $param = [':id' => $id, ':name' => $name];
        return $this->updateData($sql, $param);
    }
    public function editDepartment($departmentId, array $data)
    {
        return $this->updateTableByKey('department', 'id', $departmentId, $data);
    }
    public function editDoctor($doctorId, array $data)
    {
        return $this->updateTableByKey('doctor', 'id', $doctorId, $data);
    }
    public function editHospital($hospitalId, array $data)
    {
        return $this->updateTableByKey('hospital', 'id', $hospitalId, $data);
    }
    public function editPatient($patientId, array $data)
    {
        return $this->updateTableByKey('patient', 'id', $patientId, $data);
    }
    public function isPatientInDepartment($patientId, $department)
    {
        $where = " id = $patientId and (department1 = $department 
            or department2 = $department or department3 = $department or department_once = $department)";
        return $this->existData('patient', $where);
    }
    public function isCaseInDepartment($caseId, $department)
    {
        $where = " id = $caseId and department_id = $department";
        return $this->existData('`case`', $where);
    }
    public function existedCase($caseId)
    {
        return $this->existData('`case`', ['id' => $caseId]);
    }
    public function existedChronic($chronicId)
    {
        return $this->existData('chronic', ['id' => $chronicId]);
    }
    public function existedChronicByName($chronicName)
    {
        return $this->existData('chronic', ['name' => $chronicName]);
    }
    public function existedDepartment($departmentId)
    {
        return $this->existData('department', ['id' => $departmentId]);
    }
    public function existedDoctor($loginName)
    {
        return $this->existData('doctor', ['login_name' => $loginName]);
    }
    public function existedDoctorById($doctorId)
    {
        return $this->existData('doctor', ['id' => $doctorId]);
    }
    public function existedExamination($examinatinId)
    {
        return $this->existData('examinatin', ['id' => $examinatinId]);
    }
    public function existedHospital($hospitalId)
    {
        return $this->existData('hospital', ['id' => $hospitalId]);
    }
    public function existedPatient($patientId)
    {
        return $this->existData('patient', ['id' => $patientId]);
    }
    public function getCaseList($patientId, $departmentId = null)
    {
        $sql = 'select c.id as case_id, h.id as hospital_id, h.`name` as hospital_name, d.id as department_id,
                d.`name` as department_name, c.diagnosis, c.chief_complaint, c.present_illness, 
                c.past_illness, c.allergies, c.smoking, c.drinking, c.body_examination
                from `case` as c
                inner join department as d on c.department_id = d.id
                inner join hospital as h on d.hospital_id = h.id
                where c.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and c.department_id = $departmentId ";
        }
        $sql .= ' order by c.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql);
    }
    public function getChronicList()
    {
        $sql = 'select id as chronic_id, name as chronic_name from chronic';
        return $this->getDataAll($sql);
    }
    public function getChronicPatient($departmentId)
    {
        $sql = 'select c.id as chronic_id, c.name as chronic_name, p.patient_id, p.patient_name
                from chronic as c 
                left join (select cp.chronic_id, cp.patient_id, p.name as patient_name
                from chronic_patient as cp
                inner join patient as p on cp.patient_id = p.id
                where p.department1 = :dpt or p.department2 = :dpt or p.department3 = :dpt or p.department_once = :dpt) as p
                on c.id = p.chronic_id
                order by c.id, p.patient_id';
        $param = [':dpt' => $departmentId];
        return $this->getDataAll($sql, $param);
    }
    public function getDepartmentList($hospitalId)
    {
        $sql = 'select id as department_id, name as department_name from department where hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getDoctorInfo($loginName)
    {
        $sql = 'select d.id as doctor_id, d.real_name as doctor_name, d.type, d.password,
                d.department_id, dm.name as department_name, dm.hospital_id, h.name as hospital_name
                from doctor as d inner join department as dm on d.department_id = dm.id
                inner join hospital as h on dm.hospital_id = h.id
                where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getDoctorPassword($doctorId)
    {
        $sql = 'select password from doctor where id = :id limit 1';
        $param = [':id' => $doctorId];
        return $this->getDataString($sql, $param);
    }
    public function getDoctorList($departmentId)
    {
        $sql = 'select id as doctor_id, login_name, real_name as doctor_name, type, tel, phone
                from doctor where department_id = :department_id';
        $param = [':department_id' => $departmentId];
        return $this->getDataAll($sql, $param);
    }
    public function getExaminationList($patientId, $departmentId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select h.id as hospital_id, h.`name` as hospital_name, d.id as department_id, d.`name` as department_name, 
                p.id as patient_id, p.name as patient_name, ep.create_time as examination_time, ep.type, 
                e.id as examination_id, e.`name` as examination_name, ep.examination_value
                from examination_patient as ep 
                inner join examination as e on ep.examination_id = e.id
                inner join department as d on ep.department_id = d.id
                inner join hospital as h on d.hospital_id = h.id
                inner join patient as p on ep.patient_id = p.id
                where ep.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and ep.department_id = $departmentId ";
        }
        if ($startTime != null) {
            $sql .= " and ep.create_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and ep.create_time <= '$endTime' ";
        }
        $sql .= ' order by ep.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql);
    }
    public function getHospitalList()
    {
        $sql = 'select id as hospital_id, name as hospital_name, level, tel, area, province, city, address from hospital';
        return $this->getDataAll($sql);
    }
    public function getPatientInfo($patientId)
    {
        $sql = 'select id as patient_id, name as patient_name, identity_card, sex, tel, address, 
                ethnic, native_place, hospitalization, family_name, family_tel from patient where id = :id';
        $param = [':id' => $patientId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatientList($departmentId)
    {
        $sql = 'select id as patient_id, name as patient_name from patient
                where department1 = :dpt or department2 = :dpt or department3 = :dpt or department_once = :dpt';
        $param = [':dpt' => $departmentId];
        return $this->getDataAll($sql, $param);
    }
    public function getRecordList($patientId, $departmentId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select o.id as record_id, h.id as hospital_id, h.`name` as hospital_name, d.id as department_id, 
                d.`name` as department_name, o.create_time as record_time, "outpatient" as type, o.diagnosis as info
                from outpatient as o
                inner join department as d on o.department_id = d.id
                inner join hospital as h on d.hospital_id = h.id
                where o.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and o.department_id = $departmentId ";
        }
        if ($startTime != null) {
            $sql .= " and o.create_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and o.create_time <= '$endTime' ";
        }
        $sql .= ' order by o.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql);
    }
}
