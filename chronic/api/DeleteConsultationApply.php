<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class DeleteConsultationApply extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $required = ['consultation_id', 'reply_department_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        $checkNumeric = HpValidate::checkNumeric(['consultation_id'], $this->param);
        if (true !== $checkNumeric) {
            return $checkNumeric;
        }
        
        if (false === Dbi::getDbi()->existedConsultation($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'consultation_id.');
        }
        
        if (true === Dbi::getDbi()->existedConsultationReplied($this->param['consultation_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_DELETE_DENY);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $ret = Dbi::getDbi()->deleteConsultationApply($this->param['consultation_id']);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        send_notice($this->param['reply_department_id'], '有新的会诊申请被删除，会诊ID：' . $this->param['consultation_id']);
        
        return $this->retSuccess;
    }
}
