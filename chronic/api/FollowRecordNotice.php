<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class FollowRecordNotice extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['follow_record_id', 'status'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['follow_record_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedFollowRecord($this->param['follow_record_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'follow_record_id.');
        }
        
        //0:not noticed. 1:noticed. 2:deny.
        $checkRange = HpValidate::checkRange(['status'], $this->param, ['0', '1', '2']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $followType = Dbi::getDbi()->getFollowType($this->param['follow_record_id']);
        if (VALUE_DB_ERROR === $followType) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if ('1' != $followType) {
            return HpErrorMessage::getError(ERROR_FOLLOW_RECORD_NOTICE);
        }
        
        $ret = Dbi::getDbi()->editFollowRecord($this->param['follow_record_id'], ['parent_notice' => $this->param['status']]);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
