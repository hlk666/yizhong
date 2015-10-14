<?php
require_once PATH_LIB . 'Logger.php';

class Dbi
{
    private $logFile = 'dbLog.txt';
    private $pdo = null;
    private static $instance = null;
    
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
            return 0;
        }
    }
    
    public function getInspection($patientId)
    {
        try {
            $sql = 'select status from inspection where p_id = :id limit 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $patientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
            return 0;
        }
    }
    
//     public function existInspection($patientId) {
//         try {
//             $sql = 'select 1 from inspection where p_id = :id limit 1';
//             $stmt = $this->pdo->prepare($sql);
//             $stmt->execute([':id' => $patientId]);
//             return $stmt->rowCount() > 0;
//         } catch (Exception $e) {
//             Logger::write($this->logFile, $e->getMessage());
//             return 0;
//         }
//     }
    
    public function setInspectionStatus($patientId, $status)
    {
        try {
            $sql = 'update inspection set status = :status where p_id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':status' => $status,':id' => $patientId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
        }
    }
    
    public function addInspection($patientId, $status)
    {
        try {
            $sql = 'insert into inspection(p_id, status) values(:id, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $patientId, ':status' => $status]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
        }
    }
    
    public function delInspection($patientId)
    {
        try {
            $sql = 'delete from inspection where p_id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $patientId]);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
        }
    }
}