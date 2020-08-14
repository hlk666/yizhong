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
    public function addAgency($name, $tel, $salesman = '0', $creator = '')
    {
        if($this->existData('agency', "agency_name = '$name'")) {
            return VALUE_DB_ERROR;
        }
        $sql = "insert into agency (agency_name, agency_tel, salesman_id, creator) values ('$name', '$tel', '$salesman', '$creator')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addAnswer($deviceId, $questionId, $text, $user, $status)
    {
        $this->beginTran();
        $sql = "insert into device_answer (question_id, text, creator) 
                values ('$questionId', '$text', '$user')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->rollBack();
            return VALUE_DB_ERROR;
        }
        $sql = "update device set status = '$status' where device_id = '$deviceId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->commit();
        return true;
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
    public function addCommunication($hospitalId, $type, $content, $creator, $deviceId, $patientId)
    {
        $sql = "insert into communication (hospital_id, type, content, creator, device_id, guardian_id)
        values ('$hospitalId', '$type', '$content', '$creator', '$deviceId', '$patientId')";
        return $this->insertData($sql);
    }
    public function addCommuTitleContent($agencyId, $hospitalId, $titleId, $title, $content, $nextTime, $status)
    {
        $this->beginTran();
        if (empty($titleId)) {
            $sql = "insert into commu_title (agency_id, hospital_id, title, next_time)
                    values ('$agencyId', '$hospitalId', '$title', '$nextTime')";
            $innerTitleId = $this->insertData($sql);
            if (VALUE_DB_ERROR === $innerTitleId) {
                $this->rollBack();
                return VALUE_DB_ERROR;
            }
        } else {
            $sql = "update commu_title set next_time = '$nextTime', status = '$status' where title_id = '$titleId'";
            $ret = $this->updateData($sql);
            if (VALUE_DB_ERROR === $ret) {
                $this->rollBack();
                return VALUE_DB_ERROR;
            }
            $innerTitleId = $titleId;
        }
        $sql = "insert into commu_content (content, title_id) values ('$content', '$innerTitleId')";
        $ret = $this->insertData($sql);
        $this->commit();
        return $innerTitleId;
    }
    public function addProblem($guardianId, $text)
    {
        $sql = "insert into problem (guardian_id, text, user_id) values ('$guardianId', '$text', '1')";
        return $this->insertData($sql);
    }
    /*
    public function addDevice($hospital, $device, $user = '')
    {
        if ($this->existData('device', "device_id = '$device'")) {
            $sql = "update device set hospital_id = '$hospital', agency_id = 0, salesman_id = 0 
            where device_id = '$device'";
        } else {
            $sql = "insert into device (device_id, hospital_id) values ('$device', '$hospital')";
        }
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $sql = "insert into history_device (device_id, hospital_id, user, action) 
                values ('$device', '$hospital', '$user', '新注册')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }*/
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
    public function addDevicePD($device, $user)
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
        
        $sql = "insert into history_device (device_id, hospital_id, user, unbind_hospital_id, content) 
                values ('$device', 40, '$user', 0, '创建新设备号')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        return true;
    }
    public function addDeviceResult($questionId, $result, $user)
    {
        $sql = "update device_question set result = '$result', result_user = '$user', result_time = now() 
                where question_id = '$questionId'";
        return $this->updateData($sql);
    }
    public function addQuestion($deviceId, $hospitalId, $text, $user)
    {
        $this->beginTran();
        $sql = "insert into device_question (device_id, hospital_id, text, creator) 
                values ('$deviceId', '$hospitalId', '$text', '$user')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->rollBack();
            return VALUE_DB_ERROR;
        }
        $sql = "update device set status = 1 where device_id = '$deviceId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->commit();
        return true;
    }
    public function addHospital($name, $type, $level, $tel, $province, $city, $county, $address, $parentFlag, $parentHospital, 
            $adminUser, $messageTel, $salesman, $comment, $analysisHospital, $reportHospital, $title1, $agency, 
            $contractFlag, $deviceSale, $displayCheck, $reportMustCheck, $invoiceName, $invoiceId, $invoiceAddressTel, 
            $invoiceBank, $creator, $double = '0', $deviceList = array(), $contact = '', $password = '123456', $emergencyTel = '')
    {
        $this->pdo->beginTransaction();
        $sql = "insert into hospital(hospital_name, type, level, tel, province, city, county, address, parent_flag, 
                sms_tel, comment, contract_flag, device_sale, display_check, report_must_check, 
                invoice_name, invoice_id, invoice_addr_tel, invoice_bank, creator, worker, contact, 
                agency_id, salesman_id, emergency_tel)
                values ('$name', '$type', '$level', '$tel', '$province', '$city', '$county', '$address', '$parentFlag', 
                '$messageTel', '$comment', '$contractFlag', '$deviceSale', '$displayCheck', 
                '$reportMustCheck', '$invoiceName', '$invoiceId', '$invoiceAddressTel', '$invoiceBank', '$creator', 
                '$creator', '$contact', '$agency', '$salesman', '$emergencyTel')";
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
        $realName = $name . '管理员';
        $dbPwd = md5($password);
        $sql = "insert into account(login_name, real_name, password, type, hospital_id)
                values ('$adminUser', '$realName', '$dbPwd', 1, $hospitalId)";
        $insertAccount = $this->insertData($sql);
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
                    $sql = "update device set hospital_id = $hospitalId, agency_id = 0, salesman_id = 0 
                    where device_id = '$deviceId'";
                } else {
                    $sql = "insert into device (device_id, hospital_id) values ('$deviceId', $hospitalId)";
                }
                $ret = $this->updateData($sql);
                if (VALUE_DB_ERROR === $ret) {
                    $this->pdo->rollBack();
                    return VALUE_DB_ERROR;
                }
                
                $sql = "insert into history_device (device_id, hospital_id, user, action) 
                        values ('$deviceId', $hospitalId, '$creator', '新注册')";
                $ret = $this->insertData($sql);
                if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
                }
            }
        }
        
        if (!empty($analysisHospital) && !empty($reportHospital) && !empty($title1)) {
            if (is_numeric($title1)) {
                $title = $title1;
            } else {
                $ret = $this->getHospitalName($title1);
                if (VALUE_DB_ERROR === $ret) {
                    $this->pdo->rollBack();
                    return VALUE_DB_ERROR;
                }
                $title = $ret['hospital_id'];
            }
            
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
        return $hospitalId;
    }
    public function addExamQuestion($id, $type, $miniType, $level, $url)
    {
        if (empty($id)) {
            $sql = "insert into exam_question (type, mini_type, level, url) 
                    values ('$type', '$miniType', '$level', '$url')";
        } else {
            $sql = "update exam_question 
                    set type = '$type', mini_type = '$miniType', level = '$level', url = '$url' 
                    where id = '$id'";
        }
        
        return $this->updateData($sql);
    }
    public function addHospitalParent($hospitalId, $parentHospital)
    {
        $sql = 'insert into hospital_relation(hospital_id, parent_hospital_id)
            values (:hospital_id, :parent_hospital_id)';
        $param = [':hospital_id' => $hospitalId, ':parent_hospital_id' => $parentHospital];
        return $this->insertData($sql, $param);
    }
    public function addNotice($guardianId, $notice)
    {
        $sql = "insert into history_notice (guardian_id, notice_text) values ('$guardianId', '$notice')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addSalesman($name)
    {
        if($this->existData('salesman', "salesman_name = '$name'")) {
            return VALUE_DB_ERROR;
        }
        $sql = "insert into salesman (salesman_name) values ('$name')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addShift($userId, $type)
    {
        $sql = "insert into shift (user_id, type) values ('$userId', '$type')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function addSolution($id, $question, $answer)
    {
        if (empty($id)) {
            $sql = "insert into solution (question, answer) values ('$question', '$answer')";
        } else {
            $sql = "update solution set answer = '$answer' where id = '$id'";
        }
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function appUploadStart($guardianId)
    {
        $sql = "insert into app_upload(guardian_id, type) values ('$guardianId', 1)";
        return $this->insertData($sql);
    }
    public function appUploadFailed($guardianId)
    {
        $sql = "insert into app_upload(guardian_id, type) values ('$guardianId', 3)";
        return $this->insertData($sql);
    }
    public function appUploadSucceed($guardianId)
    {
        $sql = "insert into app_upload(guardian_id, type) values ('$guardianId', 2)";
        return $this->insertData($sql);
    }
    public function checkDeviceDelivery($deviceId)
    {
        $sql = "select device_id, hospital_id from device where device_id = '$deviceId' limit 1";
        $ret = $this->getDataRow($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        if (empty($ret)) {
            return 1;
        }
        if ($ret['hospital_id'] == '40') {
            return 2;
        }
        return 0;
    }
    public function delAccount($doctorId)
    {
        $sql = "update account set hospital_id = 9999 where account_id = $doctorId";
        return $this->updateData($sql);
    }
    public function delDevice($deviceId, $hospital, $agency, $salesman, $user, $content = '', $action = '')
    {
        $oldHospitalId = $this->getDataString("select hospital_id from device where device_id = $deviceId limit 1");
        if (VALUE_DB_ERROR === $oldHospitalId) {
            return VALUE_DB_ERROR;
        }
        if ($oldHospitalId !== '') {
            $sql = "update device set hospital_id = $hospital, agency_id = $agency, salesman_id = $salesman where device_id = '$deviceId'";
        } else {
            $oldHospitalId = '0';
            $sql = "insert into device (device_id, hospital_id, agency_id, salesman_id) 
                    values ('$deviceId', $hospital, '$agency', '$salesman')";
        }
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        
        $sql = "insert into history_device (device_id, hospital_id, agency_id, salesman_id, user, unbind_hospital_id, content, action) 
                values ('$deviceId', $hospital, '$agency', '$salesman', '$user', $oldHospitalId, '$content', '$action')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function delExamQuestion($id)
    {
        $sql = "delete from exam_question where id = '$id'";
        return $this->deleteData($sql);
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
    public function editAgency($id, $name, $tel, $salesman = '0')
    {
        $sql = "update agency set agency_name = '$name', agency_tel = '$tel', salesman_id = '$salesman' where agency_id = $id";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function editHospital($hospitalId, $hospitalName, $type, $level, $hospitalTel, $province, $city, $county,
            $hospitalAddress, $parentFlag, $loginUser, $messageTel, $agency, $salesman, $comment, 
            $contractFlag, $deviceSale, $serviceCharge, $displayCheck, $reportMustCheck, 
            $invoiceName, $invoiceId, $invoiceAddressTel, $invoiceBank, $worker, $filter, $contact, $emergencyTel = '')
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
                parent_flag = '$parentFlag', sms_tel = '$messageTel', agency_id = '$agency', salesman_id = '$salesman',
                comment = '$comment', contract_flag = '$contractFlag', device_sale = '$deviceSale', 
                display_check = '$displayCheck', service_charge = '$serviceCharge', report_must_check = '$reportMustCheck',
                invoice_name = '$invoiceName', invoice_id = '$invoiceId', invoice_addr_tel = '$invoiceAddressTel', 
                invoice_bank = '$invoiceBank', worker = '$worker', filter = '$filter', contact = '$contact', 
                emergency_tel = '$emergencyTel'
                where hospital_id = '$hospitalId'";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return true;
    }
    public function editInvoiceEndDate($hospitalId, $date)
    {

        $sql = "update hospital set invoice_end_date = '$date' where hospital_id = $hospitalId";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
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
    public function editSalesman($id, $name)
    {
        $sql = "update salesman set salesman_name = '$name' where salesman_id = $id";
        $ret = $this->updateData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
        return true;
    }
    public function endRepeat($guardianId)
    {
        $sql = "update guardian set status = 2 where guardian_id = $guardianId";
        return $this->updateData($sql);
    }
    public function existedDevice($deviceId)
    {
        return $this->existData('device', "device_id = '$deviceId' and hospital_id <> 0");
    }
    public function existedDevice1($deviceId)
    {
        return $this->existData('device', "device_id = '$deviceId' and hospital_id > 1");
    }
    public function existedDevice2($deviceId)
    {
        return $this->existData('device', "device_id = '$deviceId'");
    }
    public function existedLoginName($loginName, $hospital)
    {
        return $this->existData('account', "login_name = '$loginName' and hospital_id <> $hospital");
    }
    
    public function getAccountInfo($id)
    {
        $sql = "select * from account where account_id = '$id' limit 1";
        return $this->getDataRow($sql);
    }
    public function getAccountList($hospital)
    {
        $sql = "select account_id as doctor_id, real_name as doctor_name from account 
        where hospital_id in ($hospital)";
        return $this->getDataAll($sql);
    }
    public function getAccountForAnalytics($doctorList, $startTime, $endTime, $isReportTime = false)
    {
        if ($isReportTime) {
            $and = "and d.report_time >= '$startTime' and d.report_time <= '$endTime'";
        } else {
            $and = "and g.regist_time >= '$startTime' and g.regist_time <= '$endTime'";
        }
        $sql = "select d.guardian_id as patient_id, p.patient_name, regist_hospital_id as hospital_id, d.report_time,
                g.start_time, g.end_time, d.status, a1.real_name as hbi_doctor, a2.real_name as report_doctor, 
                d.upload_time, d.download_end_time, d.report_time
                from guardian_data as d left join guardian as g on d.guardian_id = g.guardian_id
                left join patient as p on g.patient_id = p.patient_id
                left join account as a1 on d.hbi_doctor = a1.account_id
                left join account as a2 on d.report_doctor = a2.account_id
                where (hbi_doctor in ($doctorList) or report_doctor in ($doctorList)) $and ";
        return $this->getDataAll($sql);
    }
    public function getAdminAcount($loginName)
    {
        $sql = 'select account_id, real_name as name, type, password, hospital_id
                from account where login_name = :user and hospital_id = 1 limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getAgencyByName($name)
    {
        $sql = "select agency_tel from agency where agency_name = '$name' limit 1";
        return $this->getDataString($sql);
    }
    public function getAgencyByNameId($id, $name)
    {
        return $this->existData('agency', "agency_name = '$name' and agency_id <> $id");
    }
    public function getAgencyGuardian()
    {
        $sql = "select h.agency_id, ifnull(a.agency_name, '未知代理商') as agency_name, count(g.guardian_id) as qty
                from guardian as g inner join hospital as h on g.regist_hospital_id = h.hospital_id
                left join agency as a on h.agency_id = a.agency_id
                where g.regist_time > concat(DATE_FORMAT(date_add(now(), INTERVAL -1 DAY),'%Y-%m-%d'), ' 00:00:00')
                and g.regist_time < concat(DATE_FORMAT(now(),'%Y-%m-%d'), ' 00:00:00')
                and regist_hospital_id not in (1,40)
                group by h.agency_id, a.agency_name
                order by a.agency_name";
        return $this->getData($sql);
    }
    public function getAgencyInfo($id)
    {
        $sql = "select agency_name, agency_tel, salesman_id from agency where agency_id = $id limit 1";
        return $this->getDataRow($sql);
    }
    public function getAgencyList()
    {
        $sql = "select agency_id, agency_name as `name`, agency_tel, s.salesman_name 
                from agency as a left join salesman as s on a.salesman_id = s.salesman_id
                order by convert(agency_name using gbk) collate gbk_chinese_ci asc";
        return $this->getDataAll($sql);
    }
    public function getCloudData()
    {
        $sql = "select d.guardian_id, d.upload_time, d.`status`, a.real_name, d.report_time, h.hospital_name, d.type
                from guardian_data as d inner join guardian as g on d.guardian_id = g.guardian_id
                left join hospital as h on d.moved_hospital = h.hospital_id
                left join account as a on d.report_doctor = a.account_id
                where moved_hospital in (119, 132, 139, 140, 141, 743)
                and g.regist_time > concat(DATE_FORMAT(date_add(now(), INTERVAL -1 DAY),'%Y-%m-%d'), ' 00:00:00')
                and g.regist_time < concat(DATE_FORMAT(now(),'%Y-%m-%d'), ' 00:00:00')";
        return $this->getDataAll($sql);
    }
    public function getCommunication($hospitalId, $deviceId, $user, $patientId, $startTime, $endTime)
    {
        $sql = 'select * from communication where 1';
        if (!empty($hospitalId)) {
            $sql .= " and hospital_id = '$hospitalId'";
        }
        if (!empty($deviceId)) {
            $sql .= " and device_id = '$deviceId'";
        }
        if (!empty($user)) {
            $sql .= " and creator = '$user'";
        }
        if (!empty($patientId)) {
            $sql .= " and guardian_id in ($patientId)";
        }
        if (!empty($startTime)) {
            $sql .= " and create_time >= '$startTime'";
        }
        if (!empty($endTime)) {
            $sql .= " and create_time <= '$endTime'";
        }
        $sql .= ' order by create_time';
        return $this->getDataAll($sql);
    }
    public function getCommuTitle($agencyId, $hospital, $title, $status)
    {
        $sql = "select title_id, a.agency_name, h.hospital_name, title, t.create_time, t.next_time, t.status
                from commu_title as t left join agency as a on t.agency_id = a.agency_id
                left join hospital as h on t.hospital_id = h.hospital_id where 1 ";
        if (!empty($agencyId)) {
            $sql .= " and t.agency_id = '$agencyId' ";
        }
        if (!empty($hospital)) {
            //$sql .= " and h.hospital_name like '%$hospital%' ";
            $sql .= "and t.hospital_id = '$hospital'";
        }
        if (!empty($title)) {
            $sql .= " and t.title_id = '$title' ";
        }
        if (!empty($status)) {
            $sql .= " and t.status = '$status' ";
        }
        return $this->getDataAll($sql);
    }
    public function getCommuTitleContent($id)
    {
        $sql = "select t.title_id, t.title, t.create_time as title_time, t.next_time, t.`status`, 
                c.create_time as content_time, c.content, a.agency_name, h.hospital_name
                from commu_title as t inner join commu_content as c on t.title_id = c.title_id
                left join agency as a on t.agency_id = a.agency_id
                left join hospital as h on t.hospital_id = h.hospital_id
                where t.title_id = '$id'";
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
    public function getDataForQianyi()
    {
        $sql = 'select q.guardian_id, p.patient_name, p.birth_year, p.sex, g.start_time, 
                d.report_time, h.province, h.city, h.`level`, ifnull(g.guardian_result, "") as diagnose,
                g.regist_doctor_name, h.hospital_name
                from qianyi_data as q inner join guardian as g on q.guardian_id = g.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where q.send_time is null';
        return $this->getDataAll($sql);
    }
    public function getDataForQianyiTest()
    {
        $sql = 'select g.guardian_id, p.patient_name, p.birth_year, p.sex, g.start_time,
                d.report_time, h.province, h.city, h.`level`, ifnull(g.guardian_result, "") as diagnose,
                g.regist_doctor_name, h.hospital_name
                from guardian as g
                inner join patient as p on g.patient_id = p.patient_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where g.guardian_id = 20791';
        return $this->getDataAll($sql);
    }
    public function getDataForZhongdaDoctor($list)
    {
        $sql = "select account_id as doctor_id, real_name as doctor_name, tel as doctor_tel, idc as  doctor_idc
                from account where account_id in ($list)";
        return $this->getDataAll($sql);
    }
    public function getDataForZhongdaHospital($list)
    {
        $sql = "select hospital_id, hospital_name, contact, tel as hospital_tel from hospital where hospital_id in ($list)";
        return $this->getDataAll($sql);
    }
    public function getDataForZhongdaRegist()
    {
        $sql = 'select g.guardian_id, g.regist_hospital_id as hospital_id, p.patient_name, p.birth_year, p.sex, 
                p.tel as patient_tel, g.idc as patient_idc, g.start_time
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                inner join zhongda_data as z on z.guardian_id = g.guardian_id
                where z.status = 0';
        return $this->getDataAll($sql);
    }
    public function getDataForZhongdaReport()
    {
        $sql = 'select g.guardian_id, a.hospital_id as report_hospital_id, a.account_id as doctor_id, 
                d.report_time, ifnull(g.guardian_result, "") as diagnosis
                from guardian as g
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                inner join account as a on d.report_doctor = a.account_id
                inner join zhongda_data as z on z.guardian_id = g.guardian_id
                where z.status = 2';
        return $this->getDataAll($sql);
    }
    public function getDataNotUpload()
    {
        $sql = 'select g.guardian_id as patient_id, h.hospital_name, p.patient_name, g.start_time, g.end_time
                from guardian_data as d 
                inner join guardian as g on d.guardian_id = g.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where d.status < 2 and g.`status` = 2 and g.end_time > date_add(now(), interval -2 day)
                and g.end_time < date_add(now(), interval -1 day) 
                and g.guardian_id not in (select guardian_id from guardian_error where notice_flag = 1)';
        return $this->getDataAll($sql);
    }
    public function getDepartment()
    {
        $sql = 'select d.department_id, d.hospital_id, h1.hospital_name as department_name, h2.hospital_name as hospital_name
                from department as d left join hospital as h1 on d.department_id = h1.hospital_id
                left join hospital as h2 on d.hospital_id = h2.hospital_id';
        return $this->getDataAll($sql);
    }
    public function getDeviceAgency($agency, $salesman)
    {
        if (!empty($agency)) {
            $and = " and d.agency_id = '$agency'";
        } elseif (!empty($salesman)) {
            $and = " and d.salesman_id = '$salesman'";
        } else {
            $and = '';
        }
        $sql = "select device_id, d.agency_id, a.agency_name, d.salesman_id, s.salesman_name from device as d
                left join agency as a on d.agency_id = a.agency_id
                left join salesman as s on d.salesman_id = s.salesman_id 
                where hospital_id = 0 $and";
        return $this->getDataAll($sql);
    }
    public function getDeviceAnswer($questionId)
    {
        $sql = "select q.device_id, q.text as question, a.text as answer, answer_time, ac.real_name, 
                timestampdiff(HOUR, q.question_time, a.answer_time) as time_diff
                from device_answer as a inner join device_question as q on a.question_id = q.question_id
                left join account as ac on a.creator = ac.account_id
                where a.question_id = '$questionId'";
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
        $sql = "select ifnull(hospital_name, '-') as hospital_name, device_id, d.hospital_id,
                ifnull(a.agency_name, a1.agency_name) as agency, ifnull(s.salesman_name, s1.salesman_name) as salesman, 
                ver_phone, ver_embedded, ver_app, ver_pcb, ver_box
                from device as d
                left join agency as a on d.agency_id = a.agency_id
                left join salesman as s on d.salesman_id = s.salesman_id
                left join hospital as h on d.hospital_id = h.hospital_id
                left join agency as a1 on h.agency_id = a1.agency_id
                left join salesman as s1 on h.salesman_id = s1.salesman_id
                where d.device_id like '%$id'";
        return $this->getDataAll($sql);
    }
    public function getDeviceCommunication($device, $startTime)
    {
        $sql = "select g.guardian_id as patient_id, g.start_time, g.end_time, g.device_id, c.content, c.create_time
                from guardian as g left join communication as c on c.guardian_id = g.guardian_id
                where g.device_id = '$device' and g.regist_time > '$startTime'
                order by g.guardian_id desc, c.create_time desc";
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
    public function getDeviceHistory($device)
    {
        $sql = "select d.device_id, d.bk_time, d.content, ac.real_name, 
                ifnull(h.hospital_name,ifnull(a.agency_name,ifnull(s.salesman_name,'不明'))) as position
                from history_device as d 
                left join hospital as h on d.hospital_id = h.hospital_id
                left join agency as a on d.agency_id = a.agency_id
                left join salesman as s on d.salesman_id = s.salesman_id
                left join account as ac on d.user = ac.login_name
                where d.device_id = '$device'";
        return $this->getDataAll($sql);
    }
    public function getDeviceHospital($device)
    {
        $sql = "select d.device_id, h.hospital_id, hospital_name, h.agency_id, h.salesman_id, a.agency_name, s.salesman_name, tel, a.agency_tel
                from hospital as h inner join device as d on h.hospital_id = d.hospital_id
                left join agency as a on d.agency_id = a.agency_id
                left join salesman as s on d.salesman_id = s.salesman_id
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
    public function getDeviceLastGuardian($deviceList)
    {
        $sql = "select device_id, max(regist_time) as last_time from guardian 
        where device_id in ($deviceList) group by device_id";
        return $this->getDataAll($sql);
    }
    public function getDeviceLastHistory($hospital, $deviceList)
    {
        $sql = "select device_id, max(bk_time) as bind_time from history_device 
        where hospital_id = $hospital and device_id in ($deviceList) group by device_id";
        return $this->getDataAll($sql);
    }
    public function getDeviceList($hospital, $offset = 0, $rows = null)
    {
        if (empty($hospital)) {
            return array();
        }
        $sql = "select hospital_name, device_id, a.agency_name as agency, s.salesman_name as salesman, 
                ver_phone, ver_embedded, ver_app, ver_pcb, ver_box, d.hospital_id,
                case h.device_sale when 1 then '投放' when 2 then '销售' when 3 then '押金' else '其他' end as device_sale
                from device as d
                inner join hospital as h on d.hospital_id = h.hospital_id 
                left join agency as a on h.agency_id = a.agency_id
                left join salesman as s on h.salesman_id = s.salesman_id
                where d.hospital_id = $hospital";
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
        $sql = "select '-' as hospital_name, device_id, a.agency_name as agency, s.salesman_name as salesman, 
                ver_phone, ver_embedded, ver_app, ver_pcb, ver_box, '-' as device_sale, '0' as hospital_id
                from device as d
                left join agency as a on d.agency_id = a.agency_id
                left join salesman as s on d.salesman_id = s.salesman_id
                where a.agency_name like '%$agency%' or s.salesman_name like '%$agency%'";
        if (null !== $rows) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getDeviceListPD()
    {
        $sql = "select device_id, iccid from device where hospital_id = 40 order by device_id desc";
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
    public function getDeviceQuestion($device)
    {
        $sql = "select question_id, device_id, text, question_time, a.real_name, h.hospital_name
                from device_question as q inner join account as a on q.creator = a.account_id
                inner join hospital as h on q.hospital_id = h.hospital_id
                where q.device_id = '$device'";
        return $this->getDataAll($sql);
    }
    public function getDeviceByStatus($status)
    {
        $sql = "select hospital_name, d.device_id, a.agency_name as agency, s.salesman_name as salesman, 
                ver_phone, ver_embedded, ver_app, ver_pcb, ver_box, d.hospital_id,
                case h.device_sale when 1 then '投放' when 2 then '销售' when 3 then '押金' else '其他' end as device_sale
                from device as d
                inner join device_question as q on d.device_id = q.device_id
                left join hospital as h on q.hospital_id = h.hospital_id 
                left join agency as a on h.agency_id = a.agency_id
                left join salesman as s on h.salesman_id = s.salesman_id
                where d.status = '$status'";
        return $this->getDataAll($sql);
    }
    public function getDeviceQuestionAnswer()
    {
        $sql = "select q.question_id, q.hospital_id, q.device_id, d.`status`, q.text as question, q.question_time, 
                a.text as answer, a.answer_time, q.result, q.result_time
                from device_question as q left join device_answer as a on q.question_id = a.question_id
                left join device as d on q.device_id = d.device_id";
        return $this->getData($sql);
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
    public function getEcgLast($guardians)
    {
        $sql = "select guardian_id, max(create_time) as alert_time from ecg 
                where guardian_id in ($guardians) group by guardian_id";
        return $this->getDataAll($sql);
    }
    public function getEcgMark($startTime = null, $endTime = null)
    {
        $sql = "select * from ecg where mark <> 0";
        if (!empty($startTime)) {
            $sql .= " and create_time >= '$startTime'";
        }
        if (!empty($endTime)) {
            $sql .= " and create_time <= '$endTime'";
        }
        return $this->getDataAll($sql);
    }
    public function getEcgs($startTime, $endTime, $exceptHospitalList)
    {
        $sql = "select ecg_id, e.guardian_id, alert_flag, create_time
                from ecg as e left join guardian as g on e.guardian_id = g.guardian_id
                where create_time >= '$startTime' and create_time <= '$endTime' ";
        if (!empty($exceptHospitalList)) {
            $sql .= " and regist_hospital_id not in ($exceptHospitalList)";
        }
        return $this->getDataAll($sql);
    }
    public function getEcgActive()
    {
        $sql = 'select guardian_id, max(create_time) as alert_time from ecg where create_time > date_add(now(), interval -1 hour) group by guardian_id';
        return $this->getDataAll($sql);
    }
    public function getExamQuestion($count, $type, $level)
    {
        $sql = "select * from exam_question where 1 ";
        if (!empty($type)) {
            $sql .= " and type = '$type' ";
        }
        if (!empty($level)) {
            $sql .= " and level = '$level' ";
        }
        if (!empty($count)) {
            $sql .= " order by rand() limit $count";
        }
        return $this->getDataAll($sql);
    }
    public function getExamQuestionQty($type)
    {
        if ($type == 'type') {
            $sql = 'select type, count(id) as qty from exam_question group by type';
        } else {
            $sql = 'select type, mini_type, count(id) as qty from exam_question group by type, mini_type';
        }
        return $this->getDataAll($sql);
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
    public function getGuardiansOn()
    {
        $sql = 'select guardian_id, mode from guardian 
                where status = 1 and regist_hospital_id not in (1,40) and regist_time > date_add(now(), interval -3 day)';
        return $this->getDataAll($sql);
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
    public function getHistoryDeviceByHospital($hospitals, $startTime, $endTime)
    {
        $sql = "select distinct * from history_device 
                where (hospital_id in ($hospitals) or unbind_hospital_id in ($hospitals))
                and bk_time between '$startTime' and '$endTime'";
        return $this->getDataAll($sql);
    }
    public function getHospitalAgency($agency)
    {
        if (empty($agency)) {
            return array();
        }
        $sql = "select distinct hospital_id, hospital_name from hospital where type <> 1 and agency_id = '$agency'";
        return $this->getDataAll($sql);
    }
    public function getHospitalAgencyList()
    {
        $sql = 'select h.hospital_id, h.hospital_name, a.agency_name 
                from hospital as h left join agency as a on h.agency_id = a.agency_id 
                where h.type <> 1';
        return $this->getDataAll($sql);
    }
    public function getHospitalArea($province)
    {
        $sql = "select h.hospital_id, h.hospital_name, a.agency_name
                from hospital as h left join agency as a on h.agency_id = a.agency_id
                where h.type <> 1 and h.province = '$province'";
        return $this->getDataAll($sql);
    }
    public function getHospitalDevice($startTime, $endTime, $province)
    {
        $where = '';
        if (!empty($startTime)) {
            $where .= "and h.create_time >= '$startTime' ";
        }
        if (!empty($endTime)) {
            $where .= "and h.create_time <= '$endTime' ";
        }
        if (!empty($province)) {
            $where .= "and h.province = '$province' ";
        }
        $sql = "select h.hospital_id, h.hospital_name, h.salesman_id, h.agency_id, 
                a.agency_name, s.salesman_name, h.create_time, 
                h.type, h.province, h.city, h.county, h.device_sale, h.service_charge, 
                count(d.device_id) as device_count, h.filter, h.device_sale, h.worker
                from hospital as h left join device as d on h.hospital_id = d.hospital_id
                left join agency as a on h.agency_id = a.agency_id
                left join salesman as s on h.salesman_id = s.salesman_id
                where 1 $where 
                group by h.hospital_id, h.hospital_name, h.salesman_id, h.agency_id, 
                a.agency_name, s.salesman_name, h.create_time, h.type, 
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
            $sql .= " and h.agency_id = '$agency' ";
        }
        if (!empty($salesman)) {
            $sql .= " and h.salesman_id = '$salesman' ";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalGuardian($hospital, $device, $startTime, $endTime)
    {
        $sql = "select h.hospital_name, g.device_id, g.regist_time, p.patient_name, g.regist_doctor_name, g.guardian_id
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                left join hospital as h on g.regist_hospital_id = h.hospital_id
                where device_id = '$device' ";
        if (!empty($hospital)) {
            $sql .= " and regist_hospital_id = '$hospital' ";
        }
        if (null !== $startTime) {
            $sql .= " and regist_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and regist_time <= '$endTime' ";
        }
        $sql .= ' order by regist_time desc';
        return $this->getDataAll($sql);
    }
    public function getHospitalGuardianAgency($agencyId)
    {
        $sql = "select h.hospital_name, count(g.guardian_id) as qty
                from guardian as g inner join hospital as h on g.regist_hospital_id = h.hospital_id
                where g.regist_time > concat(DATE_FORMAT(date_add(now(), INTERVAL -1 DAY),'%Y-%m-%d'), ' 00:00:00')
                and g.regist_time < concat(DATE_FORMAT(now(),'%Y-%m-%d'), ' 00:00:00')
                and regist_hospital_id not in (1,40)
                and h.agency_id = '$agencyId'
                group by h.hospital_name";
        return $this->getData($sql);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name, h.type, level, province, city, county, address, h.tel, 
                parent_flag, a.login_name, h.sms_tel, h.agency_id, h.salesman_id, h.comment, 
                h.contract_flag, h.device_sale, h.service_charge, h.display_check, h.report_must_check,
                invoice_name, invoice_id, invoice_addr_tel, invoice_bank, worker, filter, contact, emergency_tel
                from hospital as h inner join account as a on h.hospital_id = a.hospital_id
                where h.hospital_id = :hospital_id and a.type = 1 limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalInvoice($hospitalId)
    {
        $sql = "select invoice_end_date from hospital where hospital_id = $hospitalId limit 1";
        return $this->getDataString($sql);
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
    public function getHospitalList($type = '', $level = '', $salesman = '', $name = '', $offset = 0, $rows = null, $id = '')
    {
        $sql = 'select h.hospital_id, hospital_name, h.tel, address, parent_flag, a.login_name, ifnull(d.quantity,0) as quantity
                from hospital as h left join account as a on h.hospital_id = a.hospital_id 
                left join (select hospital_id, count(device_id) as quantity from device group by hospital_id) as d 
                    on h.hospital_id = d.hospital_id
                where a.type = 1 ';
        if (!empty($id)) {
            $sql .= " and h.hospital_id = '$id' ";
        }
        if (!empty($type)) {
            $sql .= " and h.type = '$type' ";
        }
        if (!empty($level)) {
            $sql .= " and h.level = '$level' ";
        }
        if (!empty($salesman)) {
            $sql .= " and h.salesman_id = '$salesman' ";
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
        $sql = "select hospital_id from hospital where type <> 1 and salesman_id = $salesman";
        return $this->getDataAll($sql);
    }
    public function getICCID($deviceId) {
        $sql = "select iccid from device where device_id = '$deviceId' limit 1";
        return $this->getDataString($sql);
    }
    public function getNotice($hospitalName, $guardianId, $patientName) {
        $sql = "select h.hospital_name, p.patient_name, n.guardian_id, n.notice_text, n.notice_time
                from history_notice as n inner join guardian as g on n.guardian_id = g.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                inner join hospital as h on regist_hospital_id = h.hospital_id
                where 1";
        if (!empty($hospitalName)) {
            $sql .= " and h.hospital_name like '%$hospitalName%' ";
        }
        if (!empty($patientName)) {
            $sql .= " and p.patient_name like '%$patientName%' ";
        }
        if (!empty($guardianId)) {
            $sql .= " and n.guardian_id = '$guardianId' ";
        }
        return $this->getDataAll($sql);
    }
    public function getNoticeRule($hospitals)
    {
        $sql = 'select hospital_id, invoice_bank as notice_rule from hospital where 1';
        if (!empty($hospitals)) {
            $sql .= " and hospital_id in ($hospitals)";
        }
        return $this->getDataAll($sql);
    }
    public function getGuardianByReportKeyword($keyword, $startTime, $endTime)
    {
        $sql = "select g.guardian_id as patient_id, p.patient_name, d.report_time, a.real_name as doctor_name, 
                regist_hospital_id as hospital_id, a.hospital_id as report_hospital_id
                from guardian as g
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                inner join patient as p on g.patient_id = p.patient_id
                left join account as a on d.report_doctor = a.account_id
                where guardian_result like '%$keyword%' ";
        if (!empty($startTime)) {
            $sql .= " and d.report_time >= '$startTime' ";
        }
        if (!empty($endTime)) {
            $sql .= " and d.report_time <= '$endTime' ";
        }
        return $this->getDataAll($sql);
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
    public function getPatientFuzy($name) {
        $sql = "select patient_id, patient_name from patient where patient_name like '%$name%' order by create_time desc";
        return $this->getDataAll($sql);
    }
    public function getPatientStatus($patient = '0', $guardian = '0') {
        if (!empty($patient)) {
            $where = 'where g.patient_id = ' . $patient;
        }
        if (!empty($guardian)) {
            $where = 'where g.guardian_id = ' . $guardian;
        }
        $sql = "select g.guardian_id, p.patient_name, h1.hospital_id, h1.hospital_name, g.start_time, g.end_time, g.device_id, g.`mode`, 
                d.`status` as upload_status, h2.hospital_name as moved_hospital_name, d.type as moved_type, d.report_time, 
                a1.real_name as hbi_doctor, a2.real_name as report_doctor, a3.real_name as download_doctor_name, d.url
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                inner join hospital as h1 on g.regist_hospital_id = h1.hospital_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                left join hospital as h2 on d.moved_hospital = h2.hospital_id
                left join account as a1 on d.hbi_doctor = a1.account_id
                left join account as a2 on d.report_doctor = a2.account_id
                left join account as a3 on d.download_doctor = a3.account_id
                $where order by g.guardian_id desc limit 1";
        return $this->getDataRow($sql);
    }
    public function getPatientStatusByName($name) {
        $sql = "select g.guardian_id, p.patient_name, h1.hospital_id, h1.hospital_name, g.start_time, g.end_time, g.device_id, g.`mode`,
                d.`status` as upload_status, h2.hospital_name as moved_hospital_name, d.type as moved_type, d.report_time,
                a1.real_name as hbi_doctor, a2.real_name as report_doctor, a3.real_name as download_doctor_name
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                inner join hospital as h1 on g.regist_hospital_id = h1.hospital_id
                inner join guardian_data as d on g.guardian_id = d.guardian_id
                left join hospital as h2 on d.moved_hospital = h2.hospital_id
                left join account as a1 on d.hbi_doctor = a1.account_id
                left join account as a2 on d.report_doctor = a2.account_id
                left join account as a3 on d.download_doctor = a3.account_id
                where p.patient_name like '%$name%' order by p.patient_name, g.guardian_id desc";
        return $this->getDataAll($sql);
    }
    public function getProblem()
    {
        $sql = 'select problem_id, guardian_id as patient_id, text, create_time, update_time, user_id as user, status, text
                from problem where create_time > date_add(now(), interval -24 hour)';
        return $this->getDataAll($sql);
    }
    public function getRelation()
    {
        $sql = 'select r1.hospital_id as h1, r1.parent_hospital_id as h2, r2.parent_hospital_id as h3, r3.parent_hospital_id as h4
                from hospital_relation as r1
                left join hospital_relation as r2 on r1.parent_hospital_id = r2.hospital_id
                left join hospital_relation as r3 on r2.parent_hospital_id = r3.hospital_id
                order by r1.hospital_id';
        return $this->getDataAll($sql);
    }
    public function getRelationChild()
    {
        $sql = 'select parent_hospital_id, GROUP_CONCAT(hospital_id) as child from hospital_relation
                group by parent_hospital_id ';
        return $this->getDataAll($sql);
    }
    public function getRelationChildName($id)
    {
        $sql = "select hospital_id, hospital_name from hospital where hospital_id in ($id)";
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
    public function getReportResult($hospital, $startTime, $endTime)
    {
        $sql = "select g.guardian_id, g.guardian_result, d.report_time, a.real_name as doctor
                from guardian as g inner join guardian_data as d on g.guardian_id = d.guardian_id
                inner join account as a on d.report_doctor = a.account_id
                where d.`status` in (5, 8) and a.hospital_id in ($hospital)";
        if (!empty($startTime)) {
            $sql .= " and d.report_time > '$startTime' ";
        }
        if (!empty($endTime)) {
            $sql .= " and d.report_time < '$endTime' ";
        }
        return $this->getDataAll($sql);
    }
    public function getSalesmanAgency($id)
    {
        $sql = "select * from agency where salesman_id = '$id'";
        return $this->getDataAll($sql);
    }
    public function getSalesmanByName($name)
    {
        $sql = "select 1 from salesman where salesman_name = '$name' limit 1";
        return $this->getDataString($sql);
    }
    public function getSalesmanByNameId($id, $name)
    {
        return $this->existData('salesman', "salesman_name = '$name' and salesman_id <> $id");
    }
    public function getSalesmanData($id, $startTime = null, $endTime = null, $offset = 0, $rows = null)
    {
        if (empty($id)) {
            return array();
        }
        $sql = "select h.hospital_name, p.patient_name, regist_time, g.regist_doctor_name as doctor_name
                from guardian as g inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                where regist_hospital_id in (select hospital_id from hospital where salesman_id = $id) ";
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
        
        return $this->getDataAll($sql);
    }
    public function getSalesmanInfo($id)
    {
        $sql = "select salesman_name from salesman where salesman_id = $id limit 1";
        return $this->getDataRow($sql);
    }
    public function getSalesmanList()
    {
        $sql = "select salesman_id, salesman_name as `name` from salesman
                order by convert(salesman_name using gbk) collate gbk_chinese_ci asc";
        return $this->getDataAll($sql);
    }
    public function getSolution()
    {
        $sql = "select * from solution";
        return $this->getDataAll($sql);
    }
    public function getTotalDiagnosis($hospital, $startTime, $endTime)
    {
        $sql = "select count(*) as total from guardian 
                where regist_hospital_id in ($hospital) and regist_time >= '$startTime' and regist_time <= '$endTime'";
        return $this->getDataString($sql);
    }
    public function getTutorGuardian($tutorId, $startTime, $endTime)
    {
        $sql = "select g.guardian_id, g.device_id, g.regist_hospital_id, g.start_time, g.end_time, d.status,
                p.patient_name, p.sex, p.birth_year, p.tel, g.regist_doctor_name as doctor_name
                from guardian as g left join patient as p on g.patient_id = p.patient_id 
                left join guardian_data as d on g.guardian_id = d.guardian_id
                where d.tutor_doctor = '$tutorId'";
        if (!empty($startTime)) {
            $sql .= " and d.report_time >= '$startTime'";
        }
        if (!empty($endTime)) {
            $sql .= " and d.report_time <= '$endTime'";
        }
        return $this->getDataAll($sql);
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
    public function pdDelete($deviceId, $user, $iccid, $vPhone, $vEmbedded, $vApp, $vPcb, $vBox)
    {
        $sql = "insert into history_device (device_id, user, unbind_hospital_id, content) values ('$deviceId', '$user', 40, '注销设备号')";
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
    public function pdAbandon($deviceId, $user, $iccid, $vPhone, $vEmbedded, $vApp, $vPcb, $vBox)
    {
        $sql = "insert into history_device (device_id, hospital_id, user, unbind_hospital_id, content) 
                values ('$deviceId', 9999, '$user', 40, '移入废品库')";
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
    public function pdWarehouse($deviceId, $user, $iccid, $vPhone, $vEmbedded, $vApp, $vPcb, $vBox)
    {
        $sql = "insert into history_device (device_id, hospital_id, user, unbind_hospital_id, content) 
                values ('$deviceId', 1, '$user', 40, '移入成品库')";
        $ret = $this->insertData($sql);
        if (VALUE_DB_ERROR === $ret) {
            return VALUE_DB_ERROR;
        }
    
        $sql = "update device set hospital_id = 1, iccid = '$iccid', ver_phone = '$vPhone', 
                ver_embedded = '$vEmbedded', ver_app = '$vApp', ver_pcb = '$vPcb', ver_box = '$vBox'
                where device_id = '$deviceId'";
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
    public function updateNoticeRule($hospitalId, $text)
    {
        $sql = "update hospital set invoice_bank = '$text' where hospital_id = '$hospitalId'";
        return $this->updateData($sql);
    }
    public function updateProblem($problemId, $userId, $status)
    {
        $sql = "update problem set update_time = now(), user_id = '$userId', status = '$status' 
                where problem_id = '$problemId'";
        return $this->updateData($sql);
    }
    public function updateQianyiData($guardianId)
    {
        $sql = "update qianyi_data set send_time = now() where guardian_id = $guardianId";
        return $this->updateData($sql);
    }
    public function updateZhongdaData($guardianId, $status)
    {
        $sql = "update zhongda_data set status = '$status' where guardian_id = '$guardianId'";
        return $this->updateData($sql);
    }
}
