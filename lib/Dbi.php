<?php
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'Logger.php';

class Dbi
{
    private $logFile = 'dbLog.txt';
    private $pdo = null;
    private static $instance;
    
    /**
     * @todo move information to config file.
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
    
    public function insertEcg($data)
    {
        try {
            $sql = 'insert into ecg(p_id, recordTime, alert, path, readstate)'
                    . ' values(:pid, :recordTime, :alert, :path, :readstate)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                    ':pid' => $data['pid'],
                    ':recordTime' => $data['recordTime'],
                    ':alert' => $data['alert'],
                    ':path' => $data['path'],
                    ':readstate' => 0
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
    
    public function existData($tableName, $where = array()) {
        try {
            $sql = "select 1 from $tableName where 1";
            foreach ($where as $key => $value) {
                $sql .= " and $key = \"$value\"";
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
    
    public function updateHistoryReport($hospitalId, $patientId, $startTime, $endTime)
    {
        try {
            $sql = 'update guardian_history set reported = 1, report_path = :path, report_time = :time 
                    where hospital_id = :h_id and patient_id = :p_id and start_time = :s_time and end_time = :e_time';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                    ':path' => URL_ROOT . 'report/' . $patientId . '/' . $startTime . '_' . $endTime . '.pdf',
                    ':time' => date('YmdHis'), 
                    ':h_id' => $hospitalId, 
                    ':p_id' => $patientId, 
                    ':s_time' => $startTime, 
                    ':e_time' => $endTime
            ]);
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
    
    public function addHistory($data)
    {
        try {
            $sql = 'insert into guardian_history(hospital_id, patient_id, start_time, end_time, name, age, sex, tel)'
                    . ' values(:hid, :pid, :stime, :etime, :name, :age, :sex, :tel)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                    ':hid' => $data['hospital_id'],
                    ':pid' => $data['patient_id'],
                    ':stime' => $data['start_time'],
                    ':etime' => $data['end_time'],
                    ':name' => $data['p_name'],
                    ':age' => $data['age'],
                    ':sex' => $data['sex'],
                    ':tel' => $data['tel']
            ));
            return $this->pdo->lastInsertId();
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
    
    public function getPatient($patientId)
    {
        try {
            $sql = 'select patient_id, patient_name from patient where patient_id = :patient_id';
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
            $sql = 'select hospital.hospital_id, hospital_name from hospital inner join hospital_relation
                    on hospital.hospital_id = hospital_relation.parent_hospital_id
                    where hospital_relation.hospital_id = :hospital_id';
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
    
    public function getGuardianPatientName($deviceId)
    {
        try {
            $sql = 'select patient_name from patient inner join guardian on patient.patient_id = guardian.patient_id 
                    where device_id = :device_id order by guardian_id desc limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':device_id' => $deviceId]);
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
