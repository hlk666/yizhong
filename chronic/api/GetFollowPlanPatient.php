<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetFollowPlanPatient extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $followPlanPatient = Dbi::getDbi()->getFollowPlanPatient();
        if (VALUE_DB_ERROR === $followPlanPatient) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($followPlanPatient)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['follow_plan_patient'] = $followPlanPatient;
        return $this->retSuccess;
    }
}
