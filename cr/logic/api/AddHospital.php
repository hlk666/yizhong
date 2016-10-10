<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddHospital extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['name']) ? HpValidate::checkRequired($this->param['name']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'name');
        }
        
        $ret = isset($this->param['address']) ? HpValidate::checkRequired($this->param['address']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'address');
        }
        
        $ret = isset($this->param['tel']) ? HpValidate::checkRequired($this->param['tel']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'tel');
        }
        
        $ret = isset($this->param['message_tel']) ? HpValidate::checkRequired($this->param['message_tel']) : false;
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
