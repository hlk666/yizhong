<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetPatientList extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $patientList = Dbi::getDbi()->getPatientList($this->param['department_id']);
        if (VALUE_DB_ERROR === $patientList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patientList)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['patient_list'] = $patientList;
        return $this->retSuccess;
    }
}
