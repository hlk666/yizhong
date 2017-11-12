<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetPatientList extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $name = isset($this->param['name']) ? $this->param['name'] : null;
        $identityCard = isset($this->param['identity_card']) ? $this->param['identity_card'] : null;
        $tel = isset($this->param['tel']) ? $this->param['tel'] : null;
        $chronic = isset($this->param['chronic']) ? $this->param['chronic'] : null;
        $page = isset($this->param['page']) ? $this->param['page'] : 0;
        $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
        
        $patientList = Dbi::getDbi()->getPatientList($this->param['department_id'], $name, $identityCard, $tel,
                $chronic, $page * $rows, $rows);
        if (VALUE_DB_ERROR === $patientList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patientList)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['patient_list'] = $patientList;
        return $this->retSuccess;
    }
}
