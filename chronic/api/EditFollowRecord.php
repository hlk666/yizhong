<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditFollowRecord extends BaseApi
{
    private $planList = array();
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['follow_record_id', 'doctor_id'];
        
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
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['record_text'])) {
            $data['record_text'] = $this->param['record_text'];
        }
        if (isset($this->param['diagnosis'])) {
            $data['diagnosis'] = $this->param['diagnosis'];
        }
        if (isset($this->param['doctor_id'])) {
            $data['doctor_id'] = $this->param['doctor_id'];
        }
        if (empty($data)) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        $ret = Dbi::getDbi()->editFollowRecord($this->param['follow_record_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
