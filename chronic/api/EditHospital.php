<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditHospital extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['hospital_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['hospital_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedHospital($this->param['hospital_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'hospital_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['name'])) {
            $data['name'] = $this->param['name'];
        }
        if (isset($this->param['level'])) {
            $data['level'] = $this->param['level'];
        }
        if (isset($this->param['tel'])) {
            $data['tel'] = $this->param['tel'];
        }
        if (isset($this->param['area'])) {
            $data['area'] = $this->param['area'];
        }
        if (isset($this->param['province'])) {
            $data['province'] = $this->param['province'];
        }
        if (isset($this->param['city'])) {
            $data['city'] = $this->param['city'];
        }
        if (isset($this->param['address'])) {
            $data['address'] = $this->param['address'];
        }
        if (empty($data)) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        $ret = Dbi::getDbi()->editHospital($this->param['hospital_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
