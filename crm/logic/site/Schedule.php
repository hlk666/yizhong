<?php
require_once PATH_ROOT . 'logic/BaseLogicSite.php';
require_once PATH_ROOT . 'lib/db/DbiCRM.php';
require_once PATH_ROOT . 'lib/util/HpPaging.php';

class Schedule extends BaseLogicSite
{
    protected function execute()
    {
        $this->setTpl(__CLASS__);
        
        $userId = isset($_GET['user']) ? $_GET['user'] : $_SESSION['user'];
        $allSchedule = DbiCRM::getDbi()->getScheduleList($userId);
        if (VALUE_DB_ERROR === $allSchedule) {
            display_error_page('数据库错误。');
        }
        $count = count($allSchedule);
        
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $rows = 10;
        $offset = ($page - 1) * $rows;
        $lastPage = ceil($count / $rows);
        $currentPage = 'schedule?user=' . $userId;
        $paging = HpPaging::getPaging($page, $lastPage, $currentPage);
        
        if ($page == 1) {
            $schedule = array_slice($allSchedule, 0, $rows);
        } else {
            $schedule = DbiCRM::getDbi()->getScheduleList($userId, $offset, $rows);
        }
        $noData = empty($schedule) ? true : false;
        
        $model = ['subTitle' => '客户推动记录', 'noData' => $noData, 'schedule' => $schedule, 'paging' => $paging];
        $this->setModel($model);
    }
}
