<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ReplyConsultation extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['consultation_id']) ? HpValidate::checkRequired($this->param['consultation_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'consultation_id.');
        }
        
        
        $ret = isset($this->param['reply_user']) ? HpValidate::checkRequired($this->param['reply_user']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'reply_user.');
        }
        
        $ret = isset($this->param['diagnosis']) ? HpValidate::checkRequired($this->param['diagnosis']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'diagnosis.');
        }
        
        $ret = isset($this->param['advice']) ? HpValidate::checkRequired($this->param['advice']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'advice.');
        }
        
        if (false === is_numeric($this->param['reply_user'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'reply_user.');
        }
        
        if ('' == $this->param['diagnosis']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'diagnosis.');
        }
        
        if ('' == $this->param['advice']) {
            return HpErrorMessage::getError(ERROR_PARAM_SPACE, 'advice.');
        }
        
        if (false === Dbi::getDbi()->existedConsultation($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'consultation_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $consultationInfo = Dbi::getDbi()->getConsultationInfo($this->param['consultation_id']);
        if (VALUE_DB_ERROR === $consultationInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        } else {
            $contentDoctor = HpErrorMessage::getTelMessageReplyConsultationDoctor($consultationInfo['reply_hospital_name'],
                    $consultationInfo['reply_doctor_name']);
            $contentCase = HpErrorMessage::getTelMessageReplyConsultationCase($consultationInfo['reply_hospital_name'],
                    $consultationInfo['reply_doctor_name'], $consultationInfo['apply_hospital_name']);
        }
        
        $telDoctor = Dbi::getDbi()->getTelList($consultationInfo['apply_hospital_id']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($consultationInfo['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $ret = Dbi::getDbi()->replyConsultation($this->param['consultation_id'], 
                $this->param['reply_user'], $this->param['diagnosis'], $this->param['advice']);
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
