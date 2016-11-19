<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class ApplyConsultation extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['case_id', 'apply_hospital', 'apply_user', 'apply_message', 'reply_hospital'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['case_id', 'apply_hospital', 'apply_user', 'reply_hospital'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $telDoctor = Dbi::getDbi()->getTelList($this->param['reply_hospital']);
        if (VALUE_DB_ERROR === $telDoctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $telCase = Dbi::getDbi()->getTelCase($this->param['case_id']);
        if (VALUE_DB_ERROR === $telCase) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $consultationId = Dbi::getDbi()->applyConsultation($this->param['case_id'], $this->param['apply_hospital'], 
                $this->param['apply_user'], $this->param['apply_message'], $this->param['reply_hospital']);
        if (VALUE_DB_ERROR === $consultationId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $consultationInfo = Dbi::getDbi()->getConsultationInfo($consultationId);
        if (VALUE_DB_ERROR === $consultationInfo || empty($consultationInfo)) {
            return HpErrorMessage::getError(ERROR_SHORT_MESSAGE);
        } else {
            $contentDoctor = HpErrorMessage::getTelMessageApplyConsultationDoctor($consultationInfo['apply_hospital_name'], 
                    $consultationInfo['apply_doctor_name']);
            $contentCase = HpErrorMessage::getTelMessageApplyConsultationCase($consultationInfo['apply_hospital_name'], 
                    $consultationInfo['apply_doctor_name'], $consultationInfo['reply_hospital_name']);
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
