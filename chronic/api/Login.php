<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class Login extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['user', 'password'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $doctor = Dbi::getDbi()->getDoctorInfo($this->param['user']);
        if (VALUE_DB_ERROR === $doctor) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($doctor)) {
            return HpErrorMessage::getError(ERROR_USER_NOT_EXISTED);
        }
        if ($doctor['password'] != md5($this->param['password'])) {
            return HpErrorMessage::getError(ERROR_PASSWORD);
        }
        /*
        $session = new HpSession();
        $sid = $session->createSession($this->param['user'], $doctor['type']);
        if (false === $sid) {
            return HpErrorMessage::getError(ERROR_CREATE_SESSION);
        }
        */
        //$this->retSuccess['sid'] = $sid;
        $this->retSuccess['id'] = $doctor['doctor_id'];
        $this->retSuccess['name'] = $doctor['doctor_name'];
        $this->retSuccess['type'] = $doctor['type'];
        $this->retSuccess['department_id'] = $doctor['department_id'];
        $this->retSuccess['department_name'] = $doctor['department_name'];
        $this->retSuccess['hospital_id'] = $doctor['hospital_id'];
        $this->retSuccess['hospital_name'] = $doctor['hospital_name'];
        return $this->retSuccess;
    }
}
