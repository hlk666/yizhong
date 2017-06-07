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
    public function getData($sql)
    {
        return $this->getDataAll($sql);
    }
    public function addDevice($hospital, $device, $city)
    {
        $sql = 'insert into device (device_id, hospital_id, city) values (:device, :hospital, :city)';
        $param = [':device' => $device, ':hospital' => $hospital, ':city' => $city];
        return $this->insertData($sql, $param);
    }
    public function addHospital($name, $tel, $address, $parentFlag, $parentHospital, $adminUser, $messageTel, $salesman, $comment)
    {
        $this->pdo->beginTransaction();
        $sql = 'insert into hospital(hospital_name, tel, address, parent_flag, sms_tel, salesman, comment)
                values (:name, :tel, :address, :flag, :sms_tel, :salesman, :comment)';
        $param = [':name' => $name, ':tel' => $tel, ':address' => $address, ':flag' => $parentFlag, 
                        ':sms_tel' => $messageTel, ':salesman' => $salesman, ':comment' => $comment];
        $hospitalId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        //default password:123456, defalt type:1->administrator
        $sql = 'insert into account(login_name, real_name, password, type, hospital_id)
                values (:login_name, :real_name, "e10adc3949ba59abbe56e057f20f883e", 1, :hospital_id)';
        $param = [':login_name' => $adminUser, ':real_name' => $name . '管理员', ':hospital_id' => $hospitalId];
        $insertAccount = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $insertAccount) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        if (!empty($parentHospital)) {
            $sql = 'insert into hospital_relation(hospital_id, parent_hospital_id)
                values (:hospital_id, :parent_hospital_id)';
            $param = [':hospital_id' => $hospitalId, ':parent_hospital_id' => $parentHospital];
            $ret = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        $this->pdo->commit();
        return true;
    }
    public function addHospitalParent($hospitalId, $parentHospital)
    {
        $sql = 'insert into hospital_relation(hospital_id, parent_hospital_id)
            values (:hospital_id, :parent_hospital_id)';
        $param = [':hospital_id' => $hospitalId, ':parent_hospital_id' => $parentHospital];
        return $this->insertData($sql, $param);
    }
    public function delDevice($deviceId)
    {
        $sql = 'delete from device where device_id = :device';
        $param = [':device' => $deviceId];
        return $this->deleteData($sql, $param);
    }
    public function delHospital($hospitalId)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'delete from hospital_relation where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'delete from hospital_relation where parent_hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'delete from account where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'delete from device where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'delete from hospital where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        $ret = $this->deleteData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return true;
    }
    public function delHospitalRelation($hospitalId, array $parentHospitalIdList = array())
    {
        $sql = 'delete from hospital_relation where hospital_id = :hospital ';
        if (!empty($parentHospitalIdList)) {
            $list = '(';
            foreach ($parentHospitalIdList as $id) {
                $list .= $id . ',';
            }
            $list = substr($list, 0, -1);
            $list .= ')';
            $sql .= ' and parent_hospital_id not in ' . $list;
        }
        $param = [':hospital' => $hospitalId];
        return $this->deleteData($sql, $param);
    }
    public function editHospital($hospitalId, $hospitalName, $hospitalTel, $hospitalAddress, 
            $parentFlag, $loginUser, $messageTel, $salesman, $comment)
    {
        $this->pdo->beginTransaction();
    
        $sql = 'update account set login_name = :login_user, real_name = :real_name 
                where hospital_id = :hospital and type = 1';
        $param = [':login_user' => $loginUser, ':hospital' => $hospitalId, ':real_name' => $hospitalName . '管理员',];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'update hospital set hospital_name = :name, tel = :tel, address = :address, 
                parent_flag = :flag, sms_tel = :sms_tel, salesman = :salesman, comment = :comment
                where hospital_id = :hospital';
        $param = [':hospital' => $hospitalId, ':name' => $hospitalName, ':tel' => $hospitalTel,
                        ':address' => $hospitalAddress, ':flag' => $parentFlag, ':sms_tel' => $messageTel, 
                        ':salesman' => $salesman, ':comment' => $comment];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return true;
    }
    public function editTree($hospitalId, $analysisHospital, $reportHospital, $titleHospital)
    {
        $param = [':hospital' => $hospitalId, ':analysis' => $analysisHospital, ':report' => $reportHospital, 'title' => $titleHospital];
        if ($this->existData('hospital_tree', 'hospital_id = ' . $hospitalId)) {
            $sql = 'update hospital_tree set analysis_hospital = :analysis, report_hospital = :report, title_hospital = :title
                    where hospital_id = :hospital';
            $ret = $this->updateData($sql, $param);
        } else {
            $sql = 'insert into hospital_tree(hospital_id, analysis_hospital, report_hospital, title_hospital)
                    values (:hospital, :analysis, :report, :title)';
            $ret = $this->insertData($sql, $param);
        }
        
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function existedDevice($deviceId)
    {
        return $this->existData('device', 'device_id = ' . $deviceId);
    }
    public function existedLoginName($loginName, $hospital)
    {
        return $this->existData('account', "login_name = '$loginName' and hospital_id <> $hospital");
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
    public function getDeviceBloc()
    {
        $sql = 'select distinct hospital_id, city from device order by city, hospital_id';
        return $this->getDataAll($sql);
    }
    public function getDeviceIdList($city, $hospital = null)
    {
        $sql = 'select device_id from device where city = ' . $city;
        if (!empty($hospital)) {
            $sql .= ' and hospital_id = ' . $hospital;
        }
        return $this->getDataAll($sql);
    }
    public function getDeviceList($hospital = null, $offset = 0, $rows = null)
    {
        $sql = 'select hospital_name, device_id, city from device as d
                inner join hospital as h on d.hospital_id = h.hospital_id';
        if (null !== $hospital){
            $sql .= ' where d.hospital_id = ' . $hospital;
        }
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
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
        $sql = 'select h.hospital_id, hospital_name, address, tel, parent_flag, a.login_name, h.sms_tel, h.salesman, h.comment
                from hospital as h inner join account as a on h.hospital_id = a.hospital_id
                where h.hospital_id = :hospital_id and a.type = 1 limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalList($offset = 0, $rows = null)
    {
        $sql = 'select h.hospital_id, hospital_name, tel, address, parent_flag, a.login_name 
                from hospital as h left join account as a on h.hospital_id = a.hospital_id where a.type = 1 order by h.hospital_id ';
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalParent($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.parent_hospital_id
                where r.hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalParentList()
    {
        $sql = 'select hospital_id, hospital_name from hospital where parent_flag = 1';
        return $this->getDataAll($sql);
    }
    public function getHospitalTree($hospitalId)
    {
        $sql = 'select hospital_id, analysis_hospital, report_hospital, title_hospital from hospital_tree
                where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getSalesmanList()
    {
        $sql = 'select distinct salesman from hospital where salesman <> "";';
        return $this->getDataAll($sql);
    }
    public function getSalesmanData($salesman, $startTime = null, $endTime = null, $offset = 0, $rows = null)
    {
        if (empty($salesman)) {
            return array();
        }
        $sql = 'select h.hospital_name, p.patient_name, regist_time, g.regist_doctor_name as doctor_name
                from guardian as g inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                where regist_hospital_id in (select hospital_id from hospital where salesman = :salesman) ';
        if (null !== $startTime) {
            $sql .= " and regist_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and regist_time <= '$endTime' ";
        }
        
        $sql .= ' order by g.guardian_id desc';
        
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        
        $param = [':salesman' => $salesman];
        return $this->getDataAll($sql, $param);
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
