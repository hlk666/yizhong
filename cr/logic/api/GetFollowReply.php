<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetFollowReply extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['hospital_id'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['hospital_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $follows = Dbi::getDbi()->getFollowReply($this->param['hospital_id']);
        if (VALUE_DB_ERROR === $follows) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($follows)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['follows'] = $follows;
        return $this->retSuccess;
    }
}
