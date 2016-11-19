<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
require_once PATH_ROOT . 'lib/db/DbiCRM.php';
require_once PATH_ROOT . 'lib/util/HpPaging.php';

class User extends BaseLogicSite
{
    protected function execute()
    {
        $this->setTpl(__CLASS__);
        
        $allUser = DbiCRM::getDbi()->getUserList();
        if (VALUE_DB_ERROR === $allUser) {
            display_error_page('数据库错误。');
        }
        $count = count($allUser);
        
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $rows = 10;
        $offset = ($page - 1) * $rows;
        $lastPage = ceil($count / $rows);
        $paging = HpPaging::getPaging($page, $lastPage);
        
        if ($page == 1) {
            $user = array_slice($allUser, 0, $rows);
        } else {
            $user = DbiCRM::getDbi()->getUserList($offset, $rows);
        }
        $noData = empty($user) ? true : false;
        
        $model = ['subTitle' => '客户推动记录', 'noData' => $noData, 'user' => $user, 'paging' => $paging];
        $this->setModel($model);
    }
}
