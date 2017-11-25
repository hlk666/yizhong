<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetPatient extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        if (empty($this->param['name']) 
                && empty($this->param['identity_card'])
                && empty($this->param['tel'])) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $name = isset($this->param['name']) ? $this->param['name'] : null;
        $identityCard = isset($this->param['identity_card']) ? $this->param['identity_card'] : null;
        $tel = isset($this->param['tel']) ? $this->param['tel'] : null;
        
        $patientInfo = Dbi::getDbi()->getPatientForEcgonline($name, $tel, $identityCard);
        if (VALUE_DB_ERROR === $patientInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patientInfo)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        if (count($patientInfo) > 1) {
            return HpErrorMessage::getError(ERROR_USER_COUNT);
        }
        $this->retSuccess['patient_id'] = $patientInfo[0]['patient_id'];
        $this->retSuccess['name'] = $patientInfo[0]['name'];
        $this->retSuccess['sex'] = $patientInfo[0]['sex'];
        $this->retSuccess['age'] = date('Y') - $patientInfo[0]['birth_year'];
        return $this->retSuccess;
    }
}
