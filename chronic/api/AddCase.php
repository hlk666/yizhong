<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddCase extends BaseApi
{
    private $chronic = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id', 'patient_id', 'diagnosis', 'chief_complaint'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id', 'patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        if (isset($this->param['chronic_label'])) {
            $this->chronic = explode('-', $this->param['chronic_label']);
            foreach ($this->chronic as $chronicId) {
                if (false === Dbi::getDbi()->existedChronic($chronicId)) {
                    return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'chronic_label.');
                }
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $presentIllness = isset($this->param['present_illness']) ? $this->param['present_illness'] : '';
        $pastIllness = isset($this->param['past_illness']) ? $this->param['past_illness'] : '';
        $allergies = isset($this->param['allergies']) ? $this->param['allergies'] : '';
        $smoking = isset($this->param['smoking']) ? $this->param['smoking'] : '';
        $drinking = isset($this->param['drinking']) ? $this->param['drinking'] : '';
        $bodyExamination = isset($this->param['body_examination']) ? $this->param['body_examination'] : '';
        
        $caseId = Dbi::getDbi()->addCase($this->param['department_id'], $this->param['patient_id'], $this->param['diagnosis'], 
                $this->param['chief_complaint'], $presentIllness, $pastIllness, $allergies, $smoking, $drinking, $bodyExamination, $this->chronic);
        if (VALUE_DB_ERROR === $caseId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['case_id'] = $caseId;
        return $this->retSuccess;
    }
}
