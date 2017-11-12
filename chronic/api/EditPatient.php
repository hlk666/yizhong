<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class EditPatient extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id', 'department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id', 'department_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        if (false === Dbi::getDbi()->existedDepartment($this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'department_id.');
        }
        if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $data = array();
        if (isset($this->param['identity_card'])) {
            $data['identity_card'] = $this->param['identity_card'];
        }
        if (isset($this->param['name'])) {
            $data['name'] = $this->param['name'];
        }
        if (isset($this->param['age'])) {
            $data['birth_year'] = date('Y') - $this->param['age'];
        }
        if (isset($this->param['sex'])) {
            $data['sex'] = $this->param['sex'];
        }
        if (isset($this->param['tel'])) {
            $data['tel'] = $this->param['tel'];
        }
        if (isset($this->param['address'])) {
            $data['address'] = $this->param['address'];
        }
        if (isset($this->param['ethnic'])) {
            $data['ethnic'] = $this->param['ethnic'];
        }
        if (isset($this->param['native_place'])) {
            $data['native_place'] = $this->param['native_place'];
        }
        if (isset($this->param['hospitalization'])) {
            $data['hospitalization'] = $this->param['hospitalization'];
        }
        if (isset($this->param['family_name'])) {
            $data['family_name'] = $this->param['family_name'];
        }
        if (isset($this->param['family_tel'])) {
            $data['family_tel'] = $this->param['family_tel'];
        }
        if (isset($this->param['height'])) {
            $data['height'] = $this->param['height'];
        }
        if (isset($this->param['weight'])) {
            $data['weight'] = $this->param['weight'];
        }
        if (isset($this->param['job'])) {
            $data['job'] = $this->param['job'];
        }
        if (isset($this->param['education'])) {
            $data['education'] = $this->param['education'];
        }
        if (empty($data)) {
            return HpErrorMessage::getError(ERROR_PARAM_REQUIRED);
        }
        
        $ret = Dbi::getDbi()->editPatient($this->param['patient_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
