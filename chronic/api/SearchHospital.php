<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class SearchHospital extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (empty($this->param['name']) 
                && empty($this->param['level'])
                && empty($this->param['tel'])
                && empty($this->param['area'])
                && empty($this->param['province'])
                && empty($this->param['city'])
                && empty($this->param['address'])) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $name = isset($this->param['name']) ? $this->param['name'] : null;
        $level = isset($this->param['level']) ? $this->param['level'] : null;
        $tel = isset($this->param['tel']) ? $this->param['tel'] : null;
        $area = isset($this->param['area']) ? $this->param['area'] : null;
        $province = isset($this->param['province']) ? $this->param['province'] : null;
        $city = isset($this->param['city']) ? $this->param['city'] : null;
        $address = isset($this->param['address']) ? $this->param['address'] : null;
        
        $hospital = Dbi::getDbi()->searchHospital($name, $level, $tel, $area, $province, $city, $address);
        if (VALUE_DB_ERROR === $hospital) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($hospital)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['hospital'] = $hospital;
        return $this->retSuccess;
    }
}
