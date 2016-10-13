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
    
    public function countEcgs($guardianId, $readStatus)
    {
        $where = ' guardian_id = ' . $guardianId;
        if ($readStatus != null) {
            $where .= " and read_status = $readStatus ";
        }
        return $this->countData('ecg', $where);
    }
    public function existedConsultation($consultationId)
    {
        return $this->existData('consultation', ['consultation_id' => $consultationId]);
    }
    public function existedUser($user)
    {
        return $this->existData('user', ['login_name' => $user]);
    }
    public function getCaseList($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select case_id, name as case_name, tel from `case` where hospital_id = :hospital order by case_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationApply($hospitalId)
    {
        $sql = 'c.consultation_id, c.case_id, h.hospital_name as apply_hospital_name, c.apply_message, c.apply_time
                from consultation as c inner join hospital as h on c.apply_hospital_id = h.hospital_id
                where reply_hospital_id = :hospital_id order by apply_time desc';
        $param = ['hospital_id' => $hospitalId];
        
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationInfo($consultationId)
    {
        
        $sql = 'select p.name, p.sex, p.birth_year, p.diagnosis as apply_diagnosis,
                c.apply_message, h1.hospital_name as apply_hospital_name, u1.real_name as apply_doctor_name, u1.tel as apply_doctor_tel,
                c.apply_time, h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                c.reply_time, c.diagnosis as reply_diagnosis, c.advice as reply_advice
                from consultation as c inner join `case` as p on c.case_id = p.case_id
                inner join hospital as h1 on c.apply_hospital_id = h1.hospital_id
                inner join user as u1 on c.apply_user_id = u1.user_id
                inner join hospital as h2 on c.reply_hospital_id = h2.hospital_id
                left join user as u2 on c.reply_user_id = u2.user_id
                where consultation_id = :consultation ';
        $sql .= ' order by consultation_id desc ';
        $param = ['consultation' => $consultationId];
        
        return $this->getDataRow($sql, $param);
    }
    public function getConsultationReply($hospitalId)
    {
        $sql = 'c.consultation_id, c.case_id, h.hospital_name as reply_hospital_name, c.diagnosis, c.advice, c.reply_time
                from consultation as c inner join hospital as h on c.reply_hospital_id = h.hospital_id
                where apply_hospital_id = :hospital_id and reply_user_id is not null order by reply_time desc';
        $param = ['hospital_id' => $hospitalId];
        
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, sms_tel
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalList($offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select hospital_id, hospital_name, tel from hospital order by hospital_id ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalParent($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.parent_hospital_id
                where r.hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getUserInfo($loginName)
    {
        $sql = 'select user_id, real_name as user_name, type as user_type, password, hospital_id
                from user where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    
    
    public function addHospital($name, $tel, $address, $messageTel)
    {
        $sql = 'insert into hospital (hospital_name, tel, address, sms_tel)
                values (:name, :tel, :address, :sms_tel)';
        $param = [':name' => $name, ':tel' => $tel, ':address' => $address, ':sms_tel' => $messageTel];
        return $this->insertData($sql, $param);
    }
    
    public function addHospitalRelation($parentHospitalId, $childHospitalId)
    {
        $sql = 'insert into hospital_relation (hospital_id, parent_hospital_id) values (:child, :parent)';
        $param = [':child' => $childHospitalId, ':parent' => $parentHospitalId];
        return $this->insertData($sql, $param);
    }
    
    public function addCase($hospitalId, $name, $sex, $birthYear, $tel, $diagnosis, $info, 
            $imgCBC, $imgMyocardialMarkers, $imgSerumElectrolytes, $imgEchocardiography, $imgEcg, $imgHolter)
    {
        $sql = 'insert into `case` (hospital_id, name, sex, birth_year, tel, diagnosis, info, 
                img_cbc, img_myocardial_markers, img_serum_electrolytes, img_echocardiography, img_ecg, img_holter)
                values (:hospital, :name, :sex, :birth, :tel, :diagnosis, :info, :cbc, :mm, :se, :eg, :e, :h)';
        $param = [':hospital' => $hospitalId, ':name' => $name, ':sex' => $sex, ':birth' => $birthYear, ':tel' => $tel, 
                        ':diagnosis' => $diagnosis, ':info' => $info, ':cbc' => $imgCBC, ':mm' => $imgMyocardialMarkers, 
                        ':se' => $imgSerumElectrolytes, ':eg' => $imgEchocardiography, ':e' => $imgEcg, ':h' => $imgHolter];
        return $this->insertData($sql, $param);
    }
    public function addUser($loginUser, $name, $password, $type, $tel, $hospitalId)
    {
        $sql = 'insert into user (login_name, real_name, password, type, tel, hospital_id)
                values (:login_name, :real_name, :password, :type, :tel, :hospital_id)';
        $param = [':login_name' => $loginUser, ':real_name' => $name, ':password' => $password,
                        ':type' => $type, ':tel' => $tel, ':hospital_id' => $hospitalId];
        return $this->insertData($sql, $param);
    }
    
    public function applyConsultation($caseId, $applyHospitalId, $applyUserId, $applyMessage, $replyHospital)
    {
        $sql = 'insert into consultation (case_id, apply_hospital_id, apply_user_id, apply_message, reply_hospital_id)
                values (:case, :applyHospital, :applyUser, :applyMessage, :replyHospital)';
        $param = [':case' => $caseId, ':applyHospital' => $applyHospitalId, ':applyUser' => $applyUserId, 
                        ':applyMessage' => $applyMessage, ':replyHospital' => $replyHospital];
        return $this->insertData($sql, $param);
    }
    public function replyConsultation($consultationId, $replyUserId, $diagnosis, $advice)
    {
        $sql = 'update consultation set reply_user_id = :user, diagnosis = :diagnosis, advice = :advice, reply_time = now()
                where consultation_id = :consultation';
        $param = [':user' => $replyUserId, ':diagnosis' => $diagnosis, ':advice' => $advice, ':consultation' => $consultationId];
        return $this->updateData($sql, $param);
    }
    
    public function delEcg($ecgId)
    {
        $sql = 'delete from ecg where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->deleteData($sql, $param);
    }
    public function flowGuardianAddDiagnosis($ecgId, $guardianId, $doctorId, $content, $type)
    {
        if ($type == 1) {
            $contentName = 'content';
        } else {
            $contentName = 'content_parent';
        }
        
        if ($this->existData('diagnosis', ' ecg_id = ' . $ecgId)) {
            $sql = "update diagnosis set guardian_id = :guardian, doctor_id = :doctor, $contentName = :content
             where ecg_id = :ecg";
        } else {
            $sql = "insert into diagnosis (ecg_id, guardian_id, doctor_id, $contentName)
                values (:ecg, :guardian, :doctor, :content)";
        }
        
        $param = [':ecg' => $ecgId, ':guardian' => $guardianId, ':doctor' => $doctorId, ':content' => $content];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianAddUser($patientName, $sex, $age, $tel, $device, $registHospital,
            $guardHospital, $mode, $hours, $lead, $doctor, $sickRoom, $bloodPressure, $height,
            $weight, $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName, $hospitalizationId = '0')
    {
        $birthYear = date('Y') - $age;
        $sql = 'select patient_id from patient
                where patient_name = :name and birth_year = :birth and tel = :tel limit 1';
        $param = [':name' => $patientName, ':birth' => $birthYear, ':tel' => $tel];
        $patientId = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $patientId) {
            return VALUE_DB_ERROR;
        }
        $this->pdo->beginTransaction();
        //if patient not existed, add to patient table.
        if ('' == $patientId) {
            $sql = 'insert into patient(patient_name, sex, birth_year, tel, address)
                    values(:name, :sex, :birth, :tel, :address)';
            $param = [':name' => $patientName, ':sex' => $sex, ':birth' => $birthYear,
                            ':tel' => $tel, ':address' => $sickRoom];
            $patientId = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $patientId) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        $sql = 'insert into guardian(device_id, regist_hospital_id, guard_hospital_id,
                    patient_id, mode, guardian_hours, lead, doctor_id, status,
                    sickroom, blood_pressure, height, weight, family_tel,
                    tentative_diagnose, medical_history, regist_doctor_name, hospitalization_id)
                    values (:device, :regist_hospital, :guard_hospital, :patient, :mode,
                    :hours, :lead, :doctor_id, 1, :sickroom, :blood_pressure, :height,
                    :weight, :family_tel, :ten_dia, :medical_history, :doctor_name, :hospitalization_id)';
        $param = [':device' => $device, ':regist_hospital' => $registHospital, ':guard_hospital' => $guardHospital,
                        ':patient' => $patientId, ':mode' => $mode, ':hours' => $hours, ':lead' => $lead,
                        ':doctor_id' => $doctor, ':sickroom' => $sickRoom, ':blood_pressure' => $bloodPressure,
                        ':height' => $height, ':weight' => $weight, ':family_tel' => $familyTel,
                        ':ten_dia' => $tentativeDiagnose, ':medical_history' => $medicalHistory,
                        ':doctor_name' => $registDoctorName, ':hospitalization_id' => $hospitalizationId];
        $guardianId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $guardianId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return $guardianId;
    }
    public function flowGuardianDelete($guardianId)
    {
        $ret = $this->backupData('guardian', 'history_guardian', 'guardian_id', $guardianId);
        if (true !== $ret) {
            return VALUE_DB_ERROR;
        } else {
            $this->pdo->beginTransaction();
            
            $sql = 'delete from guardian where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId];
            $ret = $this->deleteData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
            
            $sql = 'delete from ecg where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId];
            $ret = $this->deleteData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
            $this->pdo->commit();
            return true;
        }
    }
    public function updatePassword($user, $newPwd)
    {
        $sql = 'update account set password = :pwd where login_name = :user';
        $param = [':user' => $user, ':pwd' => $newPwd];
        return $this->updateData($sql, $param);
    }
}
