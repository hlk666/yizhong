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
    public function addCountyHospital($county, $count)
    {
        if ($this->existData('county_hospital', " county = '$county'")) {
            $sql = "update county_hospital set quantity = $count where county = '$county'";
        }
        $sql = "insert into county_hospital (county, quantity) values ('$county', '$count')";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addDevice($hospital, $device, $user = '')
    {
        if ($this->existData('device', "device_id = '$device'")) {
            $sql = "update device set hospital_id = '$hospital', agency = '' where device_id = '$device'";
        } else {
            $sql = "insert into device (device_id, hospital_id) values ('$device', '$hospital')";
        }
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $sql = "insert into history_device (device_id, hospital_id, user) values ('$device', '$hospital', '$user')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addDeviceFault($device, $fault, $content)
    {
        $sql = "insert into device_fault (device_id, fault, content) values ('$device', '$fault', '$content')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addDeviceFeedback($hospitalId, $feedback)
    {
        $sql = "insert into device_feedback (hospital_id, feedback) values ('$hospitalId', '$feedback')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addDevicePD($device)
    {
        if($this->existData('device', "device_id = '$device'")) {
            $sql = "update device set hospital_id = 40 where device_id = '$device'";
        } else {
            $sql = "insert into device (device_id, hospital_id) values ('$device', 40)";
        }
        
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addHospital($name, $type, $level, $tel, $province, $city, $county, $address, $parentFlag, $parentHospital, 
            $adminUser, $messageTel, $salesman, $comment, $analysisHospital, $reportHospital, $title1, $agency, 
            $contractFlag, $deviceSale, $displayCheck, $reportMustCheck, $invoiceName, $invoiceId, $invoiceAddressTel, 
            $invoiceBank, $creator, $double = '0', $agencyTel = '', $deviceList = array(), $contact = '')
    {
        $this->pdo->beginTransaction();
        $sql = "insert into hospital(hospital_name, type, level, tel, province, city, county, address, parent_flag, 
                sms_tel, agency, salesman, comment, contract_flag, device_sale, display_check, report_must_check, 
                invoice_name, invoice_id, invoice_addr_tel, invoice_bank, creator, worker, agency_tel, contact)
                values ('$name', '$type', '$level', '$tel', '$province', '$city', '$county', '$address', '$parentFlag', 
                '$messageTel', '$agency', '$salesman', '$comment', '$contractFlag', '$deviceSale', '$displayCheck', 
                '$reportMustCheck', '$invoiceName', '$invoiceId', '$invoiceAddressTel', '$invoiceBank', '$creator', 
                '$creator', '$agencyTel', '$contact')";
        $hospitalId = $this->insertData($sql);
        if (VALUE_DB_ERROR === $hospitalId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        //it is simple to edit code here for erp.2018-03-22 start.
        $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'erp' . DIRECTORY_SEPARATOR . 'changed_hospital.txt';
        
        if (!file_exists($file)) {
            file_put_contents($file, $hospitalId);
        } else {
            $oldArray = explode(',', file_get_contents($file));
            $oldArray[] = $hospitalId;
            $newArray = array_unique($oldArray);
            file_put_contents($file, implode(',', $newArray));
        }
        //2018-03-22 end.
        
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
        
        if (!empty($deviceList)) {
            foreach ($deviceList as $deviceId) {
                if ($this->existData('device', "device_id = $deviceId")) {
                    $sql = "update device set hospital_id = $hospitalId, agency = '' where device_id = '$deviceId'";
                } else {
                    $sql = "insert into device (device_id, hospital_id, agency) values ('$deviceId', $hospitalId, '')";
                }
                $ret = $this->updateData($sql);
                if (VALUE_DB_ERROR === $ret) {
                    $this->pdo->rollBack();
                    return VALUE_DB_ERROR;
                }
                
                $sql = "insert into history_device (device_id, hospital_id, agency, user) values ('$deviceId', $hospitalId, '', '$creator')";
                $ret = $this->insertData($sql);
                if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
                }
            }
        }
        
        if (!empty($analysisHospital) && !empty($reportHospital) && !empty($title1)) {
            $ret = $this->getHospitalName($title1);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
            $title = $ret['hospital_id'];
            if ($double == '1') {
                $sql = "insert into hospital_tree(hospital_id, analysis_hospital, report_hospital, title1, title2)
                values ($hospitalId, $analysisHospital, $reportHospital, $title, $hospitalId)";
            } else {
                $sql = "insert into hospital_tree(hospital_id, analysis_hospital, report_hospital, title1)
                values ($hospitalId, $analysisHospital, $reportHospital, $title)";
            }
            $ret = $this->insertData($sql);
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
    public function delAccount($doctorId)
    {
        $sql = "update account set hospital_id = 9999 where account_id = $doctorId";
        return $this->updateData($sql);
    }
    public function delDevice($deviceId, $hospital, $agency, $user)
    {
        $oldHospitalId = $this->getDataString("select hospital_id from device where device_id = $deviceId limit 1");
        if (VALUE_DB_ERROR === $oldHospitalId) {
            return VALUE_DB_ERROR;
        }
        if ($oldHospitalId !== '') {
            $sql = "update device set hospital_id = $hospital, agency = '$agency' where device_id = '$deviceId'";
        } else {
            $oldHospitalId = '0';
            $sql = "insert into device (device_id, hospital_id, agency) values ('$deviceId', $hospital, '$agency')";
        }
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $sql = "insert into history_device (device_id, hospital_id, agency, user, unbind_hospital_id) 
                values ('$deviceId', $hospital, '$agency', '$user', $oldHospitalId)";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
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
        
        $sql = 'delete from hospital_tree 
                where hospital_id = :hospital or analysis_hospital = :hospital or report_hospital = :hospital';
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
    public function editHospital($hospitalId, $hospitalName, $type, $level, $hospitalTel, $province, $city, $county,
            $hospitalAddress, $parentFlag, $loginUser, $messageTel, $agency, $salesman, $comment, 
            $contractFlag, $deviceSale, $serviceCharge, $displayCheck, $reportMustCheck, 
            $invoiceName, $invoiceId, $invoiceAddressTel, $invoiceBank, $worker, $filter, $contact, $agencyTel)
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
        
        $sql = "update hospital set hospital_name = '$hospitalName', type = '$type', level = '$level', tel = '$hospitalTel', 
                province = '$province', city = '$city', county = '$county', address = '$hospitalAddress', 
                parent_flag = '$parentFlag', sms_tel = '$messageTel', agency = '$agency', agency_tel = '$agencyTel', 
                salesman = '$salesman', comment = '$comment', contract_flag = '$contractFlag', device_sale = '$deviceSale', 
                display_check = '$displayCheck', service_charge = '$serviceCharge', report_must_check = '$reportMustCheck',
                invoice_name = '$invoiceName', invoice_id = '$invoiceId', invoice_addr_tel = '$invoiceAddressTel', 
                invoice_bank = '$invoiceBank', worker = '$worker', filter = '$filter', contact = '$contact'
                where hospital_id = '$hospitalId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return true;
    }
    public function editTree($hospitalId, $analysisHospital, $reportHospital, $title1, $title2)
    {
        if ($this->existData('hospital_tree', 'hospital_id = ' . $hospitalId)) {
            $sql = "update hospital_tree set analysis_hospital = $analysisHospital, report_hospital = $reportHospital, 
                    title1 = $title1, title2 = $title2 where hospital_id = $hospitalId";
            $ret = $this->updateData($sql);
        } else {
            $sql = "insert into hospital_tree(hospital_id, analysis_hospital, report_hospital, title1, title2)
                    values ($hospitalId, $analysisHospital, $reportHospital, $title1, $title2)";
            $ret = $this->insertData($sql);
        }
        
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function existedDevice($deviceId)
    {
        return $this->existData('device', "device_id = '$deviceId' and hospital_id <> 0");
    }
    public function existedDevice1($deviceId)
    {
        return $this->existData('device', "device_id = '$deviceId' and hospital_id > 1");
    }
    public function existedLoginName($loginName, $hospital)
    {
        return $this->existData('account', "login_name = '$loginName' and hospital_id <> $hospital");
    }
    
    public function getAccountList($hospital)
    {
        $sql = "select account_id as doctor_id, real_name as doctor_name from account where hospital_id = '$hospital'";
        return $this->getDataAll($sql);
    }
    public function getAccountForAnalytics($doctorList, $startTime, $endTime)
    {
        $sql = "select d.guardian_id as patient_id, p.patient_name, regist_hospital_id as hospital_id,
        g.start_time, g.end_time, d.status, a1.real_name as hbi_doctor, a2.real_name as report_doctor
        from guardian_data as d left join guardian as g on d.guardian_id = g.guardian_id
        left join patient as p on g.patient_id = p.patient_id
        left join account as a1 on d.hbi_doctor = a1.account_id
        left join account as a2 on d.report_doctor = a2.account_id
        where (hbi_doctor in ($doctorList) or report_doctor in ($doctorList))
        and g.regist_time >= '$startTime' and g.regist_time <= '$endTime'";
        return $this->getDataAll($sql);
    }
    public function getAdminAcount($loginName)
    {
        $sql = 'select account_id, real_name as name, type, password, hospital_id
                from account where login_name = :user and hospital_id = 1 limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getAgencyList()
    {
        $sql = 'select distinct agency as agency_id, agency as `name` from hospital where type <> 1';
        return $this->getDataAll($sql);
    }
    public function getCountyCount($county = '')
    {
        if (empty($county)) {
            $where = '';
        } else {
            $where = " and county in ($county) ";
        }
        $sql = "select county, quantity from county_hospital where 1 $where";
        return $this->getDataAll($sql);
    }
    public function getDepartment()
    {
        $sql = 'select d.department_id, d.hospital_id, h1.hospital_name as department_name, h2.hospital_name as hospital_name
                from department as d left join hospital as h1 on d.department_id = h1.hospital_id
                left join hospital as h2 on d.hospital_id = h2.hospital_id';
        return $this->getDataAll($sql);
    }
    public function getDeviceAgency($name)
    {
        if (empty($name)) {
            $and = '';
        } else {
            $and = " and agency = '$name'";
        }
        $sql = "select device_id, agency from device where hospital_id = 0 $and";
        return $this->getDataAll($sql);
    }
    public function getDeviceBloc()
    {
        $sql = 'select distinct hospital_id, city from device order by city, hospital_id';
        return $this->getDataAll($sql);
    }
    public function getDeviceById($id)
    {
        if (empty($id)) {
            return array();
        }
        $sql = "select ifnull(hospital_name, '') as hospital_name, device_id, d.agency from device as d
                left join hospital as h on d.hospital_id = h.hospital_id  where d.device_id like '%$id'";
        return $this->getDataAll($sql);
    }
    public function getDeviceFault($device, $fault, $time)
    {
        $whereDevice = empty($device) ? '' : " and device_id = '$device' ";
        $whereFault = empty($fault) ? '' : " and fault = '$fault' ";
        $whereTime = empty($time) ? '' : " and create_time >= '$time' ";
        $sql = "select device_id, fault, content, create_time from device_fault where 1 $whereDevice $whereFault $whereTime ";
        return $this->getDataAll($sql);
    }
    public function getDeviceFeedback($hospitalId, $createTime)
    {
        $whereHospital = empty($hospitalId) ? '' : " and hospital_id = '$hospitalId' ";
        $whereTime = empty($createTime) ? '' : " and create_time >= '$createTime' ";
        $sql = "select hospital_id, feedback, create_time from device_feedback where 1 $whereHospital $whereTime";
        return $this->getDataAll($sql);
    }
    public function getDeviceHospital($device)
    {
        $sql = "select d.device_id, h.hospital_id, hospital_name, h.agency, h.salesman, tel, agency_tel
                from hospital as h inner join device as d on h.hospital_id = d.hospital_id
                where d.device_id = '$device' limit 1";
        return $this->getDataRow($sql);
    }
    public function getDeviceGuardianCount($hospital, $startTime = null, $endTime = null)
    {
        if (empty($hospital)) {
            return array();
        }
        $sql = 'select d.device_id, count(g.guardian_id) as quantity
                from device as d left join
                (select * from guardian where regist_hospital_id = :hospital';
        if (!empty($startTime)) {
            $sql .= " and regist_time >= '$startTime'";
        }
        if (!empty($endTime)) {
            $sql .= " and regist_time <= '$endTime'";
        }
        $sql .= ') as g on d.device_id = g.device_id and d.hospital_id = g.regist_hospital_id
                where d.hospital_id = :hospital group by d.device_id order by count(g.guardian_id) desc';
        $param = [':hospital' => $hospital];
        return $this->getDataAll($sql, $param);
    }
    public function getDeviceGuardianLow($hospital)
    {
        if (empty($hospital)) {
            return array();
        }
        $sql = 'select t.hospital_id, analysis_hospital, report_hospital, h.hospital_name
                from hospital_tree as t inner join hospital as h on t.hospital_id = h.hospital_id
                where analysis_hospital = :hospital or report_hospital = :hospital';
        $param = [':hospital' => $hospital];
        return $this->getDataAll($sql, $param);
    }
    public function getDeviceIdList($city, $hospital = null)
    {
        $sql = 'select device_id from device where city = ' . $city;
        if (!empty($hospital)) {
            $sql .= ' and hospital_id = ' . $hospital;
        }
        return $this->getDataAll($sql);
    }
    public function getDeviceList($hospital, $offset = 0, $rows = null)
    {
        if (empty($hospital)) {
            return array();
        }
        $sql = "select hospital_name, device_id, '' as agency from device as d
                inner join hospital as h on d.hospital_id = h.hospital_id  where d.hospital_id = $hospital";
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getDeviceListAgency($agency, $offset = 0, $rows = null)
    {
        if (empty($agency)) {
            return array();
        }
        $sql = "select '-' as hospital_name, device_id, agency from device where agency = '$agency'";
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getDeviceListPD()
    {
        $sql = "select device_id from device where hospital_id = 40";
        return $this->getDataAll($sql);
    }
    public function getDeviceNotUsed($count)
    {
        if (empty($count)) {
            return array();
        }
        $sql = "select device_id from device where hospital_id = 1 limit $count";
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
    public function getDeviceSumByHospital($hospitals)
    {
        if (empty($hospitals)) {
            $where = '';
        } else {
            $where = " and hospital_id in ($hospitals) ";
        }
        $sql = "select hospital_id, count(device_id) as quantity from device where hospital_id > 0 $where group by hospital_id";
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
    public function getGuardiansDelete($sTime, $eTime)
    {
        $sql = 'select guardian_id, device_id, regist_hospital_id, p.patient_name, start_time, end_time, status, regist_doctor_name, bk_time
                from history_guardian as g inner join patient as p on g.patient_id = p.patient_id
                where 1 ';
        if ($sTime != null) {
            $sql .= " and g.regist_time >= '$sTime' ";
        }
        if ($eTime != null) {
            $sql .= " and g.regist_time <= '$eTime' ";
        }
        $sql .= " order by g.guardian_id desc";
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardiansStatistics($hospitalList, $sTime = null, $eTime = null, $hospitalId = null)
    {
        $sql = 'select g.guardian_id, g.device_id, g.regist_hospital_id, g.start_time, g.end_time, d.status,
                p.patient_name, p.sex, p.birth_year, p.tel, g.regist_doctor_name as doctor_name
                from guardian as g left join patient as p on g.patient_id = p.patient_id 
                left join guardian_data as d on g.guardian_id = d.guardian_id
                where 1 ';
        if (!empty($hospitalId)) {
            $sql .= " and regist_hospital_id in ($hospitalId) ";
        } else {
            if (!empty($hospitalList)) {
                $sql .= " and regist_hospital_id in ($hospitalList) ";
            }
        }
        if ($sTime != null) {
            $sql .= " and g.regist_time >= '$sTime' ";
        }
        if ($eTime != null) {
            $sql .= " and g.regist_time <= '$eTime' ";
        }
        $sql .= " order by g.guardian_id asc";
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardiansTime($hospital, $startTime, $endTime, $isAddResult = false)
    {
        $sqlIsAddResult = $isAddResult ? ' and guardian_result is not null' : '';
        if ($hospital == '0') {
            $sql = "select regist_hospital_id as hospital_id, count(guardian_id) as `count` from guardian
            where regist_time >= '$startTime' and regist_time < '$endTime' $sqlIsAddResult
            group by regist_hospital_id";
        } else {
            $sql = "select regist_hospital_id as hospital_id, count(guardian_id) as `count` from guardian
            where regist_hospital_id in ($hospital) and regist_time >= '$startTime' and regist_time < '$endTime' $sqlIsAddResult
            group by regist_hospital_id";
        }
        
        return $this->getDataAll($sql);
    }
    public function getHistoryDevice($deviceId)
    {
        $sql = "select * from history_device where device_id = $deviceId";
        return $this->getDataAll($sql);
    }
    public function getHospitalAgency($agency)
    {
        if (empty($agency)) {
            return array();
        }
        $sql = 'select distinct hospital_id, hospital_name from hospital where type <> 1 and agency = :agency';
        $param = [':agency' => $agency];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalAgencyList()
    {
        $sql = 'select hospital_id, hospital_name, agency as agency_name from hospital where type <> 1';
        return $this->getDataAll($sql);
    }
    public function getHospitalDevice($startTime, $endTime)
    {
        $time = '';
        if (!empty($startTime)) {
            $time .= "and h.create_time >= '$startTime' ";
        }
        if (!empty($endTime)) {
            $time .= "and h.create_time <= '$endTime' ";
        }
        $sql = "select h.hospital_id, h.hospital_name, h.salesman, h.agency, h.create_time, 
                h.type, h.province, h.city, h.county, h.device_sale, h.service_charge, 
                count(d.device_id) as device_count, h.filter
                from hospital as h left join device as d on h.hospital_id = d.hospital_id
                where 1 $time 
                group by h.hospital_id, h.hospital_name, h.salesman, h.agency, h.create_time, h.type, 
                h.province, h.city, h.county, h.filter, h.device_sale, h.service_charge";
        return $this->getDataAll($sql);
    }
    public function getHospitalDiagnosis($level, $reportHospital, $agency, $salesman)
    {
        $sql = 'select h.hospital_id, h.hospital_name
                from hospital as h 
                left join hospital_tree as t on h.hospital_id = t.hospital_id
                where h.type <> 1 ';
        if (!empty($level)) {
            $sql .= " and h.level in ($level) ";
        }
        if (!empty($reportHospital)) {
            $sql .= " and t.report_hospital = $reportHospital ";
        }
        if (!empty($agency)) {
            $sql .= " and h.agency = '$agency' ";
        }
        if (!empty($salesman)) {
            $sql .= " and h.salesman = '$salesman' ";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalGuardian($hospital, $device, $startTime, $endTime)
    {
        if (empty($hospital) || empty($device)) {
            return array();
        }
        $sql = 'select g.device_id, g.regist_time, p.patient_name, g.regist_doctor_name
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                where g.regist_hospital_id = :hospital and device_id = :device ';
        if (null !== $startTime) {
            $sql .= " and regist_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and regist_time <= '$endTime' ";
        }
        $sql .= ' order by regist_time desc';
        $param = [':hospital' => $hospital, ':device' => $device];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name, h.type, level, province, city, county, address, h.tel, 
                parent_flag, a.login_name, h.sms_tel, h.agency, h.agency_tel, h.salesman, h.comment, 
                h.contract_flag, h.device_sale, h.service_charge, h.display_check, h.report_must_check,
                invoice_name, invoice_id, invoice_addr_tel, invoice_bank, worker, filter, contact
                from hospital as h inner join account as a on h.hospital_id = a.hospital_id
                where h.hospital_id = :hospital_id and a.type = 1 limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalName($code)
    {
        $sql = "select hospital_id, hospital_name from hospital where `code` = '$code' limit 1";
        return $this->getDataRow($sql);
    }
    public function getHospitalLevel($level)
    {
        if (empty($level)) {
            return array();
        }
        $sql = 'select hospital_id from hospital where type <> 1 and level = :level';
        $param = [':level' => $level];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalList($type = '', $level = '', $salesman = '', $name = '', $offset = 0, $rows = null)
    {
        $sql = 'select h.hospital_id, hospital_name, h.tel, address, parent_flag, a.login_name, ifnull(d.quantity,0) as quantity
                from hospital as h left join account as a on h.hospital_id = a.hospital_id 
                left join (select hospital_id, count(device_id) as quantity from device group by hospital_id) as d 
                    on h.hospital_id = d.hospital_id
                where a.type = 1 ';
        if (!empty($type)) {
            $sql .= " and h.type = '$type' ";
        }
        if (!empty($level)) {
            $sql .= " and h.level = '$level' ";
        }
        if (!empty($salesman)) {
            $sql .= " and h.salesman = '$salesman' ";
        }
        if (!empty($name)) {
            $sql .= " and h.hospital_name like '%$name%' ";
        }
        $sql .= ' order by h.hospital_id ';
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalListHigh($hospitalId)
    {
        $sql = 'select distinct hospital_id, hospital_name from hospital 
                where type in (1,2) or hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalReport()
    {
        $sql = 'select distinct t.report_hospital as hospital_id, h.hospital_name 
                from hospital_tree as t inner join hospital as h on t.report_hospital = h.hospital_id
                where t.hospital_id <> t.report_hospital
                order by t.report_hospital';
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
    public function getHospitalTime($hospitalTime)
    {
        if (empty($hospitalTime)) {
            return array();
        }
        $sql = 'select hospital_id from hospital where type <> 1 and create_time > :time';
        $param = [':time' => $hospitalTime];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalTree($hospitalId)
    {
        $sql = "select hospital_id, analysis_hospital, report_hospital, title1, title2
                from hospital_tree where hospital_id = $hospitalId limit 1";
        return $this->getDataRow($sql);
    }
    public function getHospitalSalesman($salesman)
    {
        if (empty($salesman)) {
            return array();
        }
        $sql = 'select hospital_id from hospital where type <> 1 and salesman = :salesman';
        $param = [':salesman' => $salesman];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalWorder($user)
    {
        if (empty($user)) {
            return array();
        }
        $sql = "select hospital_id, hospital_name from hospital where worker = '$user'";
        return $this->getDataAll($sql);
    }
    public function getICCID($deviceId) {
        $sql = "select iccid from device where device_id = '$deviceId' limit 1";
        return $this->getDataString($sql);
    }
    public function getPatientDiagnosis($hospital, $diagnosis, $startTime, $endTime)
    {
        $sql = "select d.patient_id, d.diagnosis_id, d.create_time, h.hospital_id, h.hospital_name, h.tel as hospital_tel,
                p.patient_name as name, p.sex, year(now()) - p.birth_year as age, p.tel
                from patient_diagnosis as d 
                inner join guardian as g on d.patient_id = g.guardian_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                where g.regist_hospital_id in ($hospital) and d.diagnosis_id in ($diagnosis)";
        if (null !== $startTime) {
            $sql .= " and d.create_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and d.create_time <= '$endTime' ";
        }
        return $this->getDataAll($sql);
    }
    public function getPatientNotUpload($time)
    {
        $sql = "select g.guardian_id as patient_id, h.hospital_id, h.hospital_name, h.contact, h.tel, p.patient_name, g.start_time
                from guardian_data as d inner join guardian as g on d.guardian_id = g.guardian_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                where d.status < 2 and g.status = 2 and g.start_time >= '$time' and g.regist_hospital_id not in (1, 40)";
        return $this->getDataAll($sql);
    }
    public function getReportPatients($hospitalId)
    {
        $sql = "select g.guardian_id as patient_id, p.patient_name, start_time 
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                where d.status = 5 and g.regist_hospital_id = '$hospitalId' 
                and g.start_time > date_add(now(), interval -7 day) order by g.guardian_id desc";
    
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
    public function getSalesmanList()
    {
        $sql = 'select distinct salesman as `name` from hospital where salesman <> ""';
        return $this->getDataAll($sql);
    }
    public function getTotalDiagnosis($hospital, $startTime, $endTime)
    {
        $sql = "select count(*) as total from guardian 
                where regist_hospital_id in ($hospital) and regist_time >= '$startTime' and regist_time <= '$endTime'";
        return $this->getDataString($sql);
    }
    public function getUser($user)
    {
        $sql = 'select user, password, type, hospital_id from user_diagnosis where user = :user limit 1';
        $param = [':user' => $user];
        return $this->getDataRow($sql, $param);
    }
    public function notDisplayFirst($guardianId)
    {
        $sql = "update guardian set display_first = 0 where guardian_id = $guardianId";
        return $this->updateData($sql);
    }
    public function notFollow($hospitalId)
    {
        $sql = "update hospital set need_follow = 0 where hospital_id = $hospitalId";
        return $this->updateData($sql);
    }
    public function pdDelete($deviceId, $user, $version)
    {
        $sql = "insert into history_device (device_id, user, unbind_hospital_id) values ('$deviceId', '$user', 40)";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $sql = "delete from device where device_id = '$deviceId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function pdAbandon($deviceId, $user, $version)
    {
        $sql = "insert into history_device (device_id, hospital_id, user, unbind_hospital_id) values ('$deviceId', 9999, '$user', 40)";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
    
        $sql = "update device set hospital_id = 9999 where device_id = '$deviceId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function pdWarehouse($deviceId, $user, $version)
    {
        $sql = "insert into history_device (device_id, hospital_id, user, unbind_hospital_id) values ('$deviceId', 1, '$user', 40)";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
    
        $sql = "update device set hospital_id = 1, version = '$version' where device_id = '$deviceId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function setHospitalFilter($hospitalId, $filter)
    {
        $sql = "update hospital set filter = '$filter' where hospital_id = $hospitalId";
        return $this->updateData($sql);
    }
    public function updatePassword($user, $newPassword)
    {
        $sql = 'update user_diagnosis set password = :pwd where user = :user';
        $param = [':user' => $user, ':pwd' => $newPassword];
        return $this->updateData($sql, $param);
    }
    public function updateAnaticsStatus($guardianId, $status)
    {
        $sql = 'update guardian_data set status = :status where guardian_id = :id';
        $param = [':id' => $guardianId, ':status' => $status];
        return $this->updateData($sql, $param);
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
