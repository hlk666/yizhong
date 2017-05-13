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
        
        $required = ['follow_plan_id'];
        
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
        
        $this->planList = $this->getStructalData($this->param['plan_text']);
        if (empty($this->planList)) {
            return HpErrorMessage::getError(ERROR_PARAM_FORMAT, 'plan_text.');
        }
        
        foreach ($planList as $plan) {
            if ($plan[0] < date('Y-m-d H:i:s')) {
                return HpErrorMessage::getError(ERROR_TIME_ERROR, $plan[0]);
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->editFollowPlan($this->param['follow_plan_id'], 
                $this->param['plan_text'], $this->planList, $this->param['doctor_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
