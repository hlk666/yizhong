<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class AddReferralDischarge extends BaseApi
{
    private $planList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['referral_id', 'department_id', 'doctor_id', 'diagnosis', 'info', 
                        'patient_id', 'plan_text', 'plan_name', 'apply_department_id'];
        
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['referral_id', 'department_id', 'doctor_id', 'patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'referral_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        $this->planList = $this->getStructalData($this->param['plan_text']);
        if (empty($this->planList)) {
            return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_text.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addReferralDischarge($this->param['department_id'], $this->param['patient_id'],
                $this->param['plan_text'], $this->planList, $this->param['doctor_id'], $this->param['plan_name'], 
                $this->param['referral_id'], $this->param['doctor_id'], $this->param['diagnosis'], $this->param['info']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $tel = Dbi::getDbi()->getTelDepartment($this->param['apply_department_id']);
        if (VALUE_DB_ERROR === $tel) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (!empty($tel)) {
            HpShortMessageService::send($tel, '转诊病人出院，转诊ID：' . $this->param['referral_id']);
        }
        send_notice($this->param['apply_department_id'], '转诊病人出院，转诊ID：' . $this->param['referral_id']);
        
        return $this->retSuccess;
    }
}
