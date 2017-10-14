<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddReferralConfirm extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['referral_id', 'apply_department_id', 'doctor_id', 'patient_id', 'confirm_department'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['referral_id', 'apply_department_id', 'doctor_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedReferral($this->param['referral_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'referral_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['apply_department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'apply_department_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->addReferralConfirm($this->param['referral_id'], $this->param['doctor_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $patientInfo = Dbi::getDbi()->getPatientDepartment($this->param['patient_id']);
        if (VALUE_DB_ERROR === $patientInfo) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($patientInfo)) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY);
        }
        
        if ($patientInfo['department1'] != $this->param['apply_department_id'] && $patientInfo['department1'] != $this->param['confirm_department']) {
            $oldDpt = $patientInfo['department1'];
        } elseif ($patientInfo['department2'] != $this->param['apply_department_id'] && $patientInfo['department2'] != $this->param['confirm_department']) {
            $oldDpt = $patientInfo['department2'];
        } elseif ($patientInfo['department3'] != $this->param['apply_department_id'] && $patientInfo['department3'] != $this->param['confirm_department']) {
            $oldDpt = $patientInfo['department3'];
        } else {
            //can not happen.
        }
    
        $ret = Dbi::getDbi()->deletePatient($this->param['patient_id'], $oldDpt, $this->param['confirm_department']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        send_notice($this->param['apply_department_id'], '转诊病人确认到院，转诊ID：' . $this->param['referral_id']);
        
        return $this->retSuccess;
    }
}
