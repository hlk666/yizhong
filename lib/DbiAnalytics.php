<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiAnalytics extends BaseDbi
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
        $sql .= ' order by guardian_id desc';
        return $this->getDataAll($sql, $param);
    }
    public function uploadReport($guardianId, $file)
    {
        $sql = 'update guardian set reported = 1, report_file = :file where guardian_id = :guardian';
        $param = [':file' => $file, ':guardian' => $guardianId];
        return $this->updateData($sql, $param);
    }
}
