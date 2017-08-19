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
            if ($this->existData('chronic_patient', "chronic_id = $chronicId and patient_id = $patientId")) {
                continue;
            }
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
    public function addConsultationApply($applyDepartmentId, $patientId, $doctorId, $message, $replyDepartmentId)
    {
        $sql = 'insert into consultation (patient_id, apply_department_id, apply_doctor_id, apply_message, reply_department_id)
                values (:patient_id, :apply_department_id, :apply_doctor_id, :apply_message, :reply_department_id)';
        $param = [':patient_id' => $patientId, ':apply_department_id' => $applyDepartmentId, ':apply_doctor_id' => $doctorId, 
                        ':apply_message' => $message, ':reply_department_id' => $replyDepartmentId];
        return $this->insertData($sql, $param);
    }
    public function addConsultationReply($consultationId, $doctorId, $diagnosis, $advice)
    {
        $sql = 'update consultation 
                set reply_doctor_id = :doctor, diagnosis = :diagnosis, advice = :advice, reply_time = now()
                where id = :id';
        $param = [':id' => $consultationId, ':doctor' => $doctorId, ':diagnosis' => $diagnosis, ':advice' => $advice];
        return $this->updateData($sql, $param);
    }
    public function addDoctor($loginName, $realName, $password, $type, $tel, $phone, $departmentId)
    {
        $sql = 'insert into doctor (login_name, real_name, password, type, tel, phone, department_id)
                values (:login_name, :real_name, :password, :type, :tel, :phone, :department_id)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':password' => $password, 
                        ':type' => $type, ':tel' => $tel, ':phone' => $phone, ':department_id' => $departmentId];
        return $this->insertData($sql, $param);
    }
    public function addDepartment($hospitalId, $name, $tel, $loginName, $realName, $password)
    {
        $sql = 'select name from hospital where id = :id limit 1';
        $param = [':id' => $hospitalId];
        $hospitalName = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $hospitalName) {
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->beginTransaction();
        
        $sql = 'insert into department (hospital_id, name, tel)
                values (:hospial, :name, :tel)';
        $param = [':hospial' => $hospitalId, ':name' => $hospitalName . $name,  ':tel' => $tel];
        $departmentId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $departmentId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'insert into doctor (login_name, real_name, password, type, tel, phone, department_id)
                values (:login_name, :real_name, :password, :type, :tel, :phone, :department_id)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':password' => $password,
                        ':type' => '2', ':tel' => '', ':phone' => '', ':department_id' => $departmentId];
        $ret = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return $departmentId;
    }
    public function addFollowRecord($departmentId, $patientId, $planId, $recordText, 
            $examination, $examinationList, $diagnosis, $doctorId)
    {
        $this->pdo->beginTransaction();
    
        $sql = 'insert into follow_record (department_id, patient_id, plan_id, record_text, examination, diagnosis, doctor_id)
                values (:department_id, :patient_id, :plan_id, :record_text, :examination, :diagnosis, :doctor_id)';
        $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':plan_id' => $planId, ':record_text' => $recordText, 
                        ':examination' => $examination,  ':diagnosis' => $diagnosis, ':doctor_id' => $doctorId];
        $followRecordId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $followRecordId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'update plan set execute_time = now() where id = :id';
        $param = [':id' => $planId];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
    
        foreach ($examinationList as $exam) {
            $sql = 'insert into examination_patient (department_id, patient_id, record_id, type, examination_id, examination_value)
                values (:department_id, :patient_id, :record_id, :type, :examination_id, :examination_value)';
            $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':record_id' => $followRecordId, 
                            ':type' => 'follow', ':examination_id' => $exam[0], ':examination_value' => $exam[1]];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
    
        $this->pdo->commit();
        return $followRecordId;
    }
    public function addHospital($name, $level, $tel, $area, $province, $city, $address, $loginName, $realName, $password)
    {
        $this->pdo->beginTransaction();
        $sql = 'insert into hospital (name, level, tel, area, province, city, address)
                values (:name, :level, :tel, :area, :province, :city, :address)';
        $param = [':name' => $name, ':level' => $level, ':tel' => $tel,
                        ':area' => $area, ':province' => $province, ':city' => $city, ':address' => $address];
        $hospitalId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'insert into doctor (login_name, real_name, password, type, tel, phone, department_id)
                values (:login_name, :real_name, :password, :type, :tel, :phone, :department_id)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':password' => $password,
                        ':type' => '1', ':tel' => '', ':phone' => '', ':department_id' => $hospitalId];
        $ret = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return $hospitalId;
    }
    public function addOutpatient($departmentId, $patientId, $chiefComplaint, $description, 
            $medicineHistory, $medicineAdvice, $examination, $examinationList, $diagnosis, $doctorId)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'insert into outpatient (department_id, patient_id, 
                chief_complaint, description, medicine_history, medicine_advice, examination, diagnosis, doctor_id)
                values (:department_id, :patient_id, 
                :chief_complaint, :description, :medicine_history, :medicine_advice, :examination, :diagnosis, :doctor_id)';
        $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':chief_complaint' => $chiefComplaint,
                        ':description' => $description, ':medicine_history' => $medicineHistory, ':medicine_advice' => $medicineAdvice, 
                        ':examination' => $examination,  ':diagnosis' => $diagnosis, ':doctor_id' => $doctorId];
        $outpatientId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $outpatientId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        foreach ($examinationList as $exam) {
            $sql = 'insert into examination_patient (department_id, patient_id, record_id, type, examination_id, examination_value)
                values (:department_id, :patient_id, :record_id, :type, :examination_id, :examination_value)';
            $param = [':department_id' => $departmentId, ':patient_id' => $patientId,  ':record_id' => $outpatientId,
                            ':type' => 'outpatient', ':examination_id' => $exam[0], ':examination_value' => $exam[1]];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        
        $this->pdo->commit();
        return $outpatientId;
    }
    public function addFollowPlan($departmentId, $patientId, $planText, $planList, $doctorId, $name)
    {
        $this->pdo->beginTransaction();
    
        $sql = 'insert into follow_plan (department_id, patient_id, plan_text, doctor_id, name)
                values (:department_id, :patient_id, :plan_text, :doctor_id, :name)';
        $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':plan_text' => $planText, 
                        ':doctor_id' => $doctorId, ':name' => $name];
        $followPlanId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $followPlanId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
    
        foreach ($planList as $plan) {
            $sql = 'insert into plan (department_id, patient_id, follow_plan_id, plan_time, plan_value)
                values (:department_id, :patient_id, :follow_plan_id, :plan_time, :plan_value)';
            $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':follow_plan_id' => $followPlanId,
                            ':plan_time' => $plan[0], ':plan_value' => $plan[1]];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
    
        $this->pdo->commit();
        return $followPlanId;
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
    public function addReferralApply($applyDepartmentId, $patientId, $doctorId, $message, $replyDepartmentId)
    {
        $sql = 'insert into referral (patient_id, apply_department_id, apply_doctor_id, apply_message, reply_department_id, status)
                values (:patient_id, :apply_department_id, :apply_doctor_id, :apply_message, :reply_department_id, 1)';
        $param = [':patient_id' => $patientId, ':apply_department_id' => $applyDepartmentId, ':apply_doctor_id' => $doctorId,
                        ':apply_message' => $message, ':reply_department_id' => $replyDepartmentId];
        return $this->insertData($sql, $param);
    }
    public function addReferralReply($referralId, $doctorId, $message)
    {
        $sql = 'update referral set reply_doctor_id = :doctor, reply_time = now(), reply_message = :message
                where id = :id';
        $param = [':id' => $referralId, ':doctor' => $doctorId, ':message' => $message];
        return $this->updateData($sql, $param);
    }
    public function addReferralConfirm($referralId, $doctorId)
    {
        $sql = 'update referral set confirm_doctor_id = :doctor, confirm_time = now() where id = :id';
        $param = [':id' => $referralId, ':doctor' => $doctorId];
        return $this->updateData($sql, $param);
    }
    public function addReferralDischarge($departmentId, $patientId, $planText, $planList, $doctorId, $name, 
            $referralId, $doctorId, $diagnosis, $info)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'insert into follow_plan (department_id, patient_id, plan_text, doctor_id, name)
                values (:department_id, :patient_id, :plan_text, :doctor_id, :name)';
        $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':plan_text' => $planText,
                        ':doctor_id' => $doctorId, ':name' => $name];
        $followPlanId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $followPlanId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        foreach ($planList as $plan) {
            $sql = 'insert into plan (department_id, patient_id, follow_plan_id, plan_time, plan_value)
                values (:department_id, :patient_id, :follow_plan_id, :plan_time, :plan_value)';
            $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':follow_plan_id' => $followPlanId,
                            ':plan_time' => $plan[0], ':plan_value' => $plan[1]];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        
        $sql = 'update referral set discharge_doctor_id = :doctor, discharge_time = now(),
                diagnosis = :diagnosis, info = :info, follow_plan_id = :follow_plan_id where id = :id';
        $param = [':id' => $referralId, ':doctor' => $doctorId,
                        ':diagnosis' => $diagnosis, ':info' => $info, ':follow_plan_id' => $followPlanId];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return $ret;
    }
    public function deleteCase($caseId)
    {
        $sql = 'delete from `case` where id = :id';
        $param = [':id' => $caseId];
        return $this->deleteData($sql, $param);
    }
    public function deleteChronic($chronicId)
    {
        $sql = 'delete from chronic where id = :id';
        $param = [':id' => $chronicId];
        return $this->deleteData($sql, $param);
    }
    public function deleteChronicPatient($chronicId, $patientId)
    {
        $sql = 'delete from chronic_patient where chronic_id = :chronic_id and patient_id = :patient_id';
        $param = [':chronic_id' => $chronicId, ':patient_id' => $patientId];
        return $this->deleteData($sql, $param);
    }
    public function deleteConsultationApply($consultationId)
    {
        $sql = 'delete from consultation where id = :id';
        $param = [':id' => $consultationId];
        return $this->deleteData($sql, $param);
    }
    public function deleteConsultationReply($consultationId)
    {
        $sql = 'update consultation set reply_doctor_id = null, diagnosis = null, advice = null, reply_time = null
                where id = :id';
        $param = [':id' => $consultationId];
        return $this->updateData($sql, $param);
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
    public function deleteFollowPlan($followPlanId)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'delete from plan where follow_plan_id = :id';
        $param = [':id' => $followPlanId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'delete from follow_plan where id = :id';
        $param = [':id' => $followPlanId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $this->pdo->commit();
        return true;
    }
    public function deleteFollowRecord($followRecordId)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'select plan_id from follow_record where id = :id';
        $param = [':id' => $followRecordId];
        $planId = $this->getDataString($sql, $param);
        
        if (VALUE_DB_ERROR === $planId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        if (!empty($planId)) {
            $sql = 'update plan set execute_time = null where id = :id';
            $param = [':id' => $planId];
            $ret = $this->deleteData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        
        $sql = 'delete from examination_patient where record_id = :id and type = "follow"';
        $param = [':id' => $followRecordId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
    
        $sql = 'delete from follow_record where id = :id';
        $param = [':id' => $followRecordId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
    
        $this->pdo->commit();
        return true;
    }
    public function deleteHospital($hospitalId)
    {
        $sql = 'delete from hospital where id = :id';
        $param = [':id' => $hospitalId];
        return $this->deleteData($sql, $param);
    }
    public function deletePatient($patientId, $departmentId, $newDepartmentId = '0')
    {
        $sql = 'select department1, department2, department3, department_once from patient where id = :id limit 1';
        $param = [':id' => $patientId];
        $ret = $this->getDataRow($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $data = array();
        if ($ret['department1'] == $departmentId) {
            $data['department1'] = $newDepartmentId;
        }
        if ($ret['department2'] == $departmentId) {
            $data['department2'] = $newDepartmentId;
        }
        if ($ret['department3'] == $departmentId) {
            $data['department3'] = $newDepartmentId;
        }
        if ($ret['department_once'] == $departmentId) {
            $data['department_once'] = $newDepartmentId;
        }
        
        return $this->updateTableByKey('patient', 'id', $patientId, $data);
    }
    public function deleteReferralApply($referralId)
    {
        $sql = 'delete from referral where id = :id';
        $param = [':id' => $referralId];
        return $this->deleteData($sql, $param);
    }
    public function deleteReferralReply($referralId)
    {
        $sql = 'update referral set reply_doctor_id = null, message = nullreply_time = null where id = :id';
        $param = [':id' => $referralId];
        return $this->updateData($sql, $param);
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
    public function editFollowPlan($followPlanId, $planText, $planList, $doctorId, $name)
    {
        $sql = 'select department_id, patient_id from follow_plan where id = :id limit 1';
        $param = [':id' => $followPlanId];
        $followPlan = $this->getDataRow($sql, $param);
        if (VALUE_DB_ERROR === $followPlan) {
            return VALUE_DB_ERROR;
        }
        $departmentId = $followPlan['department_id'];
        $patientId = $followPlan['patient_id'];
        
        $this->pdo->beginTransaction();
        
        $sql = 'update follow_plan set plan_text = :plan_text, doctor_id = :doctor, name = :name where id = :id';
        $param = [':id' => $followPlanId, ':plan_text' => $planText, ':doctor' => $doctorId, ':name' => $name];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'delete from plan where follow_plan_id = :id and notice_time is null';
        $param = [':id' => $followPlanId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        foreach ($planList as $plan) {
            $sql = 'insert into plan (department_id, patient_id, follow_plan_id, plan_time, plan_value)
                values (:department_id, :patient_id, :follow_plan_id, :plan_time, :plan_value)';
            $param = [':department_id' => $departmentId, ':patient_id' => $patientId, ':follow_plan_id' => $followPlanId,
                            ':plan_time' => $plan[0], ':plan_value' => $plan[1]];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        
        $this->pdo->commit();
        return $followPlanId;
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
    public function existedConsultation($consultationId)
    {
        return $this->existData('consultation', ['id' => $consultationId]);
    }
    public function existedConsultationReplied($consultationId)
    {
        return $this->existData('consultation', "id = $consultationId and reply_time is not null");
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
    public function existedExamination($examinationId)
    {
        return $this->existData('examination', ['id' => $examinationId]);
    }
    public function existedFollowPlan($followPlanId)
    {
        return $this->existData('follow_plan', ['id' => $followPlanId]);
    }
    public function existedFollowPlanNoticed($followPlanId)
    {
        return $this->existData('follow_plan', "id = $followPlanId and notice_time is not null");
    }
    public function existedFollowRecord($followRecordId)
    {
        return $this->existData('follow_record', ['id' => $followRecordId]);
    }
    public function existedFollowRecordByPlan($planId)
    {
        return $this->existData('follow_record', ['plan_id' => $planId]);
    }
    public function existedHospital($hospitalId)
    {
        return $this->existData('hospital', ['id' => $hospitalId]);
    }
    public function existedPatient($patientId)
    {
        return $this->existData('patient', ['id' => $patientId]);
    }
    public function existedPlan($planId)
    {
        return $this->existData('plan', ['id' => $planId]);
    }
    public function existedReferral($referralId)
    {
        return $this->existData('referral', ['id' => $referralId]);
    }
    public function existedReferralReplied($referralId)
    {
        return $this->existData('referral', "id = $referralId and reply_time is not null");
    }
    public function getCaseList($patientId, $departmentId = null)
    {
        $sql = 'select c.id as case_id, d.id as department_id, d.`name` as department_name, 
                c.diagnosis, c.chief_complaint, c.present_illness, 
                c.past_illness, c.allergies, c.smoking, c.drinking, c.body_examination
                from `case` as c inner join department as d on c.department_id = d.id
                where c.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and c.department_id = $departmentId ";
        }
        $sql .= ' order by c.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql, $param);
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
    public function getConsultationList($departmentId = null, $patientId = null, $type = 'apply', $startTime = null, $endTime = null)
    {
        $sql = 'select c.id, c.apply_department_id, d1.name as apply_department_name, c.patient_id, p.name as patient_name, 
                apply_message, apply_time, c.apply_doctor_id, dc1.real_name as apply_doctor_name, 
                c.reply_department_id, d2.name as reply_department_name, reply_doctor_id, dc2.real_name as reply_doctor_name, 
                diagnosis, advice, reply_time
                from consultation as c
                inner join department as d1 on c.apply_department_id = d1.id
                inner join department as d2 on c.reply_department_id = d2.id
                inner join patient as p on c.patient_id = p.id
                inner join doctor as dc1 on c.apply_doctor_id = dc1.id
                left join doctor as dc2 on c.reply_doctor_id = dc2.id
                where 1 ';
        if ($departmentId != null) {
            $sql .= ' and c.' . $type . '_department_id = ' . $departmentId;
        }
        if ($patientId != null) {
            $sql .= " and c.patient_id = $patientId ";
        }
        if ($startTime != null) {
            $sql .= ' and c.' . $type . '_time >= "' . $startTime . '" ';
        }
        if ($endTime != null) {
            $sql .= ' and c.' . $type . '_time <= "' . $endTime . '" ';
        }
        $sql .= ' order by c.id desc';
        return $this->getDataAll($sql);
    }
    public function getDepartmentList($hospitalId)
    {
        $sql = 'select id as department_id, name as department_name, tel from department where hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getDoctorInfo($loginName)
    {
        $sql = 'select d.id as doctor_id, d.real_name as doctor_name, d.type, d.password,
                d.department_id, dm.name as department_name, dm.hospital_id, h.name as hospital_name
                from doctor as d left join department as dm on d.department_id = dm.id
                left join hospital as h on dm.hospital_id = h.id
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
        $sql = 'select d.id as department_id, d.`name` as department_name, p.id as patient_id, p.name as patient_name, 
                ep.create_time as examination_time, ep.type, e.id as examination_id, e.`name` as examination_name, ep.examination_value
                from examination_patient as ep 
                inner join examination as e on ep.examination_id = e.id
                inner join department as d on ep.department_id = d.id
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
        return $this->getDataAll($sql, $param);
    }
    public function getFollowPlanList($departmentId = null, $patientId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select f.id, f.department_id, d.`name` as department_name, f.patient_id, p.`name` as patient_name, 
                f.doctor_id, dc.real_name as doctor_name, f.name as plan_name, f.create_time, f.plan_text
                from follow_plan as f 
                inner join department as d on f.department_id = d.id
                inner join patient as p on f.patient_id = p.id
                inner join doctor as dc on f.doctor_id = dc.id
                where 1 ';
        if ($departmentId != null) {
            $sql .= " and f.department_id = $departmentId ";
        }
        if ($patientId != null) {
            $sql .= " and f.patient_id = $patientId ";
        }
        if ($startTime != null) {
            $sql .= " and f.create_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and f.create_time <= '$endTime' ";
        }
        $sql .= ' order by f.id desc';
        return $this->getDataAll($sql);
    }
    public function getFollowPlanPatient($departmentId, $patientId)
    {
        $sql = 'select distinct f.patient_id, p.name as patient_name, f.id as follow_plan_id, f.name as follow_plan_name
                from follow_plan as f inner join patient as p on f.patient_id = p.id
                where 1 ';
        if ($departmentId != null) {
            $sql .= " and f.department_id = $departmentId ";
        }
        if ($patientId != null) {
            $sql .= " and f.patient_id = $patientId ";
        }
        return $this->getDataAll($sql);
    }
    public function getFollowRecordList($departmentId = null, $patientId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select f.id, f.plan_id, f.department_id, d.`name` as department_name, f.patient_id, p.`name` as patient_name, 
                f.doctor_id, dc.real_name as doctor_name, f.create_time, f.record_text, f.examination, f.diagnosis
                from follow_record as f 
                inner join department as d on f.department_id = d.id
                inner join patient as p on f.patient_id = p.id
                inner join doctor as dc on f.doctor_id = dc.id
                where 1 ';
        if ($departmentId != null) {
            $sql .= " and f.department_id = $departmentId ";
        }
        if ($patientId != null) {
            $sql .= " and f.patient_id = $patientId ";
        }
        if ($startTime != null) {
            $sql .= " and f.create_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and f.create_time <= '$endTime' ";
        }
        $sql .= ' order by f.id desc';
        return $this->getDataAll($sql);
    }
    public function getHospitalList()
    {
        $sql = 'select id as hospital_id, name as hospital_name, level, tel, area, province, city, address from hospital';
        return $this->getDataAll($sql);
    }
    public function getPatientDepartment($patientId)
    {
        $sql = 'select department1, department2, department3, department_once from patient where id = :id limit 1';
        $param = [':id' => $patientId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatientInfo($patientId)
    {
        $sql = 'select id as patient_id, name as patient_name, birth_year, identity_card, sex, tel, address, 
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
    public function getRecordInfoConsultation($consultationId)
    {
        $sql = 'select de1.id as department_id, de1.name as department_name,
                c.patient_id, p.name as patient_name, d1.id as doctor_id, d1.real_name as doctor_name, 
                apply_time, apply_message, 
                de2.id as reply_department_id, de2.name as reply_department_name,
                reply_doctor_id, d2.real_name as reply_doctor_name, reply_time, diagnosis, advice
                from consultation as c
                inner join department as de1 on c.apply_department_id = de1.id
                inner join patient as p on c.patient_id = p.id
                inner join doctor as d1 on c.apply_doctor_id = d1.id
                inner join department as de2 on c.reply_department_id = de2.id
                left join doctor as d2 on c.reply_doctor_id = d2.id
                where c.id = :id ';
        $param = [':id' => $consultationId];
        return $this->getDataRow($sql, $param);
    }
    public function getRecordInfoFollow($followRecordId)
    {
        $sql = 'select de.id as department_id, de.name as department_name,
                f.patient_id, p.name as patient_name, doctor_id, d.real_name as doctor_name, 
                pl.plan_time, pl.plan_value, f.create_time as follow_time, record_text, examination, diagnosis
                from follow_record as f
                left join plan as pl on f.plan_id = pl.id
                inner join department as de on f.department_id = de.id
                inner join patient as p on f.patient_id = p.id
                inner join doctor as d on f.doctor_id = d.id
                where f.id = :id ';
        $param = [':id' => $followRecordId];
        return $this->getDataRow($sql, $param);
    }
    public function getRecordInfoOutpatient($outpatientId)
    {
        $sql = 'select de.id as department_id, de.name as department_name,
                o.patient_id, p.name as patient_name, doctor_id, d.real_name as doctor_name, 
                o.create_time as outpatient_time, chief_complaint, description,
                medicine_history, medicine_advice, examination, diagnosis
                from outpatient as o
                inner join department as de on o.department_id = de.id
                inner join patient as p on o.patient_id = p.id
                inner join doctor as d on o.doctor_id = d.id
                where o.id = :id ';
        $param = [':id' => $outpatientId];
        return $this->getDataRow($sql, $param);
    }
    public function getRecordInfoReferral($referralId)
    {
        $sql = 'select de1.id as department_id, de1.name as department_name,
                r.patient_id, p.name as patient_name, d1.id as doctor_id, d1.real_name as doctor_name, 
                apply_time, apply_message, 
                de2.id as reply_department_id, de2.name as reply_department_name,
                reply_doctor_id, d2.real_name as reply_doctor_name, reply_time, reply_message, 
                confirm_time, confirm_doctor_id, d3.real_name as confirm_doctor_name, 
                discharge_time, discharge_doctor_id, d4.real_name as discharge_doctor_name, 
                diagnosis, info, follow_plan_id, fp.name as follow_plan_name, fp.plan_text
                from referral as r
                inner join department as de1 on r.apply_department_id = de1.id
                inner join patient as p on r.patient_id = p.id
                inner join doctor as d1 on r.apply_doctor_id = d1.id
                inner join department as de2 on r.reply_department_id = de2.id
                left join doctor as d2 on r.reply_doctor_id = d2.id
                left join doctor as d3 on r.confirm_doctor_id = d3.id
                left join doctor as d4 on r.discharge_doctor_id = d4.id
                left join follow_plan as fp on r.follow_plan_id = fp.id
                where r.id = :id ';
        $param = [':id' => $referralId];
        return $this->getDataRow($sql, $param);
    }
    public function getRecordListConsultation($patientId, $departmentId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select c.id as record_id, d.id as department_id, d.`name` as department_name, 
                c.apply_time as record_time, "consultation" as type, c.diagnosis as info
                from consultation as c inner join department as d on c.apply_department_id = d.id
                where c.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and c.department_id = $departmentId ";
        }
        if ($startTime != null) {
            $sql .= " and c.apply_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and c.apply_time <= '$endTime' ";
        }
        $sql .= ' order by c.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql, $param);
    }
    public function getRecordListFollow($patientId, $departmentId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select f.id as record_id, d.id as department_id, d.`name` as department_name, 
                f.create_time as record_time, "follow" as type, f.diagnosis as info
                from follow_record as f inner join department as d on f.department_id = d.id
                where f.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and f.department_id = $departmentId ";
        }
        if ($startTime != null) {
            $sql .= " and o.create_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and f.create_time <= '$endTime' ";
        }
        $sql .= ' order by f.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql, $param);
    }
    public function getRecordListOutpatient($patientId, $departmentId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select o.id as record_id, d.id as department_id, d.`name` as department_name, 
                o.create_time as record_time, "outpatient" as type, o.diagnosis as info
                from outpatient as o inner join department as d on o.department_id = d.id
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
        return $this->getDataAll($sql, $param);
    }
    public function getRecordListReferral($patientId, $departmentId = null, $startTime = null, $endTime = null)
    {
        $sql = 'select r.id as record_id, d.id as department_id, d.`name` as department_name, 
                r.apply_time as record_time, "referral" as type, r.diagnosis as info
                from referral as r inner join department as d on r.apply_department_id = d.id
                where r.patient_id = :patient_id ';
        if ($departmentId != null) {
            $sql .= " and r.apply_department_id = $departmentId ";
        }
        if ($startTime != null) {
            $sql .= " and r.apply_time >= '$startTime' ";
        }
        if ($endTime != null) {
            $sql .= " and r.apply_time <= '$endTime' ";
        }
        $sql .= ' order by r.id desc';
        $param = [':patient_id' => $patientId];
        return $this->getDataAll($sql, $param);
    }
    public function getReferralList($departmentId = null, $patientId = null, $type = 'apply', $startTime = null, $endTime = null)
    {
        $sql = 'select r.id, r.status, r.patient_id, p.name as patient_name, 
                r.apply_department_id, d1.name as apply_department_name, apply_doctor_id, dc1.real_name as apply_doctor_name, apply_message, apply_time, 
                r.reply_department_id, d2.name as reply_department_name, reply_doctor_id, dc2.real_name as reply_doctor_name, reply_message, reply_time, 
                confirm_doctor_id, dc3.real_name as confirm_doctor_name, confirm_time, 
                discharge_doctor_id, dc4.real_name as discharge_doctor_name, discharge_time, 
                diagnosis, info, follow_plan_id
                from referral as r
                inner join department as d1 on r.apply_department_id = d1.id
                inner join department as d2 on r.reply_department_id = d2.id
                inner join patient as p on r.patient_id = p.id
                inner join doctor as dc1 on r.apply_doctor_id = dc1.id
                left join doctor as dc2 on r.reply_doctor_id = dc2.id
                left join doctor as dc3 on r.confirm_doctor_id = dc3.id
                left join doctor as dc4 on r.discharge_doctor_id = dc4.id
                where 1 ';
        if ($departmentId != null) {
            $sql .= ' and r.' . $type . '_department_id = ' . $departmentId;
        }
        if ($patientId != null) {
            $sql .= " and r.patient_id = $patientId ";
        }
        
        if ($startTime != null) {
            $sql .= ' and r.' . $type . '_time >= "' . $startTime . '" ';
        }
        if ($endTime != null) {
            $sql .= ' and r.' . $type . '_time <= "' . $endTime . '" ';
        }
        
        $sql .= ' order by r.id desc';
        return $this->getDataAll($sql);
    }
    public function searchDepartment($name = null, $tel = null)
    {
        $sql = 'select id, name, tel, hospital_id from department where 1 ';
        if ($name != null) {
            $sql .= " and name like '%$name%' ";
        }
        if ($tel != null) {
            $sql .= " and tel like '%$tel%' ";
        }
        return $this->getDataAll($sql);
    }
    public function searchHospital($name = null, $level = null, $tel = null, $area = null, $province = null, $city = null, $address = null)
    {
        $sql = 'select id, name, level, tel, area, province, city, address from hospital where 1 ';
        if ($name != null) {
            $sql .= " and name like '%$name%' ";
        }
        if ($level != null) {
            $sql .= " and level like '%$level%' ";
        }
        if ($tel != null) {
            $sql .= " and tel like '%$tel%' ";
        }
        if ($area != null) {
            $sql .= " and area like '%$area%' ";
        }
        if ($province != null) {
            $sql .= " and province like '%$province%' ";
        }
        if ($city != null) {
            $sql .= " and city like '%$city%' ";
        }
        if ($address != null) {
            $sql .= " and address like '%$address%' ";
        }
        return $this->getDataAll($sql);
    }
    public function searchPatient($name = null, $identityCard = null, $birthYear = null, $sex = null, $tel = null, 
            $address = null, $hospitalization = null)
    {
        $sql = 'select id, identity_card, name, birth_year, sex,  tel, address, ethnic, native_place, hospitalization, 
                family_name, family_tel, department1, department2, department3, department_once from patient where 1 ';
        if ($name != null) {
            $sql .= " and name like '%$name%' ";
        }
        if ($identityCard != null) {
            $sql .= " and identity_card like '%$identityCard%' ";
        }
        if ($birthYear != null) {
            $sql .= " and birth_year = $birthYear ";
        }
        if ($sex != null) {
            $sql .= " and sex = '$sex'";
        }
        if ($tel != null) {
            $sql .= " and tel like '%$tel%' ";
        }
        if ($address != null) {
            $sql .= " and address like '%$address%' ";
        }
        if ($hospitalization != null) {
            $sql .= " and hospitalization like '%$hospitalization%' ";
        }
        return $this->getDataAll($sql);
    }
    public function setManageDepartment($patientId, $data)
    {
        return $this->updateTableByKey('patient', 'id', $patientId, $data);
    }
}
