<?php
require_once PATH_LIB . 'Logger.php';

class Dbi
{
    private static $instance;
    private $pdo;
    private $logFile = 'dbLog.txt';
    
    /**
     * for speed, not use config file now. we will do it in future.
     */
    private function __construct()
    {
        try {
            $this->pdo = new PDO('mysql:host=101.200.174.235;dbname=test',
                    'yantaiyizhong', 'yantaiyizhong');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('set names utf8');
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
        }
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function beginTran()
    {
        $this->pdo->beginTransaction();
    }
    
    public function rollBack()
    {
        $this->pdo->rollBack();
    }
    
    public function commit()
    {
        $this->pdo->commit();
    }
    
    
    //************************* common methods(private) *************************
    //********************************** start **********************************
    private function deleteData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            return true;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    private function existData($tableName, $where) {
        try {
            $sql = "select 1 from $tableName where 1 ";
            if (is_array($where) && !empty($where)) {
                foreach ($where as $key => $value) {
                    $sql .= " and $key = \"$value\"";
                }
            }
            if (is_string($where) && $where != '') {
                $sql .= ' and ' . $where;
            }
            $sql .= ' limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return false;
        }
    }
    private function getDataAll($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    private function getDataRow($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            $ret = $stmt->fetch(PDO::FETCH_ASSOC);
            if (false === $ret) {
                return array();
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    private function getDataString($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            $ret = $stmt->fetchColumn();
            if (null === $ret) {
                return '';
            } else {
                return $ret;
            }
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    private function insertData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    private function updateData($sql, array $param = array())
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            return true;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    //************************* common methods(private) *************************
    //*********************************** end ***********************************
    
    //************************* existed methods(public) *************************
    //********************************** start **********************************
    public function existedEcgNotRead($guardianId)
    {
        return $this->existData('ecg', ' read_status = 0 and guardian_id = ' . $guardianId);
    }
    public function existedLoginName($loginName)
    {
        return $this->existData('account', 'login_name = "' . $loginName . '"');
    }
    public function existedRequestConsultation($hospital)
    {
        return $this->existData('consultation', 'status = 1 and response_hospital_id = ' . $hospital);
    }
    public function existedResponseConsultation($hospital)
    {
        return $this->existData('consultation', 'status = 2 and request_hospital_id = ' . $hospital);
    }
    //************************* existed methods(public) *************************
    //*********************************** end ***********************************
    
    //************************** query methods(public) **************************
    //********************************** start **********************************
    public function getAcount($loginName)
    {
        $sql = 'select account_id, type, password, hospital_id from account where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getConsultationRequest($hospitalId)
    {
        $sql = 'select consultation_id, c.ecg_id, request_message, request_time, e.guardian_id, h.hospital_name
                from consultation as c left join ecg as e on c.ecg_id = e.ecg_id
                left join hospital as h on c.request_hospital_id = h.hospital_id
                where response_hospital_id = :hospital_id and status = 1';
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationResponse($hospitalId)
    {
        $sql = 'select consultation_id, c.ecg_id, response_message, response_time, e.guardian_id, e.data_path, h.hospital_name
                from consultation as c left join ecg as e on c.ecg_id = e.ecg_id
                left join hospital as h on c.response_hospital_id = h.hospital_id
                where request_hospital_id = :hospital_id and status = 2';
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getDeviceId($guardianId)
    {
        $sql = 'select device_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getDiagnosisByEcg($ecgId)
    {
        $sql = 'select d.ecg_id, a.real_name as doctor_name, d.content, d.create_time
                from diagnosis as d left join account as a on d.doctor_id = a.account_id
                where d.ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->getDataAll($sql, $param);
    }
    public function getDiagnosisByGuardian($guardianId)
    {
        $sql = 'select d.mark, d.diagnosis_id, e.ecg_id, a.real_name as doctor_name, e.data_path, 
                d.content, e.create_time as alert_time, d.create_time as diagnose_time
                from diagnosis as d left join ecg as e on d.ecg_id = e.ecg_id
                left join account as a on d.doctor_id = a.account_id
                where e.guardian_id = :guardian_id order by d.mark desc, d.diagnosis_id desc';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getDoctorInfo($doctorId)
    {
        $sql = 'select account_id as doctor_id, login_name, real_name as doctor_name
                from account where account_id = :acount_id limit 1';
        $param = [':acount_id' => $doctorId];
        return $this->getDataRow($sql, $param);
    }
    public function getDoctorByName($doctorName)
    {
        $sql = 'select account_id as doctor_id from account where real_name = :real_name limit 1';
        $param = [':real_name' => $doctorName];
        return $this->getDataString($sql, $param);
    }
    public function getDoctorList($hospitalId, $offset = 0, $rows = null)
    {
        $sql = 'select account_id, login_name, real_name as doctor_name
                from account where hospital_id = :hospital_id and type = 2';
        if ($rows != null) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getEcg($guardianId, $offset = 0, $rows = null)
    {
        $sql = 'select ecg_id, mark, create_time, read_status, data_path from ecg
                where guardian_id = :guardian order by mark desc, ecg_id desc';
        if ($rows != null) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':guardian' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardianByDevice($deviceId)
    {
        $sql = 'select guardian_id, patient_id, status from guardian
                where device_id = :device_id and mode in (1,2) and status < 2 
                order by guardian_id desc limit 1';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardianById($guardianId)
    {
        $sql = 'select status, mode, guardian_result from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardianList($hospitalId, $offset = 0, $rows = null)
    {
        $sql = 'select g.guardian_id, g.patient_id, g.status, g.start_time, g.end_time,
                p.patient_name, h.hospital_name, p.sex, p.birth_year, p.tel,
                g.blood_pressure, g.tentative_diagnose, g.medical_history, g.sickroom
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                left join hospital as h on g.regist_hospital_id = h.hospital_id
                where guard_hospital_id = :hospital_id 
                and ((g.status < 3 and g.mode in (1,2)) or (g.status = 0 and g.mode = 3))
                order by g.guardian_id desc';
        if ($rows != null) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitlAdminInfo($hospitalId)
    {
        $sql = 'select h.hospital_id, h.hospital_name, h.address, h.tel, a.login_name, a.password
                from hospital as h inner join account as a on h.hospital_id = a.hospital_id
                where h.hospital_id = :hospital_id and a.type = 1 limit 1';
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
    public function getPatientList($hospitalId, $offset = 0, $rows = null, $where = '')
    {
        if ('' != $where) {
            $where = ' and ' . $where;
        }
        $sql = 'select g.patient_id, g.guardian_id, g.status, g.device_id,
                p.patient_name, p.sex, p.birth_year, p.tel,
                g.start_time, g.end_time, g.regist_doctor_name, g.sickroom
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where regist_hospital_id = :hospital ' . $where . 'order by guardian_id desc';
        if ($rows != null) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getPatientListCondition($flag, $hospitalId, $name)
    {
        if ('1' == $flag) {
            $where = ' where regist_hospital_id = ' . $hospitalId;
        } else {
            $childHospital = $this->getHospitalChild($hospitalId);
            $where = ' where regist_hospital_id in (';
            foreach ($childHospital as $hospital) {
                $where .= $hospital['hospital_id'] . ',';
            }
            $where = substr($where, 0, -1);
            $where .= ') ';
        }
        $sql = "select g.patient_id, g.guardian_id, g.status, g.device_id,
                p.patient_name, p.sex, p.birth_year, p.tel,
                g.start_time, g.end_time, g.regist_doctor_name, g.sickroom
                from guardian as g left join patient as p on g.patient_id = p.patient_id 
                $where and p.patient_name = '$name'";
        return $this->getDataAll($sql);
    }
    public function getPatientListDistinct($where, $offset = 0, $rows = null)
    {
        $sql = "select distinct g.patient_id, p.patient_name, p.sex, p.birth_year, p.tel
        from guardian as g left join patient as p on g.patient_id = p.patient_id
        where $where order by guardian_id desc";
        if ($rows != null) {
        $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getPatientByDevice($deviceId)
    {
        $sql = 'select guardian_id, patient_name, mode
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where device_id = :device_id and status < 2 order by guardian_id desc limit 1';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatientNameByGuardian($guardianId)
    {
        $sql = 'select patient_name from guardian as g
                left join patient as p on g.patient_id = p.patient_id
                where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getPatientsForAnalytics($hospitalId, $reported = null, $startTime = null, $endTime = null)
    {
        $sql = 'select guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 where regist_hospital_id = :hospital_id';
        
        $param = array(':hospital_id' => $hospitalId);
        if (isset($reported)) {
            $sql .= ' and reported = :reported ';
            $param[':reported'] = $reported;
        }
        if (isset($startTime)) {
            $sql .= ' and end_time >= :start_time ';
            $param[':start_time'] = $startTime;
        }
        if (isset($endTime)) {
            $sql .= ' and end_time <= :end_time ';
            $param[':end_time'] = $endTime;
        }
        
        return $this->getDataAll($sql, $param);
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
    public function delDoctor($doctorId)
    {
        $sql = 'delete from Account where account_id = :account_id';
        $param = [':account_id' => $doctorId];
        return $this->deleteData($sql, $param);
    }
    public function delEcg($ecgId)
    {
        $sql = 'delete from ecg where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->deleteData($sql, $param);
    }
    public function editAccount($accountId, array $data)
    {
        $sql = 'update account set ';
        foreach ($data as $key => $value) {
            $sql .= $key . ' = "' . $value . '",';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ' where account_id = :account';
        $param = [':account' => $accountId];
        return $this->updateData($sql, $param);
    }
    public function editHospital($hospitalId, array $data)
    {
        $sql = 'update hospital set ';
        foreach ($data as $key => $value) {
            $sql .= $key . ' = "' . $value . '",';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ' where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        return $this->updateData($sql, $param);
    }
    public function editPatient($patientId, array $data)
    {
        $sql = 'update patient set ';
        foreach ($data as $key => $value) {
            $sql .= $key . ' = "' . $value . '",';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ' where patient_id = :patient';
        $param = [':patient' => $patientId];
        return $this->updateData($sql, $param);
    }
    /**
     * step1 of consultation: accepted by parent hospial.
     */
    public function flowConsultationAccept($hospitalId, $consultationId, $guardianId)
    {
        $this->pdo->beginTransaction();
        //change guardian hospital.
        $sql = 'update guardian set guard_hospital_id = :hospital where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId, ':hospital' => $hospitalId];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        //change status.
        $sql = 'update consultation set status = 2 where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        //succeed.
        $this->pdo->commit();
        return true;
    }
    /**
     * step4 of consultation: end.
     */
    public function flowConsultationEnd($consultationId)
    {
        $sql = 'update consultation set status = 0 where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId];
        return $this->updateData($sql, $param);
    }
    /**
     * step3 of consultation: replied by parent hospital.
     */
    public function flowConsultationReply($consultationId, $result)
    {
        $sql = 'update consultation set status = 2, response_message = :result
                where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId, ':result' => $result];
        return $this->updateData($sql, $param);
    }
    /**
     * step1 of consultation: create a consultation and send.
     */
    public function flowConsultationSend($requestHospital, $responseHospital, $ecgId, $mesage)
    {
        $sql = 'insert into consultation(ecg_id, request_hospital_id, request_message, response_hospital_id, status)
                values (:ecg_id, :request_hospital_id, :request_message, :response_hospital_id, 1)';
        $param = [':ecg_id' => $ecgId, ':request_hospital_id' => $requestHospital, ':request_message' => $mesage,
                        ':response_hospital_id' => $responseHospital];
        return $this->insertData($sql, $param);
    }
    /**
     * step5 of guardian: add diagnosis to ecg data.
     */
    public function flowGuardianAddDiagnosis($ecgId, $doctorId, $content)
    {
        $sql = 'insert into diagnosis (ecg_id, doctor_id, content) values (:ecg, :doctor, :content)';
        $param = [':ecg' => $ecgId, ':doctor' => $doctorId, ':content' => $content];
        return $this->insertData($sql, $param);
    }
    /**
     * step3 of guardian: add ecg data by client.
     */
    public function flowGuardianAddEcg($guardianId, $alertFlag, $dataPath)
    {
        $sql = 'insert into ecg(guardian_id, alert_flag, data_path) values(:guardian, :alert, :path)';
        $param = [':guardian' => $guardianId, ':alert' => $alertFlag, ':path' => $dataPath];
        return $this->insertData($sql, $param);
    }
    /**
     * step7 of guardian: create result of the guardian.
     */
    public function flowGuardianAddResult($guardianId, $result)
    {
        $sql = 'update guardian set guardian_result = :result, status = 3 where guardian_id = :guardian';
        $param = [':guardian' => $guardianId, ':result' => $result];
        return $this->updateData($sql, $param);
    }
    /**
     * step1 of guardian: add user.
     */
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
        //use transaction.
        $this->pdo->beginTransaction();
        //if patient not existed, add to patient table.
        if ('' == $patientId) {
            $sql = 'insert into patient(patient_name, sex, birth_year, tel, address, creator)
                    values(:name, :sex, :birth, :tel, :address, :creator)';
            $param = [':name' => $patientName, ':sex' => $sex, ':birth' => $birthYear,
                            ':tel' => $tel, ':address' => $address, ':creator' => $creator];
            $patientId = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $patientId) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        //add to guardian table.
        $sql = 'insert into guardian(device_id, regist_hospital_id, guard_hospital_id,
                    patient_id, mode, guardian_hours, lead, doctor_id, status,
                    sickroom, blood_pressure, height, weight, family_tel,
                    tentative_diagnose, medical_history, regist_doctor_name)
                    values (:device, :regist_hospital, :guard_hospital, :patient, :mode,
                    :hours, :lead, :doctor_id, 0, :sickroom, :blood_pressure, :height,
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
        //succeed.
        $this->pdo->commit();
        return $guardianId;
    }
    /**
     * step? of guardian: delete guardian(sometimes).
     */
    public function flowGuardianDelete($guardianId)
    {
        $sql = 'delete from guardian where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->deleteData($sql, $param);
    }
    /**
     * step8 of guardian: end all of the guardian.
     */
    public function flowGuardianEndAll($guardianId)
    {
        $sql = 'update guardian set status = 4 where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    /**
     * step6 of guardian: end the guardian(after that, create result).
     */
    public function flowGuardianEndGuard($guardianId)
    {
        $sql = 'update guardian set status = 2, end_time = now() where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    /**
     * step4 of guardian: read the ecg data.
     */
    public function flowGuardianReadEcg($ecgId)
    {
        $sql = 'update ecg set read_status = 1 where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->updateData($sql, $param);
    }
    /**
     * step2 of guardian: start.
     */
    public function flowGuardianStartGuard($guardianId)
    {
        $sql = 'update guardian set status = 1, start_time = now() where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function markDiagnosis($diagnosisId, $mark)
    {
        $sql = 'update diagnosis set mark = :mark where diagnosis_id = :diagnosis_id';
        $param = [':diagnosis_id' => $diagnosisId, ':mark' => $mark];
        return $this->updateData($sql, $param);
    }
    public function markEcg($ecgId, $mark)
    {
        $sql = 'update ecg set mark = :mark where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId, ':mark' => $mark];
        return $this->updateData($sql, $param);
    }
    public function uploadReport($guardianId, $file)
    {
        $sql = 'update guardian set reported = 1, report_file = :file where guardian_id = :guardian';
        $param = [':file' => $file, ':guardian' => $guardianId];
        return $this->insertData($sql, $param);
    }
    //************************* execute methods(public) *************************
    //*********************************** end ***********************************
}
