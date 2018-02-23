<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class WxUploadFile extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        $required = ['patient_id', 'examination_id'];
        
        $checkRequired = HpValidate::checkRequiredParam($required, $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        /*
        if (false === Dbi::getDbi()->existedPatient($this->param['patient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'patient_id.');
        }
        
        if (false === Dbi::getDbi()->existedExamination($this->param['examination_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'examination_id.');
        }
        
        if (isset($this->param['follow_record_id']) && !empty($this->param['follow_record_id']) 
                && false === Dbi::getDbi()->existedFollowRecord($this->param['follow_record_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'follow_record_id.');
        }
        
        if (isset($this->param['outpatient_id']) && !empty($this->param['outpatient_id'])
                && false === Dbi::getDbi()->existedOutPatient($this->param['outpatient_id'])) {
            return HpErrorMessage::getError(ERROR_DATA_CONSISTENCY, 'outpatient_id.');
        }
        */
        if (!isset($_FILES['file']['name']) || !isset($_FILES['file']['tmp_name'])) {
            return HpErrorMessage::getError(ERROR_UPLOAD_NO_DATA);
        }
        
        if (!empty($_FILES['file']['error'])) {
            return HpErrorMessage::getError($_FILES['file']['error']);
        }
        
        return true;
    }
    
    protected function execute()
    {
        $dir = PATH_ROOT . 'upload' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $clientFileName = $_FILES['file']['name'];
        $rowNo = '_' . (isset($this->param['row_no']) ? $this->param['row_no'] : '1');
        $fileName = $this->param['patient_id'] . '_' . $this->param['examination_id']
            . '_' . date('His') . $rowNo . substr($clientFileName, strrpos($clientFileName, '.'));
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $dir . $fileName)) {
            $url = 'upload/' . date('Ymd') . '/' . $fileName;
            
            if (isset($this->param['follow_record_id']) && !empty($this->param['follow_record_id'])) {
                $type = 'follow';
                $recordId = $this->param['follow_record_id'];
            } elseif (isset($this->param['outpatient_id']) && !empty($this->param['outpatient_id'])) {
                $type = 'outpatient';
                $recordId = $this->param['outpatient_id'];
            } else {
                $type = 'app';
                $recordId = null;
            }
            $ret = Dbi::getDbi()->addRecordExamination($this->param['patient_id'], 
                    $this->param['examination_id'], $url, '', $type, $recordId);
            if (VALUE_DB_ERROR === $ret) {
                return HpErrorMessage::getError(ERROR_DB);
            }
        } else {
            return HpErrorMessage::getError(ERROR_UPLOAD_FAIL);
        }
        return $this->retSuccess;
    }
}
