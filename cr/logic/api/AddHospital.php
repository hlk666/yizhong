<?php
require_once PATH_ROOT . 'logic/BaseLogic.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddHospital extends BaseLogic
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = HpValidate::checkRequired($this->param['name']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'name');
        }
        
        $ret = HpValidate::checkRequired($this->param['address']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'address');
        }
        
        $ret = HpValidate::checkRequired($this->param['tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'tel');
        }
        
        $ret = HpValidate::checkRequired($this->param['message_tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'message_tel');
        }
        
        $ret = HpValidate::checkPhoneNo($this->param['message_tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addHospital($this->param['name'], 
                $this->param['tel'], $this->param['address'], $this->param['message_tel']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
