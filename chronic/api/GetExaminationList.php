<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetExaminationList extends BaseApi
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
        $startTime = isset($this->param['start_time']) ? $this->param['start_time'] : null;
        $endTime = isset($this->param['end_time']) ? $this->param['end_time'] : null;
        $examinationList = Dbi::getDbi()->getExaminationList($this->param['patient_id'], $departmentId, $startTime, $endTime);
        if (VALUE_DB_ERROR === $examinationList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($examinationList)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['examination_list'] = $examinationList;
        return $this->retSuccess;
    }
}
