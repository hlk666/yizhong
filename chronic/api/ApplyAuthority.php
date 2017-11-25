<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/util/HpVerificationCode.php';

class ApplyAuthority extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['patient_id', 'new_department', 'vc'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['patient_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['new_department'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'new_department.');
        }
        
        if ($this->param['vc'] != HpVerificationCode::getVC('Patient' . $this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_VC);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $patientInfo = Dbi::getDbi()->getPatientDepartment($this->param['patient_id']);
        if (VALUE_DB_ERROR === $patientInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patientInfo)) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY);
        }
        
        if ($patientInfo['department1'] == $this->param['new_department'] 
                || $patientInfo['department2'] == $this->param['new_department']
                || $patientInfo['department3'] == $this->param['new_department']) {
            return $this->retSuccess;
        }
        $data = array();
        if ($patientInfo['department1'] == '0') {
            $data['department1'] = $this->param['new_department'];
        } elseif ($patientInfo['department2'] == '0') {
            $data['department2'] = $this->param['new_department'];
        } elseif ($patientInfo['department3'] == '0') {
            $data['department3'] = $this->param['new_department'];
        } else {
            $checkRequired = HpValidate::checkRequiredParam(['old_department'], $this->param);
            if (true !== $checkRequired) {
                return $checkRequired;
            }
            if (false === Dbi::getDbi()->existedDepartment($this->param['old_department'])) {
                return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'old_department.');
            }
            if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['old_department'])) {
                return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
            }
            
            $ret = Dbi::getDbi()->deletePatient($this->param['patient_id'],
                    $this->param['old_department'], $this->param['new_department']);
            if (VALUE_DB_ERROR === $ret) {
                return HpErrorMessage::getError(ERROR_DB);
            }
            return $this->retSuccess;
        }
        
        $ret = Dbi::getDbi()->setManageDepartment($this->param['patient_id'], $data);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        return $this->retSuccess;
    }
}
