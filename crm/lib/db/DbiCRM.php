<?php
require_once PATH_ROOT . 'lib/db/BaseDbi.php';

class DbiCRM extends BaseDbi
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
    public function existedSchedule($shcheduleId)
    {
        return $this->existData('schedule', ['schedule_id' => $shcheduleId]);
    }
    
    public function addSchedule($userId, $hospitalId, $stage, $progress, $info)
    {
        $sql = 'insert into schedule (user_id, hospital_id, stage, progress, info) 
                values (:user, :hospital, :stage, :progress, :info)';
        $param = [':user' => $userId, ':hospital' => $hospitalId, ':stage' => $stage, ':progress' => $progress, ':info' => $info];
        return $this->insertData($sql, $param);
    }
    public function deleteSchedule($scheduleId)
    {
        $sql = 'delete from schedule where schedule_id = :id';
        $param = [':id' => $scheduleId];
        return $this->deleteData($sql, $param);
    }
    public function editSchedule($scheduleId, $hospitalId, $stage, $progress, $info)
    {
        $sql = 'update schedule set hospital_id = :hospital, stage = :stage, progress = :progress, info = :info where schedule_id = :id';
        $param = [':id' => $scheduleId, ':hospital' => $hospitalId, ':stage' => $stage, ':progress' => $progress, ':info' => $info];
        return $this->updateData($sql, $param);
    }
    public function getHospitalList($userId = null, $offset = 0, $rows = null)
    {
        $sql = 'select hospital_id, hospital_name, province, city, real_name
                from hospital as h inner join user as u on h.user_id = u.user_id';
        if (null !== $userId) {
            $sql .= " where h.user_id = $userId";
        }
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getScheduleInfo($scheduleId)
    {
        $sql = 'select schedule_id, hospital_id, stage, progress, info from schedule where schedule_id = :id';
        $param = [':id' => $scheduleId];
        return $this->getDataRow($sql, $param);
    }
    public function getScheduleList($userId, $offset = 0, $rows = null)
    {
        $sql = 'select schedule_id, create_date, hospital_name, stage, progress, info
                from schedule as s inner join hospital as h on s.hospital_id = h.hospital_id
                where s.user_id = :user order by schedule_id desc';
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':user' => $userId];
        return $this->getDataAll($sql, $param);
    }
    public function getUserInfo($loginName)
    {
        $sql = 'select user_id, real_name, password, type from user where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getUserInfoById($userId)
    {
        $sql = 'select user_id, real_name, password, type from user where user_id = :user limit 1';
        $param = [':user' => $userId];
        return $this->getDataRow($sql, $param);
    }
    public function getUserList($offset = 0, $rows = null)
    {
        $sql = 'select user_id, login_name, real_name, tel from `user` order by user_id desc';
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
}
