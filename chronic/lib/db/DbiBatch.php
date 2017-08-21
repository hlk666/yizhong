<?php
require_once PATH_ROOT . 'lib/db/BaseDbi.php';

class DbiBatch extends BaseDbi
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
    
    public function getPlans($startTime, $endTime)
    {
        $sql = 'select p.id as plan_id, p.patient_id, pa.name, pa.tel, plan_time, plan_value 
                from plan as p inner join patient as pa on p.patient_id = pa.id
                where plan_time >= :start and plan_time <= :end 
                and notice_time is null and execute_time is null';
        $param = [':start' => $startTime, ':end' => $endTime];
        return $this->getDataAll($sql, $param);
    }
    public function getTel($patientId)
    {
        $sql = 'select tel from patient where id = :id';
        $param = [':id' => $patientId];
        return $this->getDataString($sql, $param);
    }
    public function setMessageSend($planId)
    {
        $sql = 'update plan set notice_time = now() where plan_id = :plan';
        $param = [':plan' => $planId];
        return $this->updateData($sql, $param);
    }
}
