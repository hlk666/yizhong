<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddHospitalRelation extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = isset($this->param['parent_hospital_id']) ? HpValidate::checkRequired($this->param['parent_hospital_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'parent_hospital_id');
        }
        
        $ret = isset($this->param['child_hospital_id']) ? HpValidate::checkRequired($this->param['child_hospital_id']) : false;
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'child_hospital_id');
        }
        
        if (false === is_numeric($this->param['parent_hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'parent_hospital_id');
        }
        
        if (false === is_numeric($this->param['child_hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'child_hospital_id');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addHospitalRelation($this->param['parent_hospital_id'], $this->param['child_hospital_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
