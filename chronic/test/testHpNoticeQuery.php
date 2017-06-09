<?php
require '../config/config.php';
require PATH_ROOT . 'lib/util/HpNoticeQuery.php';

$data1 = ['name' => 'test1', 'message' => 'message1', 'time' => '2017-01-01 00:00:00'];
$data2 = [
                ['name' => 'test1', 'message' => 'message1', 'time' => '2017-01-01 00:00:00'], 
                ['name' => 'test2', 'message' => 'message2', 'time' => '2017-01-02 00:00:00'],
                ['name' => 'test3', 'message' => 'message3', 'time' => '2017-01-03 00:00:00'],
                
];

$notice1 = new HpNoticeQuery('hospital', '1', DIMENSION_ONE, 'message');
$notice2 = new HpNoticeQuery('hospital', '2', DIMENSION_TWO, 'message');


$notice1->set($data1);
$notice2->set($data2);

$data3 = $notice1->getAll();
$data4 = $notice2->getAll('time');

echo 'get all data.<br />';
print_r($data3);
echo '<br />';
print_r($data4);
echo '<br />';
echo 'finished to get all data.<br /><br />';

echo 'get all data desc.<br />';
$data5 = $notice2->getAll('time', SORT_DESC);
print_r($data5);
echo '<br />';
echo 'finished to get all data desc.<br /><br />';

echo 'get one data of item2.<br />';
$data6 = $notice2->getOne(2);
print_r($data6);
echo '<br />';
echo 'finished to get one data.<br /><br />';

echo 'remove data of item2 and get data asc.<br />';
$notice2->delete(2);
$data7 = $notice2->getAll('time', SORT_ASC);
print_r($data7);
echo '<br />';
echo 'finished to get data after removing.<br /><br />';

echo 'delete data.<br />';
$notice1->delete();
$notice2->delete();

$data8 = $notice1->getAll();
$data9 = $notice2->getAll('time');
print_r($data8);
echo '<br />';
print_r($data9);
echo '<br />';
echo 'finished to get data after deleting.<br /><br />';