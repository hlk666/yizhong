<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditUser extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['user_id', 'login_name', 'real_name', 'password', 'type', 'tel'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['user_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        $checkRange = HpValidate::checkRange(['type'], $this->param, ['0', '1', '2']);
        if (true !== $checkRange) {
            return $checkRange;
        }
        
        $ret = HpValidate::checkPhoneNo($this->param['tel']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_PHONE);
        }
        
        $ret = Dbi::getDbi()->existedUser($this->param['login_name']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_NOT_EXIST_ID);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $session = new HpSession($this->param['sid']);
        if ($session->getSessionType() > $this->param['type']) {
            return HpErrorMessage::getError(ERROR_NO_PERMISSON);
        }
        
        $ret = Dbi::getDbi()->editUser($this->param['user_id'], $this->param['login_name'], 
                $this->param['real_name'], md5($this->param['password']), $this->param['type'], $this->param['tel']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
