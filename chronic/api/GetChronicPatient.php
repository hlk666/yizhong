<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetChronicPatient extends BaseApi
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
        /*
        if (!isset($this->param['page']) && !isset($this->param['rows'])) {
            $hospitals = Dbi::getDbi()->getHospitalList();
        } else {
            $page = isset($this->param['page']) ? $this->param['page'] : 0;
            $rows = isset($this->param['rows']) ? $this->param['rows'] : VALUE_DEFAUTL_ROWS;
            $hospitals = Dbi::getDbi()->getHospitalList($page * $rows, $rows);
        }
        if (VALUE_DB_ERROR === $hospitals) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($hospitals)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        */
        $chronicPatient = Dbi::getDbi()->getChronicPatient($this->param['department_id']);
        if (VALUE_DB_ERROR === $chronicPatient) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($chronicPatient)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        
        $currentChronic = 0;
        $currentChronicCount = 0;
        $chronicList = array();
        $patientList = array();
        $length = count($chronicPatient);
        for ($i = 0; $i < $length; $i++) {
            if ($currentChronic != $chronicPatient[$i]['chronic_id']) {
                if ($currentChronic != 0) {
                    $chronicList[] = array(
                                    'chronic_id' => $chronicPatient[$i - 1]['chronic_id'], 
                                    'chronic_name' => $chronicPatient[$i - 1]['chronic_name'],
                                    'patient_count' => $currentChronicCount 
                    );
                }
                $currentChronic = $chronicPatient[$i]['chronic_id'];
                if (!empty($chronicPatient[$i]['patient_id'])) {
                    $currentChronicCount = 1;
                } else {
                    $currentChronicCount = 0;
                }
            } else {
                if (!empty($chronicPatient[$i]['patient_id'])) {
                    $currentChronicCount++;
                } else {
                    //do nothing.
                }
            }
            
            if ($i == $length - 1) {
                $chronicList[] = array(
                                'chronic_id' => $chronicPatient[$i]['chronic_id'],
                                'chronic_name' => $chronicPatient[$i]['chronic_name'],
                                'patient_count' => $currentChronicCount
                );
            }
        }
        for ($i = 0; $i < $length; $i++) {
            if (!empty($chronicPatient[$i]['patient_id'])) {
                $patientList[] = array(
                                'patient_id' => $chronicPatient[$i]['patient_id'],
                                'patient_name' => $chronicPatient[$i]['patient_name'],
                                'chronic_id' => $chronicPatient[$i]['chronic_id'],
                                'level' => $chronicPatient[$i]['level'],
                                'parent_id' => $chronicPatient[$i]['parent_id'],
                );
            }
        }
        
        $this->retSuccess['chronic_list'] = $chronicList;
        $this->retSuccess['patient_list'] = $patientList;
        return $this->retSuccess;
    }
}
