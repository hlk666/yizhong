<?php
require_once PATH_ROOT . 'logic/BaseLogicApi.php';
require_once PATH_ROOT . 'lib/util/HpDataFile.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class AddExamination extends BaseLogicApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        $checkRequired = HpValidate::checkRequiredParam(['case_id', 'examination'], $this->param);
        if (true !== $checkRequired) {
            return $checkRequired;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $idList = array();
        $resultList = array();
        
        $file = HpDataFile::getDataFile('dictionaryExamination');
        if (false === $file) {
            $examinations = array();
        } else {
            include $file;
        }
        $beforeCount = count($examinations);
        
        $examData = $this->getStructalData($this->param['examination']);
        foreach ($examData as $exam) {
            foreach ($exam as $examName => $examResult) {
                $key = array_search($examName, $examinations);
                if (false === $key) {
                    $newKey = count($examinations) + 1;
                    $examinations[$newKey] = $examName;
                
                    $idList[] = $newKey;
                }
                else {
                    $idList[] = $key;
                }
                $resultList[] = $examResult;
            }
        }
        if ($beforeCount != count($examinations)) {
            HpDataFile::setDataFile('dictionaryExamination', ['examinations' => $examinations]);
        }
        
        $ret = Dbi::getDbi()->addExaminations($this->param['case_id'], $idList, $resultList);
        if (VALUE_DB_ERROR === $ret) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        
        return $this->retSuccess;
    }
}
