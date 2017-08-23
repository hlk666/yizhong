<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetCaseList extends BaseApi
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
        
        if (isset($this->param['department_id'])) {
            if (false === HpValidate::checkRequired($this->param['department_id'])) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'department_id.');
            }
            $checkNumeric = HpValidate::checkNumeric(['department_id'], $this->param);
            if (true !== $checkNumeric) {
                return $checkNumeric;
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $departmentId = isset($this->param['department_id']) ? $this->param['department_id'] : null;
        
        $caseList = Dbi::getDbi()->getCaseList($this->param['patient_id'], $departmentId);
        if (VALUE_DB_ERROR === $caseList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($caseList)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $chronicList = Dbi::getDbi()->getChronicByPatient($this->param['patient_id']);
        if (VALUE_DB_ERROR === $chronicList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $this->retSuccess['case_list'] = $caseList;
        $this->retSuccess['chronic_list'] = $chronicList;
        return $this->retSuccess;
    }
}
