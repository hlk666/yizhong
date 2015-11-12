<?php
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class Dbi extends BaseDbi
{
    private static $instance;
    private $pdo = null;
    
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
    
    public function insertEcg($guardianId, $alertFlag, $dataPath)
    {
        try {
            $sql = 'insert into ecg(guardian_id, create_time, alert_flag, data_path, read_status)'
                    . ' values(:guardian_id, now(), :alert_flag, :data_path, 0)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                    ':guardian_id' => $guardianId,
                    ':alert_flag' => $alertFlag,
                    ':data_path' => $dataPath
            ));
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getAllData($sql)
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getEcg($guardianId, $startTime, $endTime)
    {
        try {
            $sql = 'select ecg_id, create_time, read_status, data_path from ecg where guardian_id = :guardian_id';
            $param = array(':guardian_id' => $guardianId);
            $sql .= ' order by ecg_id desc';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($param);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getConsultationRequest($hospitalId)
    {
        try {
            $sql = 'select consultation_id, c.ecg_id, request_message, request_time, 
                    e.guardian_id, h.hospital_name
                    from consultation as c inner join ecg as e on c.ecg_id = e.ecg_id 
                    left join hospital as h on c.request_hospital_id = h.hospital_id 
                    where response_hospital_id = :hospital_id and status = 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['hospital_id' => $hospitalId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function getConsultationResponse($hospitalId)
    {
        try {
            $sql = 'select consultation_id, c.ecg_id, response_message, response_time,
                    e.guardian_id, e.data_path, h.hospital_name
                    from consultation as c inner join ecg as e on c.ecg_id = e.ecg_id
                    left join hospital as h on c.response_hospital_id = h.hospital_id
                    where request_hospital_id = :hospital_id and status = 2';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['hospital_id' => $hospitalId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getRecordCount($table, $where)
    {
        try {
            $hasWhere = false;
            $sql = "select 1 from $table ";
            if (is_string($where) && $where != '') {
                $sql .= ' where ' . $where . ' ';
                $hasWhere = true;
            }
            if (is_array($where) && !empty($where)) {
                $sql .= $hasWhere ? '' : ' where ';
                foreach ($where as $key => $value) {
                    $sql .= " $key = \"$value\"";
                }
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function existData($tableName, $where) {
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
    
    public function updateReport($guardianId, $file)
    {
        try {
            $sql = 'update guardian set reported = 1, report_file = :file where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':file' => $file, ':guardian_id' => $guardianId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function editAccount($accountId, array $data)
    {
        try {
            if (empty($data)) {
                throw new Exception('parameter of $data is empty.');
            }
            
            $sql = 'update account set ';
            foreach ($data as $key => $value) {
                $sql .= $key . ' = "' . $value . '",';
            }
            $sql = substr($sql, 0, -1);
            $sql .= ' where account_id = ' . $accountId;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function editHospital($hospitalId, array $data)
    {
        try {
            if (empty($data)) {
                throw new Exception('parameter of $data is empty.');
            }
    
            $sql = 'update hospital set ';
            foreach ($data as $key => $value) {
                $sql .= $key . ' = "' . $value . '",';
            }
            $sql = substr($sql, 0, -1);
            $sql .= ' where hospital_id = ' . $hospitalId;
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function addAccount($loginName, $realName, $password, $type, $hospitalId, $creator)
    {
        try {
            $sql = 'insert into account (login_name, real_name, password, type, hospital_id, creator) 
                    values (:login_name, :real_name, :password, :type, :hospital_id, :creator)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                            ':login_name' => $loginName, 
                            ':real_name' => $realName,
                            ':password' => $password,
                            ':type' => $type,
                            ':hospital_id' => $hospitalId,
                            ':creator' => $creator
            ));
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function startGuard($guardianId)
    {
        try {
            $sql = 'update guardian set status = 1, start_time = now() where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function createDiagnosis($ecgId, $doctorId, $content)
    {
        try {
            $sql = 'insert into diagnosis (ecg_id, doctor_id, content) values (:ecg, :doctor, :content)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ecg' => $ecgId, ':doctor' => $doctorId, ':content' => $content]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function endGuard($guardianId)
    {
        try {
            $sql = 'update guardian set status = 2, end_time = now() where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function createIllResult($guardianId, $result)
    {
        try {
            $sql = 'update guardian set guardian_result = :result, status = 3 
                    where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId, ':result' => $result]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function endMedical($guardianId)
    {
        try {
            $sql = 'update guardian set status = 4 where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function addConsultation($requestHospital, $responseHospital, $ecgId, $mesage)
    {
        try {
            $sql = 'insert into consultation(ecg_id, request_hospital_id, request_message, response_hospital_id, status)'
                    . ' values(:ecg_id, :request_hospital_id, :request_message, :response_hospital_id, 1)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                            ':ecg_id' => $ecgId,
                            ':request_hospital_id' => $requestHospital,
                            ':request_message' => $mesage,
                            ':response_hospital_id' => $responseHospital
            ));
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function handleConsultation($hospitalId, $consultationId, $guardianId)
    {
        try {
            $this->pdo->beginTransaction();
            $sql = 'update guardian set guard_hospital_id = :hospital where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId, ':hospital' => $hospitalId]);
            
            $sql = 'update consultation set status = 2 where consultation_id = :consultation_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':consultation_id' => $consultationId]);
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function replyConsultation($consultationId, $result)
    {
        try {
            $sql = 'update consultation set status = 2, response_message = :result 
                    where consultation_id = :consultation_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':consultation_id' => $consultationId, ':result' => $result]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function endConsultation($consultationId)
    {
        try {
            $sql = 'update consultation set status = 0 where consultation_id = :consultation_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':consultation_id' => $consultationId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function delEcg($ecgId)
    {
        try {
            $sql = 'delete from ecg where ecg_id = :ecg_id';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':ecg_id' => $ecgId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function delDoctor($doctorId)
    {
        try {
            $sql = 'delete from Account where account_id = :account_id';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':account_id' => $doctorId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function setEcgReadStatus($ecgId)
    {
        try {
            $sql = 'update ecg set read_status = 1 where ecg_id = :ecg_id';
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':ecg_id' => $ecgId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function editGuardianResult($guardianId, $newResult)
    {
        try {
            $sql = 'update guardian set guardian_result = :result where guardian_id = :guardian_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':result' => $newResult, ':guardian_id' => $guardianId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getGuardianResult($guardianId)
    {
        try {
            $sql = 'select guardian_result from guardian where guardian_id = :guardian_id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
//             $ret = array();
//             while ($row = $stmt->fetchColumn()) {
//                 $ret[] = $row;
//             }
            $ret = $stmt->fetchColumn();
            return $ret;
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getPatientInfo($patientId)
    {
        $sql = 'select p_id as patient_id, p_name, birthYear as age, gender as sex, phone as tel, higherhos as hospital_id 
                from remote_ecg.patient_basic_info 
                where p_id = ' . $patientId;
        $data = $this->getAllData($sql);
        for ($i = 0; $i < count($data); $i++) {
            if (isset($data[$i]['birthYear'])) {
                if (false !== strpos($data[$i]['birthYear'], '岁')) {
                    $data[$i]['birthYear'] = trim(str_replace('岁', '', $data[$i]['birthYear']));
                }
                if (is_numeric($data[$i]['birthYear']) && $data[$i]['birthYear'] > 1900) {
                    $data[$i]['birthYear'] = date('Y') - $data[$i]['birthYear'];
                }
            }
        }
        if (empty($data)) {
            return array();
        } else {
            return $data[0];
        }
    }
    
    public function getPatientList($where, $offset = 0, $rows = null)
    {
        try {
            $sql = "select g.patient_id, g.guardian_id, g.status, g.device_id, g.start_time, g.end_time, 
                    p.patient_name, p.sex, p.birth_year, p.tel, 
                    g.regist_doctor_name, g.sickroom
                    from guardian as g left join patient as p on g.patient_id = p.patient_id 
                    where $where order by guardian_id desc";
            if ($rows != null) {
                $sql .= " limit $offset, $rows";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getPatientListDistinct($where, $offset = 0, $rows = null)
    {
        try {
            $sql = "select distinct g.patient_id, p.patient_name, p.sex, p.birth_year, p.tel 
            from guardian as g left join patient as p on g.patient_id = p.patient_id
            where $where order by guardian_id desc";
            if ($rows != null) {
            $sql .= " limit $offset, $rows";
            }
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
        Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
        }
    
    public function getDiagnosisByGuardian($guardianId)
    {
        try {
            $sql = 'select d.ecg_id, a.real_name as doctor_name, e.data_path, d.content, 
                    e.create_time as alert_time, d.create_time as diagnose_time
                    from diagnosis as d 
                    inner join ecg as e on d.ecg_id = e.ecg_id
                    left join account as a on d.doctor_id = a.account_id
                    where e.guardian_id = :guardian_id order by d.diagnosis_id desc';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getDiagnosisByEcg($ecgId)
    {
        try {
            $sql = 'select d.ecg_id, a.real_name as doctor_name, d.content, d.create_time
                    from diagnosis as d left join account as a on d.doctor_id = a.account_id
                    where d.ecg_id = :ecg_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ecg_id' => $ecgId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getGuardianStatusByDevice($deviceId)
    {
        try {
            $sql = 'select guardian_id, patient_id, status from guardian where device_id = :device_id and status < 2';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':device_id' => $deviceId]);
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
    
    public function getPatientByDevice($deviceId)
    {
        try {
            $sql = 'select guardian_id, patient_name, mode
                    from guardian as g left join patient as p on g.patient_id = p.patient_id 
                    where device_id = :device_id and status < 2 order by guardian_id desc limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':device_id' => $deviceId]);
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
    
    public function getGuardianStatusByGuardian($guardianId)
    {
        try {
            $sql = 'select status from guardian where guardian_id = :guardian_id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function getGuardianTime($guardianId)
    {
        try {
            $sql = 'select start_time, end_time from guardian where guardian_id = :guardian_id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
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
    
    public function getGuardianList($hospitalId)
    {
        try {
            $sql = 'select g.guardian_id, g.patient_id, g.status, g.start_time, g.end_time,
                    p.patient_name, h.hospital_name, p.sex, p.birth_year, p.tel, 
                    g.blood_pressure, g.tentative_diagnose, g.medical_history, g.sickroom
                    from guardian as g left join patient as p on g.patient_id = p.patient_id
                    left join hospital as h on g.regist_hospital_id = h.hospital_id
                    where guard_hospital_id = :hospital_id and g.status = 1
                    order by g.guardian_id desc';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':hospital_id' => $hospitalId]);
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
    
    public function getPatient($patientId)
    {
        try {
            $sql = 'select patient_id, patient_name from patient where patient_id = :patient_id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':patient_id' => $patientId]);
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
    
    public function getParentHospitals($hospitalId)
    {
        try {
            $sql = 'select h.hospital_id, h.hospital_name 
                    from hospital as h inner join hospital_relation as r 
                        on h.hospital_id = r.parent_hospital_id
                    where r.hospital_id = :hospital_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':hospital_id' => $hospitalId]);
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
    
    public function getChildHospitals($hospitalId)
    {
        try {
            $sql = 'select hospital.hospital_id, hospital_name from hospital inner join hospital_relation
                    on hospital.hospital_id = hospital_relation.hospital_id
                    where hospital_relation.parent_hospital_id = :hospital_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':hospital_id' => $hospitalId]);
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
    
    public function getAcount($loginName)
    {
        try {
            $sql = 'select account_id, type, password, hospital_id from account where login_name = :user';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':user' => $loginName]);
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
    public function getDoctorList($hospitalId, $offset = 0, $rows = null)
    {
        try {
            $sql = 'select account_id, login_name, real_name as doctor_name 
                    from account where hospital_id = :hospital_id and type = 2';
            if ($rows != null) {
                $sql .= " limit $offset, $rows";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':hospital_id' => $hospitalId]);
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
    public function getDoctorInfo($accountId)
    {
        try {
            $sql = 'select account_id as doctor_id, login_name, real_name as doctor_name
                    from account where account_id = :acount_id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':acount_id' => $accountId]);
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
    public function getHospitlAdminInfo($hospitalId)
    {
        try {
            $sql = 'select h.hospital_id, h.hospital_name, h.address, h.tel, a.login_name, a.password
                    from hospital as h inner join account as a on h.hospital_id = a.hospital_id
                    where h.hospital_id = :hospital_id and a.type = 1 limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':hospital_id' => $hospitalId]);
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
    
//     public function getDoctorsByHospital($hospitalId)
//     {
//         try {
//             $sql = 'select account_id, real_name from account where hospital_id = :hospital_id';
//             $stmt = $this->pdo->prepare($sql);
//             $stmt->execute([':hospital_id' => $hospitalId]);
//             $ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
//             if (false === $ret) {
//                 return array();
//             } else {
//                 return $ret;
//             }
//         } catch (Exception $e) {
//             Logger::write($this->logFile, $e->getMessage());
//             return VALUE_DB_ERROR;
//         }
//     }
//     <td><select name="doctor" style="width: 80px" id="doctor">
//     php tag start
//     $doctors = Dbi::getDbi()->getDoctorsByHospital($registHospital);
//     foreach ($doctors as $value) {
//         echo '<option value="' . $value['account_id'] . '" ';
//         if (isset($_SESSION['guardian']) && $_SESSION['guardian']['doctor'] == $value['account_id']) {
//             echo 'selected="selected" ';
//         }
//         echo '>' . $value['real_name'] . '</option>';
//     }
//     php tag end
//         </select></td>
    
    public function getPatientNameByDevice($deviceId)
    {
        try {
            $sql = 'select patient_name from guardian as g 
                    left join patient as p on g.patient_id = p.patient_id 
                    where device_id = :device_id order by guardian_id desc limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':device_id' => $deviceId]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    public function getPatientNameByGuardian($guardianId)
    {
        try {
            $sql = 'select patient_name from guardian as g 
                    left join patient as p on g.patient_id = p.patient_id
                    where guardian_id = :guardian_id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':guardian_id' => $guardianId]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function registUser($patientName, $sex, $age, $tel, $device, $registHospital, $guardHospital, 
            $mode, $hours, $lead, $doctor, $sickRoom, $bloodPressure, $height, $weight, 
            $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName)
    {
        $birthYear = date('Y') - $age;
        try {
            $patientId = $this->getSamePatient($patientName, $birthYear, $tel);
            $this->pdo->beginTransaction();
            if (false == $patientId) {
                $patientId = $this->addPatient($patientName, $sex, $birthYear, $tel, $sickRoom, $doctor);
            }
            $guardianId = $this->addGuardian($device, $registHospital, $guardHospital, $patientId, $mode, $hours, 
                    $lead, $doctor, $sickRoom, $bloodPressure, $height, $weight, $familyTel, 
                    $tentativeDiagnose, $medicalHistory, $registDoctorName);
            $this->pdo->commit();
            return $guardianId;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    private function addPatient($patientName, $sex, $birthYear, $tel, $address, $creator)
    {
        $sql = 'insert into patient(patient_name, sex, birth_year, tel, address, creator)'
                . ' values(:name, :sex, :birth, :tel, :address, :creator)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(
                ':name' => $patientName,
                ':sex' => $sex,
                ':birth' => $birthYear,
                ':tel' => $tel,
                ':address' => $address,
                ':creator' => $creator
        ));
        return $this->pdo->lastInsertId();
    }
    
    private function addGuardian($device, $registHospital, $guardHospital, $patientId, 
            $mode, $hours, $lead, $doctor, $sickRoom, $bloodPressure, $height, $weight, 
            $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName)
    {
        $sql = 'insert into guardian(device_id, regist_hospital_id, guard_hospital_id, 
                patient_id, mode, guardian_hours, lead, doctor_id, status,
                sickroom, blood_pressure, height, weight, family_tel, 
                tentative_diagnose, medical_history, regist_doctor_name) 
                values(:device, :regist_hospital, :guard_hospital, :patient, :mode, 
                :hours, :lead, :doctor_id, 0, :sickroom, :blood_pressure, :height, 
                :weight, :family_tel, :ten_dia, :medical_history, :doctor_name)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array(
                ':device' => $device,
                ':regist_hospital' => $registHospital,
                ':guard_hospital' => $guardHospital,
                ':patient' => $patientId,
                ':mode' => $mode,
                ':hours' => $hours,
                ':lead' => $lead,
                ':doctor_id' => $doctor,
                ':sickroom' => $sickRoom,
                ':blood_pressure' => $bloodPressure,
                ':height' => $height,
                ':weight' => $weight,
                ':family_tel' => $familyTel,
                ':ten_dia' => $tentativeDiagnose,
                ':medical_history' => $medicalHistory,
                ':doctor_name' => $registDoctorName
        ));
        return $this->pdo->lastInsertId();
    }
    
    private function getSamePatient($patientName, $birthYear, $tel)
    {
        try {
            $sql = 'select patient_id from patient 
                    where patient_name = :name and birth_year = :birth and tel = :tel limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(':name' => $patientName, ':birth' => $birthYear, ':tel' => $tel));
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
//     public function addCommand($patientId, $status)
//     {
//         try {
//             $sql = 'insert into remote_command(p_id, status) values(:id, :status)';
//             $stmt = $this->pdo->prepare($sql);
//             $stmt->execute([':id' => $patientId, ':status' => $status]);
//         } catch (Exception $e) {
//             Logger::write($this->logFile, $e->getMessage());
//             return VALUE_DB_ERROR;
//         }
//     }
    
//     public function delCommand($patientId)
//     {
//         try {
//             $sql = 'delete from remote_command where p_id = :id';
//             $stmt = $this->pdo->prepare($sql);
//             $stmt->execute([':id' => $patientId]);
//         } catch (Exception $e) {
//             Logger::write($this->logFile, $e->getMessage());
//             return VALUE_DB_ERROR;
//         }
//     }
}
