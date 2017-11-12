<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditFollowPlan extends BaseApi
{
    private $planList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['follow_plan_id', 'doctor_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['follow_plan_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedFollowPlan($this->param['follow_plan_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'follow_plan_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        /*
        $this->planList = $this->getStructalData($this->param['plan_text']);
        if (empty($this->planList)) {
            return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_text.');
        }
        
        foreach ($this->planList as $plan) {
            if ($plan[0] < date('Y-m-d H:i:s')) {
                return HpErrorMessage::getError(ERROR_TIME_ERROR, $plan[0]);
            }
        }
        */
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['name'])) {
            $data['name'] = $this->param['name'];
        }
        if (isset($this->param['plan_text'])) {
            $data['plan_text'] = $this->param['plan_text'];
        }
        if (isset($this->param['doctor_id'])) {
            $data['doctor_id'] = $this->param['doctor_id'];
        }
        if (isset($this->param['plan_time'])) {
            $data['plan_time'] = $this->param['plan_time'];
        }
        if (isset($this->param['plan_interval'])) {
            $data['plan_interval'] = $this->param['plan_interval'];
        }
        if (isset($this->param['type'])) {
            $data['type'] = $this->param['type'];
        }
        //0:common, 1:finished, 2:deleted
        if (isset($this->param['status'])) {
            $data['status'] = $this->param['status'];
        }
        if (empty($data)) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        $ret = Dbi::getDbi()->editFollowPlan($this->param['follow_plan_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
