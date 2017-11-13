<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class Dbi extends BaseDbi
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
        $sql = 'select ecg_id from difference';
        $diffEcgId = $this->getDataString($sql);
        if (VALUE_DB_ERROR === $diffEcgId) {
            return VALUE_DB_ERROR;
        }
        
        if ($guardianId > $diffEcgId) {
            $table = 'ecg';
        } else {
            $table = 'ecg_history';
        }
        
        $where = ' guardian_id = ' . $guardianId;
        if ($readStatus != null) {
            $where .= " and read_status = $readStatus ";
        }
        return $this->countData($table, $where);
    }
    //************************* existed methods(public) *************************
    //********************************** start **********************************
    public function existedDeviceHospital($deviceId, $hospitalId)
    {
        return $this->existData('device', " device_id = $deviceId and hospital_id = $hospitalId ");
    }
    public function existedEcg($ecgId)
    {
        return $this->existData('ecg', ' ecg_id = ' . $ecgId);
    }
    public function existedLoginName($loginName)
    {
        return $this->existData('account', 'login_name = "' . $loginName . '"');
    }
    public function existedGuardian($guardianId)
    {
        return $this->existData('guardian', 'guardian_id = ' . $guardianId);
    }
    public function existedOldMode($guardianId, $oldMode)
    {
        return $this->existData('guardian', " guardian_id = $guardianId and mode = $oldMode ");
    }
    //************************* existed methods(public) *************************
    //*********************************** end ***********************************
    
    //************************** query methods(public) **************************
    //********************************** start **********************************
    public function getAcount($loginName)
    {
        $sql = 'select account_id, real_name as name, type, password, hospital_id 
                from account where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function getAcountById($doctorId)
    {
        $sql = 'select account_id, login_name as user, real_name as name, type, password, hospital_id
                from account where account_id = :account_id limit 1';
        $param = [':account_id' => $doctorId];
        return $this->getDataRow($sql, $param);
    }
    public function getAgencyHospitals($agency)
    {
        $sql = 'select hospital_id, hospital_name from hospital where agency = :agency';
        $param = [':agency' => $agency];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationRequest($hospitalId, $allFlag, $requestHospital, $startTime, $endTime)
    {
        $sql = 'select consultation_id, h.hospital_name, guardian_id as patient_id, ecg_id, 
                request_message, request_time, response_message, response_time
                from consultation as c left join hospital as h on c.request_hospital_id = h.hospital_id
                where response_hospital_id = :hospital_id ';
        if (0 == $allFlag) {
            $sql .= ' and status = 1 ';
        }
        if (null !== $requestHospital) {
            $sql .= ' and request_hospital_id = ' . $requestHospital;
        }
        if (null !== $startTime) {
            $sql .= " and request_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and request_time <= '$endTime' ";
        }
        $sql .= ' order by consultation_id desc ';
        
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationResponse($hospitalId, $allFlag, $responseHospital, $startTime, $endTime)
    {
        $sql = 'select consultation_id, h.hospital_name, guardian_id as patient_id, ecg_id, 
                response_message, response_time, request_message, request_time
                from consultation as c left join hospital as h on c.response_hospital_id = h.hospital_id
                where request_hospital_id = :hospital_id ';
        if (1 == $allFlag) {
            $sql .= ' and status >= 2';
        } else {
            $sql .= ' and status = 2';
        }
        if (null !== $responseHospital) {
            $sql .= ' and response_hospital_id = ' . $responseHospital;
        }
        if (null !== $startTime) {
            $sql .= " and response_time >= '$startTime' ";
        }
        if (null !== $endTime) {
            $sql .= " and response_time <= '$endTime' ";
        }
        $sql .= ' order by consultation_id desc ';
        
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationById($consultationId)
    {
        $sql = 'select request_hospital_id, response_hospital_id
                from consultation where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId];
        return $this->getDataRow($sql, $param);
    }
    public function getDataStatus($guardianId)
    {
        $sql = 'select status from guardian_data where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getDeviceId($guardianId)
    {
        $sql = 'select device_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataString($sql, $param);
    }
    public function getDeviceList($hospitalId)
    {
        $sql = 'select device_id from device where hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getDeviceStatusHistory($deviceId)
    {
        $sql = 'select device_id, create_time, phone_power, collection_power, bluetooth, line
                from history_app_status where device_id = :id';
        $param = [':id' => $deviceId];
        return $this->getDataAll($sql, $param);
    }
    public function getDiagnosisByGuardian($guardianId)
    {
        $sql = 'select ecg_id from difference';
        $diffEcgId = $this->getDataString($sql);
        if (VALUE_DB_ERROR === $diffEcgId) {
            return VALUE_DB_ERROR;
        }
        
        if ($guardianId > $diffEcgId) {
            $table = 'ecg';
        } else {
            $table = 'ecg_history';
        }
        
        $sql = "select d.ecg_id, d.content, d.content_parent, d.create_time as content_time, e.data_path
                from diagnosis as d left join $table as e on d.ecg_id = e.ecg_id
                where d.guardian_id = :guardian_id";
        $param = [':guardian_id' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getDownloadData($guardianId)
    {
        $sql = 'select guardian_id, url, upload_time, download_start_time, download_end_time, device_type 
                from guardian_data where guardian_id = :guardian_id and url <> "" limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getEcgIdFromDiagnosis($guardianId)
    {
        $sql = 'select ecg_id from diagnosis where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getDoctorList($hospitalId)
    {
        $sql = 'select account_id as doctor_id, login_name as user, real_name as doctor_name, type
                from account where hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    
    public function getEcgs($guardianId, $offset, $rows, $readStatus)
    {
        $sql = 'select ecg_id from difference';
        $diffEcgId = $this->getDataString($sql);
        if (VALUE_DB_ERROR === $diffEcgId) {
            return VALUE_DB_ERROR;
        }
        
        if ($guardianId > $diffEcgId) {
            $table = 'ecg';
        } else {
            $table = 'ecg_history';
        }
        
        $sql = "select ecg_id, alert_flag, create_time, read_status, data_path, mark 
                from $table where guardian_id = :guardian ";
        if ($readStatus != null) {
            $sql .= " and read_status = $readStatus ";
        }
        $sql .= " order by mark desc, ecg_id desc limit $offset, $rows";
        
        $param = [':guardian' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getEcgsHospital($hospitalId, $startTime)
    {
        $sql = 'select ecg_id, alert_flag, e.create_time, data_path
                from guardian as g inner join ecg as e on g.guardian_id = e.guardian_id 
                where g.guard_hospital_id = :hospital and read_status = 0 and start_time >= :time 
                order by ecg_id desc';
        $param = [':hospital' => $hospitalId, ':time' => $startTime];
        return $this->getDataAll($sql, $param);
    }
    public function getEcgsByTime($guardianId, $lastTime)
    {
        $sql = "select ecg_id, alert_flag, create_time, read_status, data_path, mark
                from ecg where guardian_id = :guardian and create_time > '$lastTime'";
        $param = [':guardian' => $guardianId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardianByDevice($deviceId)
    {
        $sql = 'select guardian_id, patient_id, status from guardian
                where device_id = :device_id and status < 2 
                order by guardian_id desc limit 1';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardianById($guardianId)
    {
        $sql = 'select patient_id, status, mode, ifnull(guardian_result, \'\') as result 
                from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getGuardians($hospitalId, $offset, $rows, $mode = null, $status = null, 
            $name = null, $tel = null, $sTime = null, $eTime = null, 
            $device = null, $registHospitalId = null, $doctorName = null)
    {
        $sql = 'select g.guardian_id, g.mode, g.status, g.mark, g.device_id, g.regist_hospital_id, 
                p.patient_name, p.sex, p.birth_year, p.tel, g.start_time, g.end_time, 
                g.blood_pressure, g.tentative_diagnose, g.medical_history, g.hospitalization_id, 
                g.lead, g.regist_doctor_name as doctor_name, g.sickroom
                from guardian as g left join patient as p on g.patient_id = p.patient_id ';
        if ($hospitalId === 0) {
            $sql .= ' where 1 ';
        } else {
            $sql .= ' where guard_hospital_id in (' . $hospitalId . ')'; 
        }
        if ($mode != null) {
            $sql .= " and g.mode = $mode ";
        }
        if ($status != null) {
            $sql .= " and g.status in ($status) ";
        }
        if ($device != null) {
            $sql .= " and g.device_id = $device ";
        }
        if ($name != null) {
            $sql .= " and p.patient_name = '$name' ";
        }
        if ($tel != null) {
            $sql .= " and p.tel = '$tel' ";
        }
        if ($sTime != null) {
            $sql .= " and g.start_time >= '$sTime' ";
        }
        if ($eTime != null) {
            $sql .= " and g.start_time <= '$eTime' ";
        }
        if ($registHospitalId != null) {
            $sql .= " and g.regist_hospital_id in ($registHospitalId) ";
        }
        if ($doctorName != null) {
            $sql .= " and g.regist_doctor_name = '$doctorName' ";
        }
        $sql .= " order by g.guardian_id desc limit $offset, $rows";
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardiansByRegist($hospitalId, $offset, $rows, $mode = null, $status = null,
            $name = null, $tel = null, $sTime = null, $eTime = null, $device = null, $doctorName = null)
    {
        $sql = 'select g.guardian_id, g.mode, g.status, g.mark, g.device_id, g.regist_hospital_id, 
                p.patient_name, p.sex, p.birth_year, p.tel, g.start_time, g.end_time,
                g.blood_pressure, g.tentative_diagnose, g.medical_history, g.hospitalization_id, 
                g.lead, g.regist_doctor_name as doctor_name, g.sickroom
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where regist_hospital_id = ' . $hospitalId;
        if ($mode != null) {
            $sql .= " and g.mode = $mode ";
        }
        if ($status != null) {
            $sql .= " and g.status in ($status) ";
        }
        if ($device != null) {
            $sql .= " and g.device_id = $device ";
        }
        if ($name != null) {
            $sql .= " and p.patient_name = '$name' ";
        }
        if ($tel != null) {
            $sql .= " and p.tel = '$tel' ";
        }
        if ($sTime != null) {
            $sql .= " and g.start_time >= '$sTime' ";
        }
        if ($eTime != null) {
            $sql .= " and g.start_time <= '$eTime' ";
        }
        if ($doctorName != null) {
            $sql .= " and g.regist_doctor_name = $doctorName ";
        }
        $sql .= " order by g.guardian_id desc limit $offset, $rows";
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getGuardianError()
    {
        $sql = 'select h.hospital_name, p.patient_name, e.guardian_id, e.create_time, content 
                from guardian_error as e inner join guardian as g on e.guardian_id = g.guardian_id
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                where notice_flag = 0';
        return $this->getDataAll($sql);
    }
    public function getHospitalByDevice($diviceId)
    {
        $sql = 'select hospital_id from device where device_id = :device limit 1';
        $param = [':device' => $diviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalByGuardian($guardianId)
    {
        $sql = 'select guard_hospital_id from guardian where guardian_id = :guardian limit 1';
        $param = [':guardian' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, parent_flag, sms_tel, upload_flag
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalList()
    {
        $sql = 'select hospital_id, hospital_name, tel, level, device_sale from hospital';
        return $this->getDataAll($sql);
    }
    public function getHospitalChild($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.hospital_id
                where r.parent_hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalParent($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.parent_hospital_id
                where r.hospital_id = :hospital_id';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getInfoByDevice($deviceId)
    {
        $sql = 'select g.regist_hospital_id as hopital_id, h.hospital_name, h.tel as hospitl_tel, 
                p.patient_name, p.tel as patient_tel from guardian as g 
                inner join hospital as h on g.regist_hospital_id = h.hospital_id
                inner join patient as p on g.patient_id = p.patient_id
                where g.device_id = :device_id
                order by g.guardian_id desc limit 1;';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatient($patientId)
    {
        $sql = 'select patient_id, patient_name, sex, birth_year, tel, address
                from patient where patient_id = :patient_id limit 1';
        $param = [':patient_id' => $patientId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatientByNameAndTel($name, $tel)
    {
        if (null == $name && null == $tel) {
            return VALUE_DB_ERROR;
        }
        
        if (null != $name && null != $tel) {
            $where = " where patient_name LIKE '$name%' and tel LIKE '$tel%'";
        } elseif (null != $name) {
            $where = " where patient_name LIKE '$name%'";
        } else {
            $where = " where tel LIKE '$tel%'";
        }
        $sql = "select patient_name, tel, start_time, CONCAT('http://101.200.174.235/', report_file) as report_url
                from patient as p left join guardian as g on p.patient_id = g.patient_id $where order by guardian_id desc";
        return $this->getDataAll($sql);
    }
    public function getPatientByDevice($deviceId)
    {
        $sql = 'select guardian_id, patient_name, mode
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where device_id = :device_id and status < 2 order by guardian_id desc limit 1';
        $param = [':device_id' => $deviceId];
        return $this->getDataRow($sql, $param);
    }
    public function getRegistInfo($guardianId)
    {
        $sql = 'select g.mode, g.lead, p.patient_name as name, p.birth_year, p.sex, p.tel,
                tentative_diagnose, medical_history, regist_hospital_id, guard_hospital_id,
                device_id, guardian_hours, regist_doctor_name as doctor_name, g.hospitalization_id,
                height, weight, blood_pressure, sickroom, family_tel, start_time
                from guardian as g left join patient as p on g.patient_id = p.patient_id
                where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->getDataRow($sql, $param);
    }
    public function getRepeatPatient($hospital, $name)
    {
        $sql = 'select g.device_id, g.start_time, g.regist_doctor_name as doctor_name
                from guardian as g inner join patient as p on g.patient_id = p.patient_id
                where g.regist_hospital_id = :hospital and p.patient_name = :name and g.status = 1 
                and g.regist_time > date_add(now(), interval "-12" hour)';
        $param = [':hospital' => $hospital, ':name' => $name];
        return $this->getDataAll($sql, $param);
    }
    //************************** query methods(public) **************************
    //*********************************** end ***********************************
    
    //************************* execute methods(public) *************************
    //********************************** start **********************************
    public function addAccount($loginName, $realName, $tel, $password, $type, $hospitalId, $creator)
    {
        $sql = 'insert into account (login_name, real_name, tel, password, type, hospital_id, creator)
                values (:login_name, :real_name, :tel, :password, :type, :hospital_id, :creator)';
        $param = [':login_name' => $loginName, ':real_name' => $realName, ':tel' => $tel, ':password' => $password,
                        ':type' => $type, ':hospital_id' => $hospitalId,':creator' => $creator ];
        return $this->insertData($sql, $param);
    }
    public function addDeviceStatus($deviceId, $phonePower, $collectionPower, $bluetooth, $line)
    {
        $sql = 'insert into history_app_status (device_id, phone_power, collection_power, bluetooth, line)
                values (:device_id, :phone_power, :collection_power, :bluetooth, :line)';
        $param = [':device_id' => $deviceId, ':phone_power' => $phonePower, ':collection_power' => $collectionPower,
                        ':bluetooth' => $bluetooth, ':line' => $line];
        return $this->insertData($sql, $param);
    }
    public function changeMode($guardianId, $oldMode, $newMode)
    {
        $this->pdo->beginTransaction();
        
        $sql = 'update guardian set mode = :new where guardian_id = :id';
        $param = [':id' => $guardianId, ':new' => $newMode];
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'insert into history_mode (guardian_id, old_mode, new_mode) values (:id, :old, :new)';
        $param = [':id' => $guardianId, ':old' => $oldMode, ':new' => $newMode];
        $ret = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return true;
    }
    public function addGuardError($guardianId, $content)
    {
        $sql = 'insert into guardian_error (guardian_id, content) values (:id, :content)';
        $param = [':id' => $guardianId, ':content' => $content];
        return $this->insertData($sql, $param);
    }
    public function delEcg($ecgId)
    {
        $sql = 'delete from ecg where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->deleteData($sql, $param);
    }
    public function editGuardian($guardianId, array $data)
    {
        return $this->updateTableByKey('guardian', 'guardian_id', $guardianId, $data);
    }
    public function editPatient($patientId, array $data)
    {
        return $this->updateTableByKey('patient', 'patient_id', $patientId, $data);
    }
    public function flowConsultationEnd($idList)
    {
        $sql = 'update consultation set status = 3 where consultation_id in ' . $idList;
        return $this->updateData($sql);
    }
    public function flowConsultationReply($consultationId, $result)
    {
        $sql = 'update consultation set status = 2, response_message = :result, response_time = now()
                where consultation_id = :consultation_id';
        $param = [':consultation_id' => $consultationId, ':result' => $result];
        return $this->updateData($sql, $param);
    }
    public function flowConsultationApply($guardianId, $requestHospital, $responseHospital, $ecgId, $mesage)
    {
        $sql = 'insert into consultation(guardian_id, ecg_id, request_hospital_id, request_message, response_hospital_id, status)
                values (:guardian, :ecg, :request_hospital_id, :request_message, :response_hospital_id, 1)';
        $param = [
                        ':guardian' => $guardianId,
                        ':ecg' => $ecgId, 
                        ':request_hospital_id' => $requestHospital, 
                        ':request_message' => $mesage,
                        ':response_hospital_id' => $responseHospital
        ];
        return $this->insertData($sql, $param);
    }
    public function flowGuardianAddDiagnosis($ecgId, $guardianId, $doctorId, $content, $type)
    {
        if ($type == 1) {
            $contentName = 'content';
        } else {
            $contentName = 'content_parent';
        }
        
        if ($this->existData('diagnosis', ' ecg_id = ' . $ecgId)) {
            $sql = "update diagnosis set guardian_id = :guardian, doctor_id = :doctor, $contentName = :content
             where ecg_id = :ecg";
        } else {
            $sql = "insert into diagnosis (ecg_id, guardian_id, doctor_id, $contentName)
                values (:ecg, :guardian, :doctor, :content)";
        }
        
        $param = [':ecg' => $ecgId, ':guardian' => $guardianId, ':doctor' => $doctorId, ':content' => $content];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianAddEcg($guardianId, $alertFlag, $time, $dataPath)
    {
        $sql = 'insert into ecg(guardian_id, alert_flag, create_time, data_path) values(:guardian, :alert, :time, :path)';
        $param = [':guardian' => $guardianId, ':alert' => $alertFlag, ':time' => $time, ':path' => $dataPath];
        return $this->insertData($sql, $param);
    }
    public function flowGuardianAddResult($guardianId, $result)
    {
        $sql = 'update guardian set guardian_result = :result, status = 3 where guardian_id = :guardian';
        $param = [':guardian' => $guardianId, ':result' => $result];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianAddUser($patientName, $sex, $age, $tel, $device, $registHospital,
            $guardHospital, $mode, $hours, $lead, $doctor, $sickRoom, $bloodPressure, $height,
            $weight, $familyTel, $tentativeDiagnose, $medicalHistory, $registDoctorName, 
            $hospitalizationId = '0', $startTime = null)
    {
        $birthYear = date('Y') - $age;
        $sql = 'select patient_id from patient
                where patient_name = :name and birth_year = :birth and tel = :tel limit 1';
        $param = [':name' => $patientName, ':birth' => $birthYear, ':tel' => $tel];
        $patientId = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $patientId) {
            return VALUE_DB_ERROR;
        }
        $this->pdo->beginTransaction();
        //if patient not existed, add to patient table.
        if ('' == $patientId) {
            $sql = 'insert into patient(patient_name, sex, birth_year, tel, address)
                    values(:name, :sex, :birth, :tel, :address)';
            $param = [':name' => $patientName, ':sex' => $sex, ':birth' => $birthYear,
                            ':tel' => $tel, ':address' => $sickRoom];
            $patientId = $this->insertData($sql, $param);
            if (VALUE_DB_ERROR === $patientId) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        $sql = 'insert into guardian(device_id, regist_hospital_id, guard_hospital_id,
                    patient_id, mode, guardian_hours, lead, doctor_id, status,
                    sickroom, blood_pressure, height, weight, family_tel,
                    tentative_diagnose, medical_history, regist_doctor_name, hospitalization_id)
                    values (:device, :regist_hospital, :guard_hospital, :patient, :mode,
                    :hours, :lead, :doctor_id, 1, :sickroom, :blood_pressure, :height,
                    :weight, :family_tel, :ten_dia, :medical_history, :doctor_name, :hospitalization_id)';
        $param = [':device' => $device, ':regist_hospital' => $registHospital, ':guard_hospital' => $guardHospital,
                        ':patient' => $patientId, ':mode' => $mode, ':hours' => $hours, ':lead' => $lead,
                        ':doctor_id' => $doctor, ':sickroom' => $sickRoom, ':blood_pressure' => $bloodPressure,
                        ':height' => $height, ':weight' => $weight, ':family_tel' => $familyTel,
                        ':ten_dia' => $tentativeDiagnose, ':medical_history' => $medicalHistory,
                        ':doctor_name' => $registDoctorName, ':hospitalization_id' => $hospitalizationId];
        $guardianId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $guardianId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        if ($startTime != null) {
            $sql = 'update guardian set start_time = :start_time where guardian_id = :guardian_id';
            $param = [':start_time' => $startTime, ':guardian_id' => $guardianId];
            $ret = $this->updateData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
        }
        $this->pdo->commit();
        return $guardianId;
    }
    public function flowGuardianDelete($guardianId)
    {
        $ret = $this->backupData('guardian', 'history_guardian', 'guardian_id', $guardianId);
        if (true !== $ret) {
            return VALUE_DB_ERROR;
        } else {
            $this->pdo->beginTransaction();
            
            $sql = 'delete from guardian where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId];
            $ret = $this->deleteData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
            
            $sql = 'delete from ecg where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId];
            $ret = $this->deleteData($sql, $param);
            if (VALUE_DB_ERROR === $ret) {
                $this->pdo->rollBack();
                return VALUE_DB_ERROR;
            }
            $this->pdo->commit();
            return true;
        }
    }
    public function flowGuardianEndGuard($guardianId)
    {
        $sql = 'update guardian set status = 2, end_time = now() where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianPrintReport($guardianId)
    {
        $sql = 'update guardian set status = 4 where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianReadEcg($ecgId)
    {
        $sql = 'update ecg set read_status = 1 where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId];
        return $this->updateData($sql, $param);
    }
    public function flowGuardianStartGuard($guardianId)
    {
        $sql = 'select regist_hospital_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        $hospitalId = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId || '' == $hospitalId) {
            return VALUE_DB_ERROR;
        }
        $sql = 'select analysis_hospital from hospital_tree where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        $analysisHospital = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $analysisHospital) {
            return VALUE_DB_ERROR;
        }
        if (empty($analysisHospital)) {
            $analysisHospital = $hospitalId;
        }
        
        $this->pdo->beginTransaction();
        $sql = 'insert into guardian_data(guardian_id, url, status, moved_hospital) values (:guardian, "", 1, :movedHospital)';
        $param = [':guardian' => $guardianId, ':movedHospital' => $analysisHospital];
        $guardianId = $this->insertData($sql, $param);
        if (VALUE_DB_ERROR === $guardianId) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        
        $sql = 'update guardian set status = 1, start_time = now() where guardian_id = :guardian_id';
        $param = [':guardian_id' => $guardianId];
        $this->updateData($sql, $param);
        
        $this->pdo->commit();
        return true;
    }
    public function markEcg($ecgId, $mark)
    {
        $sql = 'update ecg set mark = :mark where ecg_id = :ecg_id';
        $param = [':ecg_id' => $ecgId, ':mark' => $mark];
        return $this->updateData($sql, $param);
    }
    public function markPatient($guardianId, $mark)
    {
        $sql = 'update guardian set mark = :mark where guardian_id = :guardian';
        $param = [':guardian' => $guardianId, ':mark' => $mark];
        return $this->updateData($sql, $param);
    }
    public function noticeDownloadData($guardianId, array $data)
    {
        return $this->updateTableByKey('guardian_data', 'guardian_id', $guardianId, $data);
    }
    public function noticeGuardianError($guardianId)
    {
        $sql = 'update guardian_error set notice_flag = 1 where guardian_id = :id';
        $param = [':id' => $guardianId];
        return $this->updateData($sql, $param);
    }
    public function updatePassword($user, $newPwd)
    {
        $sql = 'update account set password = :pwd where login_name = :user';
        $param = [':user' => $user, ':pwd' => $newPwd];
        return $this->updateData($sql, $param);
    }
    //************************* execute methods(public) *************************
    //*********************************** end ***********************************
}
