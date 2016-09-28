<?php
require_once PATH_ROOT . 'logic/BaseLogic.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class Login extends BaseLogic
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = HpValidate::checkRequired($this->param['user']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'user');
        }
        
        $ret = HpValidate::checkRequired($this->param['password']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'password');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $user = Dbi::getDbi()->getUserInfo($this->param['user']);
        if (VALUE_DB_ERROR === $user) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($user)) {
            return HpErrorMessage::getError(ERROR_USER_NOT_EXISTED);
        }
        if ($user['password'] != md5($this->param['password'])) {
            return HpErrorMessage::getError(ERROR_PASSWORD);
        }
        
        $hospital = Dbi::getDbi()->getHospitalInfo($user['hospital_id']);
        if (VALUE_DB_ERROR === $hospital) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($hospital)) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY);
        }
        
        $session = new HpSession();
        $sid = $session->createSession($this->param['user'], $user['user_type']);
        if (false === $sid) {
            return HpErrorMessage::getError(ERROR_CREATE_SESSION);
        }
        
        $this->retSuccess['sid'] = $sid;
        $this->retSuccess['user_id'] = $user['user_id'];
        $this->retSuccess['user_name'] = $user['user_name'];
        $this->retSuccess['user_type'] = $user['user_type'];
        $this->retSuccess['hospital_id'] = $hospital['hospital_id'];
        $this->retSuccess['hospital_name'] = $hospital['hospital_name'];
        return $this->retSuccess;
    }
}
