<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class ResetPassword extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['login_name', 'old_password', 'new_password'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $user = Dbi::getDbi()->getUserInfo($this->param['login_name']);
        if (VALUE_DB_ERROR === $user) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($user)) {
            return HpErrorMessage::getError(ERROR_USER_NOT_EXISTED);
        }
        if ($user['password'] != md5($this->param['old_password'])) {
            return HpErrorMessage::getError(ERROR_PASSWORD);
        }
        
        $password = md5($this->param['new_password']);
        $ret = Dbi::getDbi()->editUserPassword($this->param['login_name'], $password);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
