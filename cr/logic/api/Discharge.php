<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

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
        
        $planList = array();
        if (!empty($this->param['plan_info'])) {
            $plansStr = explode(';', $this->param['plan_info']);
            foreach ($plansStr as $planStr) {
                $plan = explode(',', $planStr);
                if (!isset($plan[1])) {
                    return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_info.');
                }
                $planList[]['time'] = $plan[0];
                $planList[]['message'] = $plan[1];
            }
        }
        
        
        $ret = Dbi::getDbi()->discharge($this->param['referral_id'], $this->param['discharge_user_id'], $operateTime, $operateInfo, 
                $this->param['course'], $this->param['diagnosis'], $this->param['instructions'], $this->param['medicine'], 
                $this->param['advice'], $childHospitalName, $childHospitalTel, $parentHospitalName, $caseName, $planList);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
