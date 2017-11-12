<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';
require_once PATH_ROOT . 'lib/tool/HpShortMessageService.php';

class AddConsultationApply extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['apply_department_id', 'patient_id', 'doctor_id', 'message', 'reply_department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['apply_department_id', 'patient_id', 'doctor_id', 'reply_department_id'], 
                $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['apply_department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'apply_department_id.');
        }
        
        if (false === Dbi::getDbi()->existedDepartment($this->param['reply_department_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'reply_department_id.');
        }
        
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedDoctorById($this->param['doctor_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'doctor_id.');
        }
        
        if (false === Dbi::getDbi()->isPatientInDepartment($this->param['patient_id'], $this->param['apply_department_id'])) {
            return HpErrorMessage::getError(ERROR_NOT_IN_DEPARTMENT);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $consultationId = Dbi::getDbi()->addConsultationApply($this->param['apply_department_id'], $this->param['patient_id'], 
                $this->param['doctor_id'], $this->param['message'], $this->param['reply_department_id']);
        if (VALUE_DB_ERROR === $consultationId) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        $tel = Dbi::getDbi()->getTelDepartment($this->param['reply_department_id']);
        if (VALUE_DB_ERROR === $tel) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (!empty($tel)) {
            HpShortMessageService::send($tel, "有新的会诊申请，请确认。");
        }
        send_notice($this->param['reply_department_id'], '有新的会诊申请，请确认。');
        
        $this->retSuccess['consultation_id'] = $consultationId;
        return $this->retSuccess;
    }
}
