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
    
    //************************* existed methods(public) *************************
    //********************************** start **********************************
    public function existedDeviceHospital($deviceId, $hospitalId)
    {
        return $this->existData('device', " device_id = $deviceId and hospital_id = $hospitalId ");
    }
    public function existedEcg($ecgId)
    {
        return $this->existData('ecg', ' ecg_id = ' . $ecgId);
    }
    public function existedLoginName($loginName)
    {
        return $this->existData('account', 'login_name = "' . $loginName . '"');
    }
    public function existedGuardian($guardianId)
    {
        return $this->existData('guardian', 'guardian_id = ' . $guardianId);
    }
    //************************* existed methods(public) *************************
    //*********************************** end ***********************************
    
    //************************** query methods(public) **************************
    //********************************** start **********************************
    public function getAcount($loginName)
    {
        $sql = 'select account_id, real_name as name, type, password, hospital_id 
                from account where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getAcountById($doctorId)
    {
        $sql = 'select account_id, login_name as user, real_name as name, type, password, hospital_id
                from account where account_id = :account_id limit 1';
        $param = [':account_id' => $doctorId];
        return $this->getDataRow($sql, $param);
    }
    public function getConsultationRequest($hospitalId)
    {
        $sql = 'select consultation_id, h.hospital_name, guardian_id as patient_id, ecg_id, request_message, request_time
                from consultation as c left join hospital as h on c.request_hospital_id = h.hospital_id
                where response_hospital_id = :hospital_id and status = 1';
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationResponse($hospitalId)
    {
        $sql = 'select consultation_id, h.hospital_name, guardian_id as patient_id, ecg_id, response_message, response_time
                from consultation as c left join hospital as h on c.response_hospital_id = h.hospital_id
                where request_hospital_id = :hospital_id and status = 2';
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationById($consultationId)
    {
        $sql = 'select request_hospital_id, response_hospital_id
                from consultation where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId];
        return $this->getDataRow($sql, $param);
    }
    public function getDeviceId($guardianId)
    {
        $sql = 'select device_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getDiagnosisByGuardian($guardianId)
    {
        $sql = 'select d.ecg_id, d.content, d.content_parent, d.create_time as content_time, e.data_path
                from diagnosis as d left join ecg as e on d.ecg_id = e.ecg_id
                where d.guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getDoctorList($hospitalId)
    {
        $sql = 'select account_id as doctor_id, login_name as user, real_name as doctor_name, type
                from account where hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getEcgs($guardianId, $offset, $rows, $readStatus)
    {
        $sql = 'select ecg_id, alert_flag, create_time, read_status, data_path 
                from ecg where guardian_id = :guardian ';
        if ($readStatus != null) {
            $sql .= " and read_status = $readStatus ";
        }
        $sql .= " order by mark desc, ecg_id desc limit $offset, $rows";
        
        $param = [':guardian' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardianByDevice($deviceId)
    {
        $sql = 'select guardian_id, patient_id, status from guardian
                where device_id = :device_id and status < 2 
                order by guardian_id desc limit 1';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardianById($guardianId)
    {
        $sql = 'select patient_id, status, mode, ifnull(guardian_result, \'\') as result 
                from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardians($hospitalId, $offset, $rows, $mode = null, $status = null, 
            $name = null, $tel = null, $sTime = null, $eTime = null)
    {
        $sql = 'select g.guardian_id, g.mode, g.status, g.mark, g.device_id, 
                p.patient_name, p.sex, p.birth_year, p.tel, g.start_time, g.end_time, 
                g.blood_pressure, g.tentative_diagnose, g.medical_history,
                g.lead, h.hospital_name, g.regist_doctor_name as doctor_name, g.sickroom
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                left join hospital as h on g.guard_hospital_id = h.hospital_id
                where g.guard_hospital_id = ' . $hospitalId;
        if ($mode != null) {
            $sql .= " and g.mode = $mode ";
        }
        if ($status != null) {
            $sql .= " and g.status in ($status) ";
        }
        if ($name != null) {
            $sql .= " and p.patient_name = '$name' ";
        }
        if ($tel != null) {
            $sql .= " and p.tel = '$tel' ";
        }
        if ($sTime != null) {
            $sql .= " and g.start_time >= '$sTime' ";
        }
        if ($eTime != null) {
            $sql .= " and g.start_time <= '$eTime' ";
        }
        $sql .= " order by g.guardian_id desc limit $offset, $rows";
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalByGuardian($guardianId)
    {
        $sql = 'select guard_hospital_id from guardian where guardian_id = :guardian limit 1';
        $param = [':guardian' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalChild($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.hospital_id
                where r.parent_hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalParent($hospitalId)
    {
        $sql = 'select h.hospital_id, h.hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.parent_hospital_id
                where r.hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getPatient($patientId)
    {
        $sql = 'select patient_id, patient_name, sex, birth_year, tel, address
                from patient where patient_id = :patient_id limit 1';
        $param = [':patient_id' => $patientId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatientByDevice($deviceId)
    {
        $sql = 'select guardian_id, patient_name, mode
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where device_id = :device_id and status < 2 order by guardian_id desc limit 1';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getRegistInfo($guardianId)
    {
        $sql = 'select g.mode, g.lead, p.patient_name as name, p.birth_year, p.sex, p.tel,
                tentative_diagnose, medical_history, regist_hospital_id, guard_hospital_id,
                device_id, guardian_hours, regist_doctor_name as doctor_name,
                height, weight, blood_pressure, sickroom, family_tel
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    //************************** query methods(public) **************************
    //*********************************** end ***********************************
    
    //************************* execute methods(public) *************************
    //********************************** start **********************************
    public function addAccount($loginName, $realName, $password, $type, $hospitalId, $creator)
    {
        $sql = 'insert into account (login_name, real_name, password, type, hospital_id, creator)
                values (:login_name, :real_name, :password, :type, :hospital_id, :creator)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':password' => $password,
                        ':type' => $type, ':hospital_id' => $hospitalId,':creator' => $creator ];
        return $this->insertData($sql, $param);
    }
    public function delEcg($ecgId)
    {
        $sql = 'delete from ecg where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->deleteData($sql, $param);
    }
    public function editGuardian($guardianId, array $data)
    {
        return $this->updateTableByKey('guardian', 'guardian_id', $guardianId, $data);
    }
    public function editPatient($patientId, array $data)
    {
        return $this->updateTableByKey('patient', 'patient_id', $patientId, $data);
    }
    public function flowConsultationEnd($idList)
    {
        $sql = 'update consultation set status = 0 where consultation_id in ' . $idList;
        return $this->updateData($sql);
    }
    public function flowConsultationReply($consultationId, $result)
    {
        $sql = 'update consultation set status = 2, response_message = :result
                where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId, ':result' => $result];
        return $this->updateData($sql, $param);
    }
    public function flowConsultationApply($guardianId, $requestHospital, $responseHospital, $ecgId, $mesage)
    {
        $sql = 'insert into consultation(guardian_id, ecg_id, request_hospital_id, request_message, response_hospital_id, status)
                values (:guardian, :ecg, :request_hospital_id, :request_message, :response_hospital_id, 1)';
        $param = [
                        ':guardian' => $guardianId,
                        ':ecg' => $ecgId, 
                        ':request_hospital_id' => $requestHospital, 
                        ':request_message' => $mesage,
                        ':response_hospital_id' => $responseHospital
        ];
        return $this->insertData($sql, $param);
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
    public function flowGuardianAddEcg($guardianId, $alertFlag, $dataPath)
    {
        $sql = 'insert into ecg(guardian_id, alert_flag, data_path) values(:guardian, :alert, :path)';
        $param = [':guardian' => $guardianId, ':alert' => $alertFlag, ':path' => $dataPath];
        return $this->insertData($sql, $param);
    }
    public function flowGuardianAddResult($guardianId, $result)
    {
        $sql = 'update guardian set guardian_result = :result, status = 3 where guardian_id = :guardian';
        $param = [':guardian' => $guardianId, ':result' => $result];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianAddUser($patientName, $sex, $age, $tel, $device, $registHospital,
            $guardHospital, $mode, $hours, $lead, $doctor, $sickRoom, $bloodPressure, $height,
            $weight, $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName)
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
                    tentative_diagnose, medical_history, regist_doctor_name)
                    values (:device, :regist_hospital, :guard_hospital, :patient, :mode,
                    :hours, :lead, :doctor_id, 1, :sickroom, :blood_pressure, :height,
                    :weight, :family_tel, :ten_dia, :medical_history, :doctor_name)';
        $param = [':device' => $device, ':regist_hospital' => $registHospital, ':guard_hospital' => $guardHospital,
                        ':patient' => $patientId, ':mode' => $mode, ':hours' => $hours, ':lead' => $lead,
                        ':doctor_id' => $doctor, ':sickroom' => $sickRoom, ':blood_pressure' => $bloodPressure,
                        ':height' => $height, ':weight' => $weight, ':family_tel' => $familyTel,
                        ':ten_dia' => $tentativeDiagnose, ':medical_history' => $medicalHistory,
                        ':doctor_name' => $registDoctorName];
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
            $sql = 'delete from guardian where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId];
            return $this->deleteData($sql, $param);
        }
    }
    public function flowGuardianEndGuard($guardianId)
    {
        $sql = 'update guardian set status = 2, end_time = now() where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianReadEcg($ecgId)
    {
        $sql = 'update ecg set read_status = 1 where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianStartGuard($guardianId)
    {
        $sql = 'update guardian set status = 1, start_time = now() where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function markEcg($ecgId, $mark)
    {
        $sql = 'update ecg set mark = :mark where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId, ':mark' => $mark];
        return $this->updateData($sql, $param);
    }
    public function markPatient($guardianId, $mark)
    {
        $sql = 'update guardian set mark = :mark where guardian_id = :guardian';
        $param = [':guardian' => $guardianId, ':mark' => $mark];
        return $this->updateData($sql, $param);
    }
    public function updatePassword($user, $newPwd)
    {
        $sql = 'update account set password = :pwd where login_name = :user';
        $param = [':user' => $user, ':pwd' => $newPwd];
        return $this->updateData($sql, $param);
    }
    //************************* execute methods(public) *************************
    //*********************************** end ***********************************
}
