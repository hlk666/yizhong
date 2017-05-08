<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditCase extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['case_id', 'department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['case_id', 'department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedCase($this->param['case_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'case_id.');
        }
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        if (false === Dbi::getDbi()->isCaseInDepartment($this->param['case_id'], $this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['diagnosis'])) {
            $data['diagnosis'] = $this->param['diagnosis'];
        }
        if (isset($this->param['chief_complaint'])) {
            $data['chief_complaint'] = $this->param['chief_complaint'];
        }
        if (isset($this->param['present_illness'])) {
            $data['present_illness'] = $this->param['present_illness'];
        }
        if (isset($this->param['past_illness'])) {
            $data['past_illness'] = $this->param['past_illness'];
        }
        if (isset($this->param['allergies'])) {
            $data['allergies'] = $this->param['allergies'];
        }
        if (isset($this->param['smoking'])) {
            $data['smoking'] = $this->param['smoking'];
        }
        if (isset($this->param['drinking'])) {
            $data['drinking'] = $this->param['drinking'];
        }
        if (isset($this->param['body_examination'])) {
            $data['body_examination'] = $this->param['body_examination'];
        }
        $ret = Dbi::getDbi()->editCase($caseId, $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
