<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiAdmin extends BaseDbi
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
    public function addDevice($hospital, $device)
    {
        $sql = 'insert into device (device_id, hospital_id) values (:device, :hospital)';
        $param = [':device' => $device, ':hospital' => $hospital];
        return $this->insertData($sql, $param);
    }
    public function delDevice($deviceId)
    {
        $sql = 'delete from device where device_id = :device';
        $param = [':device' => $deviceId];
        return $this->deleteData($sql, $param);
    }
    public function existedDevice($deviceId)
    {
        return $this->existData('device', 'device_id = ' . $deviceId);
    }
    public function getAdminAcount($loginName)
    {
        $sql = 'select account_id, real_name as name, type, password, hospital_id
                from account where login_name = :user and type = 0 limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardiansByRegistTime($startTime, $endTime, $exceptHospitalList)
    {
        $sql = 'select guardian_id, device_id, regist_hospital_id, guard_hospital_id, mode, p.patient_name, 
                h1.hospital_name as regist_hospital_name, h2.hospital_name as guard_hospital_name
                from guardian as g left join hospital as h1 on g.regist_hospital_id = h1.hospital_id
                left join hospital as h2 on g.guard_hospital_id = h2.hospital_id
                left join patient as p on g.patient_id = p.patient_id
                where regist_time <= "' . $endTime . '"';
        if (null !== $startTime) {
            $sql .= ' and regist_time >= "' . $startTime . '"';
        }
        if (!empty($exceptHospitalList)) {
            $sql .= " and regist_hospital_id not in ($exceptHospitalList)";
        }
        $sql .= ' order by g.regist_hospital_id';
        return $this->getDataAll($sql);
    }
    public function getEcgs($startTime, $endTime, $exceptHospitalList)
    {
        $sql = 'select ecg_id, e.guardian_id, alert_flag, create_time
                from ecg as e left join guardian as g on e.guardian_id = g.guardian_id
                where create_time >= :start and create_time <= :end ';
        if (!empty($exceptHospitalList)) {
            $sql .= " and regist_hospital_id not in ($exceptHospitalList)";
        }
        $param = [':start' => $startTime, ':end' => $endTime];
        return $this->getDataAll($sql, $param);
    }
    public function getDeviceSum($exceptHospitalList)
    {
        $sql = 'select count(device_id) as total from device where 1 ';
        if (!empty($exceptHospitalList)) {
            $sql .= " and hospital_id not in ($exceptHospitalList)";
        }
        return $this->getDataRow($sql);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name, address, tel, parent_flag, a.login_name
                from hospital as h inner join account as a on h.hospital_id = a.hospital_id
                where h.hospital_id = :hospital_id and a.type = 1 limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalList($offset = 0, $rows = null)
    {
        $sql = 'select hospital_id, hospital_name, tel, address, parent_flag from hospital ';
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    /*
    public function existedLoginName($loginName)
    {
        return $this->existData('account', 'login_name = "' . $loginName . '"');
    }
    
    public function getAcount($loginName)
    {
        $sql = 'select account_id, real_name as name, type, password, hospital_id 
                from account where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getDoctorInfo($doctorId)
    {
        $sql = 'select account_id as doctor_id, login_name, real_name as doctor_name
                from account where account_id = :acount_id limit 1';
        $param = [':acount_id' => $doctorId];
        return $this->getDataRow($sql, $param);
    }
    public function getDoctorList($hospitalId)
    {
        $sql = 'select account_id as doctor_id, login_name as user, real_name as doctor_name, type
                from account where hospital_id = :hospital_id';
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
    
    public function addAccount($loginName, $realName, $password, $type, $hospitalId, $creator)
    {
        $sql = 'insert into account (login_name, real_name, password, type, hospital_id, creator)
                values (:login_name, :real_name, :password, :type, :hospital_id, :creator)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':password' => $password,
                        ':type' => $type, ':hospital_id' => $hospitalId,':creator' => $creator ];
        return $this->insertData($sql, $param);
    }
    public function editAccount($accountId, array $data)
    {
        return $this->updateTableByKey('account', 'account_id', $accountId, $data);
    }
    public function editGuardian($guardianId, array $data)
    {
        return $this->updateTableByKey('guardian', 'guardian_id', $guardianId, $data);
    }
    public function editHospital($hospitalId, array $data)
    {
        return $this->updateTableByKey('hospital', 'hospital_id', $hospitalId, $data);
    }
    public function editPatient($patientId, array $data)
    {
        return $this->updateTableByKey('patient', 'patient_id', $patientId, $data);
    }*/
}
