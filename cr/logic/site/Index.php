<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
//require_once PATH_ROOT . 'lib/db/Dbi.php';
//require_once PATH_ROOT . 'lib/tool/HpApi.php';

class Index extends BaseLogicSite
{
    protected function execute()
    {
        $this->setTpl(__CLASS__);
        /*
        $api = new HpApi();
        $ret = $api->getJson('api_get_hospital_list');
        var_dump($ret);
        */
        $model = ['message' => 'I am index page.'];
        $this->setModel($model);
    }
}
