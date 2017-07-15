<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddFollowPlan extends BaseApi
{
    private $planList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id', 'patient_id', 'name', 'plan_text', 'doctor_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id', 'patient_id', 'doctor_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        $this->planList = $this->getStructalData($this->param['plan_text']);
        if (empty($this->planList)) {
            return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_text.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $followPlanId = Dbi::getDbi()->addFollowPlan($this->param['department_id'], $this->param['patient_id'], 
                $this->param['plan_text'], $this->planList, $this->param['doctor_id'], $this->param['name']);
        if (VALUE_DB_ERROR === $followPlanId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['follow_plan_id'] = $followPlanId;
        return $this->retSuccess;
    }
}
