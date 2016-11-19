<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class ApplyReferral extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $params = [
                        'case_id' => $this->param['case_id'],
                        'apply_hospital' => $this->param['apply_hospital'],
                        'apply_user' => $this->param['apply_user'],
                        'apply_message' => $this->param['apply_message'],
                        'reply_hospital' => $this->param['reply_hospital'],
        ];
        $checkRequired = HpValidate::checkRequiredArray($params);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        if (false === is_numeric($this->param['case_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'case_id.');
        }
        if (false === is_numeric($this->param['apply_hospital'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'apply_hospital.');
        }
        if (false === is_numeric($this->param['apply_user'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'apply_user.');
        }
        if (false === is_numeric($this->param['reply_hospital'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'reply_hospital.');
        }
        
        if ('' == $this->param['apply_message']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'apply_message.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $telDoctor = Dbi::getDbi()->getTelList($this->param['apply_hospital']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($this->param['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $referralId = Dbi::getDbi()->applyReferral($this->param['case_id'], $this->param['apply_hospital'], $this->param['apply_user'], 
                $this->param['apply_message'], $this->param['reply_hospital']);
        if (VALUE_DB_ERROR === $referralId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $referralInfo = Dbi::getDbi()->getReferralInfo($referralId);
        if (VALUE_DB_ERROR === $referralInfo || empty($referralInfo)) {
            return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
        } else {
            $contentDoctor = HpErrorMessage::getTelMessageApplyReferralDoctor($referralInfo['apply_hospital_name'],
                    $referralInfo['apply_doctor_name']);
            $contentCase = HpErrorMessage::getTelMessageApplyReferralCase($referralInfo['apply_hospital_name'],
                    $referralInfo['apply_doctor_name'], $referralInfo['reply_hospital_name']);
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
