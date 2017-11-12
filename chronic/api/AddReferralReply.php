<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class AddReferralReply extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['referral_id', 'apply_department_id', 'doctor_id', 'message', 'status'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['referral_id', 'apply_department_id', 'doctor_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'referral_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['apply_department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'apply_department_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        $checkRange = HpValidate::checkRange(['status'], $this->param, ['2', '3']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addReferralReply($this->param['referral_id'], $this->param['doctor_id'], $this->param['message'], $this->param['status']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $tel = Dbi::getDbi()->getTelDepartment($this->param['apply_department_id']);
        if (VALUE_DB_ERROR === $tel) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (!empty($tel)) {
            HpShortMessageService::send($tel, "有新的转诊回复，请确认。");
        }
        send_notice($this->param['apply_department_id'], '有新的转诊回复，请确认。');
        
        return $this->retSuccess;
    }
}
