<?php
require_once PATH_ROOT . 'lib/db/BaseDbi.php';

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
        $where = ' guardian_id = ' . $guardianId;
        if ($readStatus != null) {
            $where .= " and read_status = $readStatus ";
        }
        return $this->countData('ecg', $where);
    }
    public function existedConsultation($consultationId)
    {
        return $this->existData('consultation', ['consultation_id' => $consultationId]);
    }
    public function existedFollow($followId)
    {
        return $this->existData('follow', ['follow_id' => $followId]);
    }
    public function existedReferral($referralId)
    {
        return $this->existData('referral', ['referral_id' => $referralId]);
    }
    public function existedUser($user)
    {
        return $this->existData('user', ['login_name' => $user]);
    }
    public function getCaseCase($caseId)
    {
        $sql = 'select name as case_name, sex, birth_year, tel, diagnosis as treat_diagnosis, info, img_cbc, 
                img_myocardial_markers, img_serum_electrolytes, img_echocardiography, img_ecg, img_holter, 
                create_time as treat_time from `case` where case_id = :case_id limit 1';
        $param = [':case_id' => $caseId];
        return $this->getDataRow($sql, $param);
    }
    public function getCaseConsultation($caseId)
    {
        $sql = 'select h1.hospital_name as apply_hospital_name, u1.real_name as apply_doctor_name, u1.tel as apply_doctor_tel,
                c.apply_time, c.apply_message, 
                h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                c.reply_time, c.diagnosis as reply_diagnosis, c.advice as reply_advice
                from consultation as c inner join hospital as h1 on c.apply_hospital_id = h1.hospital_id
                inner join user as u1 on c.apply_user_id = u1.user_id
                inner join hospital as h2 on c.reply_hospital_id = h2.hospital_id
                left join user as u2 on c.reply_user_id = u2.user_id
                where case_id = :case_id ';
        $param = [':case_id' => $caseId];
        return $this->getDataAll($sql, $param);
    }
    public function getCaseReferral($caseId)
    {
        $sql = 'select h1.hospital_name as apply_hospital_name, u1.real_name as apply_doctor_name, u1.tel as apply_doctor_tel,
                r.apply_time, r.apply_message,
                h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                r.reply_time, r.reply_message, r.expect_time, 
                u3.real_name as confirm_doctor_name, u3.tel as confirm_doctor_tel, r.confirm_time, r.advice as reply_advice,
                u4.real_name as discharge_doctor_name, u4.tel as discharge_doctor_tel,
                r.operate_time, operate_info, r.course, r.diagnosis as discharge_diagnosis, r.instructions, r.medicine, r.advice
                from referral as r inner join hospital as h1 on r.apply_hospital_id = h1.hospital_id
                inner join user as u1 on r.apply_user_id = u1.user_id
                inner join hospital as h2 on r.reply_hospital_id = h2.hospital_id
                left join user as u2 on r.reply_user_id = u2.user_id
                left join user as u3 on r.confirm_user_id = u3.user_id
                left join user as u4 on r.discharge_user_id = u4.user_id
                where case_id = :case_id ';
        $param = [':case_id' => $caseId];
        return $this->getDataAll($sql, $param);
    }
    public function getCaseFollow($caseId)
    {
        $sql = 'select h1.hospital_name as apply_hospital_name, u1.real_name as apply_doctor_name, u1.tel as apply_doctor_tel,
                f.follow_time, f.symptom, f.advice as advice, f.question, 
                img_ecg, img_holter, img_echocardiography, img_inr, img_other,
                h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                f.reply_time, f.reply_advice, f.reply_question
                from follow as f inner join hospital as h1 on f.follow_hospital_id = h1.hospital_id
                inner join user as u1 on f.follow_user_id = u1.user_id
                inner join hospital as h2 on f.discharge_hospital_id = h2.hospital_id
                left join user as u2 on f.reply_user_id = u2.user_id
                where case_id = :case_id ';
        $param = [':case_id' => $caseId];
        return $this->getDataAll($sql, $param);
    }
    public function getCaseListAll($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select case_id, name as case_name, sex, birth_year, tel, create_date as treat_date, diagnosis
                from `case` where hospital_id = :hospital order by case_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getCaseListConsultation($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select distinct c.case_id, name as case_name, sex, birth_year, c.tel, c.create_date as treat_date, 
                c.diagnosis, h.hospital_name as consultation_hospital_name
                from `case` as c inner join consultation as cn on c.case_id = cn.case_id
                inner join hospital as h on cn.reply_hospital_id = h.hospital_id
                where c.hospital_id = :hospital order by consultation_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getCaseListReferral($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select distinct c.case_id, name as case_name, sex, birth_year, c.tel, c.create_date as treat_date,
                c.diagnosis, h.hospital_name as referral_hospital_name
                from `case` as c inner join referral as r on c.case_id = r.case_id
                inner join hospital as h on r.reply_hospital_id = h.hospital_id
                where c.hospital_id = :hospital order by referral_id desc';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationList($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select distinct c.case_id, name as case_name, sex, birth_year, c.tel, c.create_date as treat_date,
                c.diagnosis, h.hospital_name as apply_hospital_name
                from consultation as cn inner join `case` as c on cn.case_id = c.case_id
                inner join hospital as h on cn.apply_hospital_id = h.hospital_id
                where cn.reply_hospital_id = :hospital ';
        $param = [':hospital' => $hospitalId];
        /*
        if (null !== $status) {
            $sql .= ' and r.status = :status';
            $param[':status'] = $status;
        }*/
        $sql .= ' order by consultation_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql, $param);
    }
    
    public function getConsultationApply($hospitalId)
    {
        $sql = 'select c.consultation_id, c.case_id, h.hospital_name as apply_hospital_name, 
                c.apply_message, c.apply_time, c.reply_time
                from consultation as c inner join hospital as h on c.apply_hospital_id = h.hospital_id
                where reply_hospital_id = :hospital_id order by consultation_id desc';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getConsultationInfo($consultationId)
    {
        
        $sql = 'select ca.name, ca.sex, ca.birth_year, ca.diagnosis as apply_diagnosis,
                c.apply_message, h1.hospital_name as apply_hospital_name, u1.real_name as apply_doctor_name, u1.tel as apply_doctor_tel,
                c.apply_time, h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                c.reply_time, c.diagnosis as reply_diagnosis, c.advice as reply_advice
                from consultation as c inner join `case` as ca on c.case_id = ca.case_id
                inner join hospital as h1 on c.apply_hospital_id = h1.hospital_id
                inner join user as u1 on c.apply_user_id = u1.user_id
                inner join hospital as h2 on c.reply_hospital_id = h2.hospital_id
                left join user as u2 on c.reply_user_id = u2.user_id
                where consultation_id = :consultation  limit 1';
        $param = [':consultation' => $consultationId];
        return $this->getDataRow($sql, $param);
    }
    public function getConsultationReply($hospitalId)
    {
        $sql = 'select c.consultation_id, c.case_id, h.hospital_name as reply_hospital_name, c.diagnosis, c.advice, c.reply_time
                from consultation as c inner join hospital as h on c.reply_hospital_id = h.hospital_id
                where apply_hospital_id = :hospital_id and reply_user_id is not null order by reply_time desc';
        $param = [':hospital_id' => $hospitalId];
        
        return $this->getDataAll($sql, $param);
    }
    public function getFollowInfo($followId)
    {
        $sql = 'select c.name, c.sex, c.birth_year, c.diagnosis as apply_diagnosis,
                h1.hospital_name as follow_hospital_name, u1.real_name as follow_doctor_name, u1.tel as follow_doctor_tel,
                follow_time, symptom, advice, question, img_ecg, img_holter, img_echocardiography, img_inr, img_other,
                h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                reply_time, reply_advice, reply_question
                from follow as f inner join `case` as c on f.case_id = c.case_id
                inner join hospital as h1 on f.follow_hospital_id = h1.hospital_id
                inner join user as u1 on f.follow_user_id = u1.user_id
                inner join hospital as h2 on f.discharge_hospital_id = h2.hospital_id
                left join user as u2 on f.reply_user_id = u2.user_id
                where follow_id = :follow limit 1';
        $param = [':follow' => $followId];
    
        return $this->getDataRow($sql, $param);
    }
    public function getFollowListDischarge($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select distinct c.case_id, name as case_name, sex, birth_year, c.tel, c.create_date as treat_date,
                c.diagnosis, r.reply_hospital_id as discharge_hospital_id, h.hospital_name as discharge_hospital_name
                from referral as r inner join `case` as c on r.case_id = c.case_id
                inner join hospital as h on r.reply_hospital_id = h.hospital_id
                where apply_hospital_id = :hospital and discharge_time is not null order by referral_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getFollowListFollow($hospitalId, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select distinct c.case_id, name as case_name, sex, birth_year, c.tel, c.create_date as treat_date,
                c.diagnosis, f.follow_id, f.follow_time, f.follow_hospital_id, h.hospital_name as follow_hospital_name
                from follow as f inner join `case` as c on f.case_id = c.case_id
                inner join hospital as h on f.follow_hospital_id = h.hospital_id
                where discharge_hospital_id = :hospital order by f.follow_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getFollowReply($hospitalId)
    {
        $sql = 'select follow_id, case_id, hospital_name as reply_hospital_name, reply_time, reply_advice, reply_question 
                from follow as f inner join hospital as h on f.discharge_hospital_id = h.hospital_id
                where follow_hospital_id = :hospital_id and reply_user_id is not null order by reply_time desc';
        $param = ['hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, sms_tel
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalList($offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select hospital_id, hospital_name, tel from hospital order by hospital_id ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql);
    }
    public function getHospitalParent($hospitalId)
    {
        $sql = 'select h.hospital_id, hospital_name from hospital as h
                inner join hospital_relation as r on h.hospital_id = r.parent_hospital_id
                where r.hospital_id = :hospital';
        $param = [':hospital' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getInfo($table, $field, $value)
    {
        $sql = "select * from `$table` where $field = '$value' limit 1";
        return $this->getDataRow($sql);
    }
    public function getReferralApply($hospitalId)
    {
        $sql = 'select r.referral_id, r.case_id, h.hospital_name as apply_hospital_name, r.apply_message, r.apply_time
                from referral as r inner join hospital as h on r.apply_hospital_id = h.hospital_id
                where reply_hospital_id = :hospital_id order by referral_id desc';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getReferralInfo($referralId)
    {
        $sql = 'select c.name, c.sex, c.birth_year, c.diagnosis as apply_diagnosis,
                r.apply_message, h1.hospital_name as apply_hospital_name, u1.real_name as apply_doctor_name, u1.tel as apply_doctor_tel,
                r.apply_time, h2.hospital_name as reply_hospital_name, u2.real_name as reply_doctor_name, u2.tel as reply_doctor_tel,
                r.reply_time, r.reply_message, r.expect_time, r.status
                from referral as r inner join `case` as c on r.case_id = c.case_id
                inner join hospital as h1 on r.apply_hospital_id = h1.hospital_id
                inner join user as u1 on r.apply_user_id = u1.user_id
                inner join hospital as h2 on r.reply_hospital_id = h2.hospital_id
                left join user as u2 on r.reply_user_id = u2.user_id
                where referral_id = :referral  limit 1';
        $param = [':referral' => $referralId];
        return $this->getDataRow($sql, $param);
    }
    public function getReferralList($hospitalId, $status, $offset = VALUE_DEFAULT_OFFSET, $rows = VALUE_DEFAUTL_ROWS)
    {
        $sql = 'select distinct c.case_id, name as case_name, sex, birth_year, c.tel, c.create_date as treat_date,
                c.diagnosis, h.hospital_name as apply_hospital_name
                from referral as r inner join `case` as c on r.case_id = c.case_id
                inner join hospital as h on r.apply_hospital_id = h.hospital_id
                where r.reply_hospital_id = :hospital ';
        $param = [':hospital' => $hospitalId];
        if (null !== $status) {
            $sql .= ' and r.status = :status';
            $param[':status'] = $status;
        }
        $sql .= ' order by r.referral_id desc ';
        if (VALUE_DEFAULT_OFFSET !== $offset) {
            $sql .= " limit $offset, $rows";
        }
        return $this->getDataAll($sql, $param);
    }
    public function getReferralReply($hospitalId)
    {
        $sql = 'select r.referral_id, r.case_id, h.hospital_name as reply_hospital_name, r.reply_message, r.reply_time, r.expect_time
                from referral as r inner join hospital as h on r.reply_hospital_id = h.hospital_id
                where apply_hospital_id = :hospital_id and reply_user_id is not null order by reply_time desc';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataAll($sql, $param);
    }
    public function getUserInfo($loginName)
    {
        $sql = 'select user_id, real_name as user_name, type as user_type, password, hospital_id
                from user where login_name = :user limit 1';
        $param = [':user' => $loginName];
        return $this->getDataRow($sql, $param);
    }
    public function addHospital($name, $tel, $address, $messageTel)
    {
        $sql = 'insert into hospital (hospital_name, tel, address, sms_tel)
                values (:name, :tel, :address, :sms_tel)';
        $param = [':name' => $name, ':tel' => $tel, ':address' => $address, ':sms_tel' => $messageTel];
        return $this->insertData($sql, $param);
    }
    public function addHospitalRelation($parentHospitalId, $childHospitalId)
    {
        $sql = 'insert into hospital_relation (hospital_id, parent_hospital_id) values (:child, :parent)';
        $param = [':child' => $childHospitalId, ':parent' => $parentHospitalId];
        return $this->insertData($sql, $param);
    }
    public function addCase($hospitalId, $name, $sex, $birthYear, $tel, $diagnosis, $info, 
            $imgCBC, $imgMyocardialMarkers, $imgSerumElectrolytes, $imgEchocardiography, $imgEcg, $imgHolter)
    {
        $sql = 'insert into `case` (hospital_id, name, sex, birth_year, tel, diagnosis, info, 
                img_cbc, img_myocardial_markers, img_serum_electrolytes, img_echocardiography, img_ecg, img_holter)
                values (:hospital, :name, :sex, :birth, :tel, :diagnosis, :info, :cbc, :mm, :se, :eg, :e, :h)';
        $param = [':hospital' => $hospitalId, ':name' => $name, ':sex' => $sex, ':birth' => $birthYear, ':tel' => $tel, 
                        ':diagnosis' => $diagnosis, ':info' => $info, ':cbc' => $imgCBC, ':mm' => $imgMyocardialMarkers, 
                        ':se' => $imgSerumElectrolytes, ':eg' => $imgEchocardiography, ':e' => $imgEcg, ':h' => $imgHolter];
        return $this->insertData($sql, $param);
    }
    public function addFollow($followHospitalId, $dischargeHospitalId, $caseId, $followUserId, $symptom, $advice, $question, 
            $imgEcg, $imgHolter, $imgEchocardiography, $imgInr, $imgOther)
    {
        $sql = 'insert into follow (follow_hospital_id, discharge_hospital_id, case_id, follow_user_id, 
                symptom, advice, question, img_ecg, img_holter, img_echocardiography, img_inr, img_other)
                values (:follow_hospital_id, :discharge_hospital_id, :case_id, 
                :follow_user_id, :symptom, :advice, :question, :e, :h, :eg, :inr, :other)';
        $param = [':follow_hospital_id' => $followHospitalId, ':discharge_hospital_id' => $dischargeHospitalId, 
                        ':case_id' => $caseId, ':follow_user_id' => $followUserId, ':symptom' => $symptom, 
                        ':advice' => $advice, ':question' => $question, ':e' => $imgEcg, ':h' => $imgHolter, 
                        ':eg' => $imgEchocardiography, ':inr' => $imgInr, ':other' => $imgOther];
        return $this->insertData($sql, $param);
    }
    public function addUser($loginUser, $name, $password, $type, $tel, $hospitalId)
    {
        $sql = 'insert into user (login_name, real_name, password, type, tel, hospital_id)
                values (:login_name, :real_name, :password, :type, :tel, :hospital_id)';
        $param = [':login_name' => $loginUser, ':real_name' => $name, ':password' => $password,
                        ':type' => $type, ':tel' => $tel, ':hospital_id' => $hospitalId];
        return $this->insertData($sql, $param);
    }
    public function applyConsultation($caseId, $applyHospitalId, $applyUserId, $applyMessage, $replyHospital)
    {
        $sql = 'insert into consultation (case_id, apply_hospital_id, apply_user_id, apply_message, reply_hospital_id)
                values (:case, :applyHospital, :applyUser, :applyMessage, :replyHospital)';
        $param = [':case' => $caseId, ':applyHospital' => $applyHospitalId, ':applyUser' => $applyUserId, 
                        ':applyMessage' => $applyMessage, ':replyHospital' => $replyHospital];
        return $this->insertData($sql, $param);
    }
    public function applyReferral($caseId, $applyHospitalId, $applyUserId, $applyMessage, $replyHospital)
    {
        $sql = 'insert into referral (case_id, apply_hospital_id, apply_user_id, apply_message, reply_hospital_id, status)
                values (:case, :applyHospital, :applyUser, :applyMessage, :replyHospital, :status)';
        $param = [':case' => $caseId, ':applyHospital' => $applyHospitalId, ':applyUser' => $applyUserId,
                        ':applyMessage' => $applyMessage, ':replyHospital' => $replyHospital, ':status' => REFERRAL_START];
        return $this->insertData($sql, $param);
    }
    public function confirmHospitalize($referralId, $confirmUserId)
    {
        $sql = 'update referral set confirm_user_id = :user, status = :status, confirm_time = now()
                where referral_id = :referral';
        $param = [':user' => $confirmUserId, ':status' => REFERRAL_CONFIRM, ':referral' => $referralId];
        return $this->updateData($sql, $param);
    }
    public function discharge($referralId, $userId, $operateTime, $operateInfo, $course, $diagnosis, $instructions, $medicine, $advice, 
            $childHospitalName, $childHospitalTel, $parentHospitalName, $caseName, array $planList)
    {
        $this->pdo->beginTransaction();
        
        if (!empty($planList)) {
            $sql = 'insert into plan (child_hospital_name, child_hospital_tel, parent_hospital_name, case_name, 
                    referral_id, follow_time, follow_text)
                    values (:child_hospital_name, :child_hospital_tel, :parent_hospital_name, :case_name, :referral, :time, :text)';
            foreach ($planList as $plan) {
                $param = [':child_hospital_name' => $childHospitalName, ':child_hospital_tel' => $childHospitalTel, 
                                ':parent_hospital_name' => $parentHospitalName, ':case_name' => $caseName, ':referral' => $referralId, 
                                ':time' => $plan['time'], ':text' => $plan['message']];
                $planId = $this->insertData($sql, $param);
                if (VALUE_DB_ERROR === $planId) {
                    $this->pdo->rollBack();
                    return VALUE_DB_ERROR;
                }
            }
        }
        if (null == $operateTime) {
            $sql = 'update referral set discharge_user_id = :user, course = :course, diagnosis = :diagnosis, 
                    instructions = :instructions, medicine = :medicine, advice = :advice, discharge_time = now();
                where referral_id = :referral';
            $param = [':user' => $userId, ':course' => $course, ':diagnosis' => $diagnosis, ':instructions' => $instructions, 
                            ':medicine' => $medicine, ':advice' => $advice, ':referral' => $referralId];
        } else {
            $sql = 'update referral set discharge_user_id = :user, operate_time = :operate_time, operate_info = :operate_info, 
                    course = :course, diagnosis = :diagnosis, instructions = :instructions, medicine = :medicine, 
                    advice = :advice, discharge_time = now();
                where referral_id = :referral';
            $param = [':user' => $userId, ':operate_time' => $operateTime, ':operate_info' => $operateInfo, 
                            ':course' => $course, ':diagnosis' => $diagnosis, ':instructions' => $instructions,
                            ':medicine' => $medicine, ':advice' => $advice, ':referral' => $referralId];
        }
        $ret = $this->updateData($sql, $param);
        if (VALUE_DB_ERROR === $ret) {
            $this->pdo->rollBack();
            return VALUE_DB_ERROR;
        }
        $this->pdo->commit();
        return true;
    }
    public function replyConsultation($consultationId, $replyUserId, $diagnosis, $advice)
    {
        $sql = 'update consultation set reply_user_id = :user, diagnosis = :diagnosis, advice = :advice, reply_time = now()
                where consultation_id = :consultation';
        $param = [':user' => $replyUserId, ':diagnosis' => $diagnosis, ':advice' => $advice, ':consultation' => $consultationId];
        return $this->updateData($sql, $param);
    }
    public function replyFollow($followId, $replyUserId, $replyAdvice, $replyQuestion)
    {
        $sql = 'update follow set reply_user_id = :user, reply_advice = :advice, reply_question = :question,
                reply_time = now() where follow_id = :follow';
        $param = [':user' => $replyUserId, ':advice' => $replyAdvice, ':question' => $replyQuestion, ':follow' => $followId];
        return $this->updateData($sql, $param);
    }
    public function replyReferral($referralId, $replyUserId, $replyMessage, $status, $expectTime)
    {
        $sql = 'update referral set reply_user_id = :user, reply_message = :message, status = :status, 
                expect_time = :expect_time, reply_time = now() where referral_id = :referral';
        $param = [':user' => $replyUserId, ':message' => $replyMessage, ':status' => $status, 
                        ':expect_time' => $expectTime, ':referral' => $referralId];
        return $this->updateData($sql, $param);
    }
}
