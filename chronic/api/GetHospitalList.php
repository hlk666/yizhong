<?php
require_once 'BaseApi.php';
require_once PATH_ROOT . 'lib/db/Dbi.php';

class GetHospitalList extends BaseApi
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
        $hospitalList = Dbi::getDbi()->getHospitalList();
        if (VALUE_DB_ERROR === $hospitalList) {
            return HpErrorMessage::getError(ERROR_DB);
        }
        if (empty($hospitalList)) {
            return HpErrorMessage::getError(ERROR_NO_DATA);
        }
        $this->retSuccess['hospital_list'] = $hospitalList;
        return $this->retSuccess;
    }
}
