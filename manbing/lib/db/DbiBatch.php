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
        $sql = 'select plan_id, case_name, child_hospital_name, child_hospital_tel, parent_hospital_name, 
                follow_time, follow_text, r.apply_hospital_id, c.tel from plan as p 
                inner join referral as r on p.referral_id = r.referral_id
                inner join `case` as c on r.case_id = c.case_id 
                where follow_time >= :start and follow_time <= :end and message_time is null';
        $param = [':start' => $startTime, ':end' => $endTime];
        return $this->getDataAll($sql, $param);
    }
    public function getTelList($hospitalId)
    {
        $sql = 'select distinct tel from `user` where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function setMessageSend($planId)
    {
        $sql = 'update plan set message_time = now() where plan_id = :plan';
        $param = [':plan' => $planId];
        return $this->updateData($sql, $param);
    }
}
