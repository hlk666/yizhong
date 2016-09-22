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
    public function existedDeviceHospital($deviceId, $hospitalId)
    {
        return $this->existData('device', " device_id = $deviceId and hospital_id = $hospitalId ");
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
                where r.hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getDeviceId($guardianId)
    {
        $sql = 'select device_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getAcountById($doctorId)
    {
        $sql = 'select account_id, login_name as user, real_name as name, type, password, hospital_id
                from account where account_id = :account_id limit 1';
        $param = [':account_id' => $doctorId];
        return $this->getDataRow($sql, $param);
    }
    
    public function getConsultationRequest($hospitalId, $allFlag, $requestHospital, $startTime, $endTime)
    {
        $sql = 'select consultation_id, h.hospital_name, guardian_id as patient_id, ecg_id, 
                request_message, request_time, response_message, response_time
                from consultation as c left join hospital as h on c.request_hospital_id = h.hospital_id
                where response_hospital_id = :hospital_id ';
        if (0 == $allFlag) {
            $sql .= ' and status = 1 ';
        }
        if (null !== $requestHospital) {
            $sql .= ' and request_hospital_id = ' . $requestHospital;
        }
        if (null !== $startTime) {
            $sql .= " and request_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and request_time <= '$endTime' ";
        }
        $sql .= ' order by consultation_id desc ';
        
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
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
    public function flowConsultationEnd($idList)
    {
        $sql = 'update consultation set status = 3 where consultation_id in ' . $idList;
        return $this->updateData($sql);
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
