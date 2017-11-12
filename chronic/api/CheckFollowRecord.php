<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class CheckFollowRecord extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id', 'follow_plan_id', 'plan_time'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $isExistRecord = Dbi::getDbi()->existedFollowRecordPlanTime($this->param['patient_id'], 
                $this->param['follow_plan_id'], $this->param['plan_time']);
        if (VALUE_DB_ERROR === $isExistRecord) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        $this->retSuccess['is_exist_follow_record'] = $isExistRecord;
        return $this->retSuccess;
    }
}
