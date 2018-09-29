<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '业务员列表';
require 'header.php';

$salesmanList = DbiAdmin::getDbi()->getSalesmanList();
if (VALUE_DB_ERROR === $salesmanList) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$html = '';
foreach ($salesmanList as $value) {
    $html .= '<tr><td>' . $value['name'] . '</td><td><a href="edit_salesman.php?id=' . $value['salesman_id'] . '">点击编辑</a></td></tr>';
}


echo <<<EOF
  <button type="button" class="btn btn-lg btn-primary" onclick="location='add_salesman.php'">添加业务员</button>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>姓名</th>
        <th></th>
      </tr>
    </thead>
    <tbody>$html</tbody>
  </table>
EOF;
require 'tpl/footer.tpl';
