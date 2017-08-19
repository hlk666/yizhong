<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetPatientInfo extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $patientInfo = Dbi::getDbi()->getPatientInfo($this->param['patient_id']);
        if (VALUE_DB_ERROR === $patientInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patientInfo)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $patientInfo['age'] = date('Y') - $patientInfo['birth_year'];
        return array_merge($this->retSuccess, $patientInfo);
    }
}
