<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetFollowPlanList extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (!isset($this->param['department_id']) && !isset($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'department_id | patient_id.');
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
        
        if (isset($this->param['patient_id'])) {
            if (false === HpValidate::checkRequired($this->param['patient_id'])) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'patient_id.');
            }
            $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
            if (true !== $checkNumeric) {
                return $checkNumeric;
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $departmentId = isset($this->param['department_id']) ? $this->param['department_id'] : null;
        $patientId = isset($this->param['patient_id']) ? $this->param['patient_id'] : null;
        
        $patientName = isset($this->param['patient_name']) ? $this->param['patient_name'] : null;
        $planName = isset($this->param['plan_name']) ? $this->param['plan_name'] : null;
        $startTime = isset($this->param['start_time']) ? $this->param['start_time'] : null;
        $endTime = isset($this->param['end_time']) ? $this->param['end_time'] : null;
        
        $followPlanList = Dbi::getDbi()->getFollowPlanList($departmentId, $patientId, $patientName, $planName, $startTime, $endTime);
        if (VALUE_DB_ERROR === $followPlanList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($followPlanList)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['follow_plan_list'] = $followPlanList;
        return $this->retSuccess;
    }
}
