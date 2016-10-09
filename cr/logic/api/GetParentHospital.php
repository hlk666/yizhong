<?php
require_once PATH_ROOT . 'logic/BaseLogic.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetParentHospital extends BaseLogic
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $ret = HpValidate::checkRequired($this->param['hospital_id']);
        if (true !== $ret) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED, 'hospital_id.');
        }
        
        if (false === is_numeric($this->param['hospital_id'])) {
            return HpErrorMessage::getError(ERROR_PARAM_NUMERIC, 'hospital_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {

        $hospitals = Dbi::getDbi()->getHospitalParent($this->param['hospital_id']);
        if (VALUE_DB_ERROR === $hospitals) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($hospitals)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $this->retSuccess['hospitals'] = $hospitals;
        return $this->retSuccess;
    }
}
