<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetExaminationMst extends BaseApi
{
    protected function validate($class = '')
    {
        $ret = parent::validate(__CLASS__);
        if (true !== $ret) {
            return $ret;
        }
        
        return true;
    }
    
    protected function execute()
    {
        $mst = Dbi::getDbi()->getExaminationMst();
        if (VALUE_DB_ERROR === $mst) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($mst)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['examination_mst'] = $mst;
        return $this->retSuccess;
    }
}
