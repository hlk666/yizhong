<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetFollowInfo extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['follow_id'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['follow_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $follow = Dbi::getDbi()->getFollowInfo($this->param['follow_id']);
        if (VALUE_DB_ERROR === $follow) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($follow)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        return array_merge($this->retSuccess, $follow);
    }
}
