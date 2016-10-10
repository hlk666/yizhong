<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddUser extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['login_user']) ? HpValidate::checkRequired($this->param['login_user']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'login_user');
        }
        
        $ret = isset($this->param['name']) ? HpValidate::checkRequired($this->param['name']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'name');
        }
        
        $ret = isset($this->param['password']) ? HpValidate::checkRequired($this->param['password']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'password');
        }
        
        $ret = isset($this->param['type']) ? HpValidate::checkRequired($this->param['type']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'type');
        }
        
        $ret = isset($this->param['hospital_id']) ? HpValidate::checkRequired($this->param['hospital_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'hospital_id');
        }
        
        if ($this->param['type'] != '1' && $this->param['type'] != '2') {
            return HpErrorMessage::getError(ERROR_PARAM_RANGE, 'type');
        }
        
        $ret = Dbi::getDbi()->existedUser($this->param['login_user']);
        if (true === $ret) {
            return HpErrorMessage::getError(ERROR_USER_NAME_USED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $session = new HpSession($this->param['sid']);
        if ($session->getSessionType() > $this->param['type']) {
            return HpErrorMessage::getError(ERROR_NO_PERMISSON);
        }
        $ret = Dbi::getDbi()->addUser($this->param['login_user'], $this->param['name'], 
                md5($this->param['password']), $this->param['type'], $this->param['hospital_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
