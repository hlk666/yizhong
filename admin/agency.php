<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '代理商列表';
require 'header.php';

$agencyList = DbiAdmin::getDbi()->getAgencyList();
if (VALUE_DB_ERROR === $agencyList) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$html = '';
foreach ($agencyList as $value) {
    $html .= '<tr><td>' . $value['name'] . '</td><td>' . $value['agency_tel'] . '</td><td>' . $value['salesman_name']
        . '</td><td><a href="edit_agency.php?id=' . $value['agency_id'] . '">点击编辑</a></td></tr>';
}


echo <<<EOF
  <button type="button" class="btn btn-lg btn-primary" onclick="location='add_agency.php'">添加代理商</button>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>代理商名</th>
        <th>电话</th>
        <th>业务员</th>
        <th></th>
      </tr>
    </thead>
    <tbody>$html</tbody>
  </table>
EOF;
require 'tpl/footer.tpl';
