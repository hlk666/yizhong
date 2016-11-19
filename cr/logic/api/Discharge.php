<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class Discharge extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $params = [
                        'referral_id' => $this->param['referral_id'],
                        'discharge_user_id' => $this->param['discharge_user_id'],
                        'course' => $this->param['course'],
                        'diagnosis' => $this->param['diagnosis'],
                        'instructions' => $this->param['instructions'],
                        'medicine' => $this->param['medicine'],
                        'advice' => $this->param['advice'],
                        'plan_info' => $this->param['plan_info'],
        ];
        $checkRequired = HpValidate::checkRequiredArray($params);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        if (false === is_numeric($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'referral_id.');
        }
        if (false === is_numeric($this->param['discharge_user_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'discharge_user_id.');
        }
        
        if ('' == $this->param['course']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'message.');
        }
        if ('' == $this->param['diagnosis']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'message.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $referral = Dbi::getDbi()->getReferralInfo($this->param['referral_id']);
        if (VALUE_DB_ERROR === $referral) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($referral)) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'referral_id.');
        }
        
        $operateTime = isset($this->param['operate_time']) ? $this->param['operate_time'] : null;
        $operateInfo = isset($this->param['operate_info']) ? $this->param['operate_info'] : null;
        $childHospitalName = $referral['apply_hospital_name'];
        $childHospitalTel = $referral['apply_doctor_tel'];
        $parentHospitalName = $referral['reply_hospital_name'];
        $caseName = $referral['name'];
        $contentDoctor = HpErrorMessage::getTelMessageDischargeDoctor($parentHospitalName,
                $referral['reply_doctor_name'], $caseName);
        $contentCase = HpErrorMessage::getTelMessageDischargeCase($parentHospitalName,
                $referral['reply_doctor_name'], $childHospitalName);
        
        $telDoctor = Dbi::getDbi()->getTelList($referral['apply_hospital_id']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($referral['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $planList = array();
        if (!empty($this->param['plan_info'])) {
            $plansStr = explode(';', $this->param['plan_info']);
            
            foreach ($plansStr as $planStr) {
                if (empty($planStr)) {
                    continue;
                }
                $plan = explode('#', $planStr);
                if (!isset($plan[1])) {
                    return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_info.');
                }
                $temp = ['time' => $plan[0], 'message' => $plan[1]];
                $planList[] = $temp;
            }
        }
        
        
        $ret = Dbi::getDbi()->discharge($this->param['referral_id'], $this->param['discharge_user_id'], $operateTime, $operateInfo, 
                $this->param['course'], $this->param['diagnosis'], $this->param['instructions'], $this->param['medicine'], 
                $this->param['advice'], $childHospitalName, $childHospitalTel, $parentHospitalName, $caseName, $planList, $this->param['plan_info']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        foreach ($telDoctor as $tel) {
            if (true === HpValidate::checkPhoneNo($tel['tel'])) {
                $ret = HpShortMessageService::send($tel['tel'], $contentDoctor);
                if (false === $ret) {
                    return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
                }
            }
        }
        if ('' != $telCase && true === HpValidate::checkPhoneNo($telCase)) {
            $ret = HpShortMessageService::send($telCase, $contentCase);
            if (false === $ret) {
                return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
            }
        }
        
        return $this->retSuccess;
    }
}
