<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditDoctor extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['doctor_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['doctor_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        if (isset($this->param['login_name']) && Dbi::getDbi()->existedDoctor($this->param['login_name'])) {
            return HpErrorMessage::getError(ERROR_USER_NAME_USED);
        }
        
        if (isset($this->param['password'])) {
            if (!isset($this->param['old_password']) || empty($this->param['old_password'])) {
                return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'old_passord');
            }
            $oldPassword = Dbi::getDbi()->getDoctorPassword($this->param['doctor_id']);
            if (VALUE_DB_ERROR === $oldPassword) {
                return HpErrorMessage::getError(ERROR_DB);
            }
            if ($oldPassword != md5($this->param['old_password'])) {
                return HpErrorMessage::getError(ERROR_PASSWORD);
            }
        }
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['real_name'])) {
            $data['real_name'] = $this->param['real_name'];
        }
        if (isset($this->param['password'])) {
            $data['password'] = md5($this->param['password']);
        }
        if (isset($this->param['type'])) {
            $data['type'] = $this->param['type'];
        }
        if (isset($this->param['tel'])) {
            $data['tel'] = $this->param['tel'];
        }
        if (isset($this->param['phone'])) {
            $data['phone'] = $this->param['phone'];
        }
        if (empty($data)) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        $ret = Dbi::getDbi()->editDoctor($this->param['doctor_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
