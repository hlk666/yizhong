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
    
    /**
     * @todo ecg1 -> ecg.
     */
    public function insertEcg($data)
    {
        try {
            $sql = 'insert into ecg1(p_id, recordTime, alert, path, readstate)'
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
    
    public function getStatus($patientId)
    {
        try {
            $sql = 'select status from remote_command where p_id = :id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $patientId]);
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
            $sql = 'update patient_history set reported = 1, report_path = :path, report_time = :time 
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
    
    public function updateCommand($patientId, $status)
    {
        try {
            $sql = 'update remote_command set status = :status where p_id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':status' => $status,':id' => $patientId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function addCommand($patientId, $status)
    {
        try {
            $sql = 'insert into remote_command(p_id, status) values(:id, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $patientId, ':status' => $status]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
    
    public function delCommand($patientId)
    {
        try {
            $sql = 'delete from remote_command where p_id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $patientId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return VALUE_DB_ERROR;
        }
    }
}
