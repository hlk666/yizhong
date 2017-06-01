<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/util/HpVerificationCode.php';

class ApplyAuthority extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['new_department_id', 'old_department_id', 'patient_id', 'vc'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['new_department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'new_department_id.');
        }
        if (false === Dbi::getDbi()->existedDepartment($this->param['old_department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'old_department_id.');
        }
        if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['old_department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if ($this->param['vc'] != HpVerificationCode::getVC('Patient' . $this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_VC);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->deletePatient($this->param['patient_id'], 
                $this->param['old_department_id'], $this->param['new_department_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
