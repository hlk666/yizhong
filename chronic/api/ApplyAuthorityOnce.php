<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/util/HpVerificationCode.php';

class ApplyAuthorityOnce extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id', 'department', 'vc'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department.');
        }
        
        if ($this->param['vc'] != HpVerificationCode::getVC('Patient' . $this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_VC);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        $data['department_once'] = $this->param['department'];
        
        $ret = Dbi::getDbi()->setManageDepartment($this->param['patient_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
