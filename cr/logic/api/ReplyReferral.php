<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class ReplyReferral extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $params = [
                        'referral_id' => $this->param['referral_id'],
                        'reply_user' => $this->param['reply_user'],
                        'judge' => $this->param['judge'],
                        'message' => $this->param['message'],
                        'expect_time' => $this->param['expect_time'],
        ];
        $checkRequired = HpValidate::checkRequiredArray($params);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        if (false === is_numeric($this->param['reply_user'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'reply_user.');
        }
        
        if ($this->param['judge'] != REFERRAL_OK && $this->param['judge'] != REFERRAL_DENY) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'judge.');
        }
        
        if ('' == $this->param['message']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'message.');
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'referral_id.');
        }
        
        $ret = HpValidate::checkTime($this->param['expect_time']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_TIME);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $referralInfo = Dbi::getDbi()->getReferralInfo($this->param['referral_id']);
        if (VALUE_DB_ERROR === $referralInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        } else {
            $contentDoctor = HpErrorMessage::getTelMessageReplyReferralDoctor($referralInfo['reply_hospital_name'],
                    $referralInfo['reply_doctor_name']);
            $contentCase = HpErrorMessage::getTelMessageReplyReferralCase($referralInfo['reply_hospital_name'],
                    $referralInfo['reply_doctor_name'], $referralInfo['apply_hospital_name']);
        }
        
        $telDoctor = Dbi::getDbi()->getTelList($referralInfo['apply_hospital_id']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($referralInfo['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $ret = Dbi::getDbi()->replyReferral($this->param['referral_id'], 
                $this->param['reply_user'], $this->param['message'], $this->param['judge'], $this->param['expect_time']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        foreach ($telDoctor as $tel) {
            if (true === HpValidate::checkPhoneNo($tel)) {
                $ret = HpShortMessageService::send($tel, $contentDoctor);
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
