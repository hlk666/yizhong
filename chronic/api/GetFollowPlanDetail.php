<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetFollowPlanDetail extends BaseApi
{
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
        
        return true;
    }
    
    protected function execute()
    {
        $detail = Dbi::getDbi()->getFollowPlanDetail($this->param['follow_plan_id']);
        if (VALUE_DB_ERROR === $detail) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($detail)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['plans'] = $detail;
        return $this->retSuccess;
    }
}
