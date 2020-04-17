<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '代理商开单统计';
require 'header.php';

$ret = DbiAdmin::getDbi()->getAgencyGuardian();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlData = '';
foreach ($ret as $value) {
    $htmlData .= '<tr><td>' 
        . $value['agency_name'] . '</td><td>'
        . $value['qty'] . '</td><td>'
        . '<a href="hospital_guardian.php?agency=' . $value['agency_id'] . '">查看</a></td></tr>';
}
echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>代理商</th>
        <th>数量</th>
        <th>查看</th>
      </tr>
    </thead>
    <tbody>$htmlData</tbody>
  </table>
  <div class="col-sm-offset-4 col-sm-4">
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px" 
        onclick="javascript:history.back();">返回</button>
    </div>
EOF;
require 'tpl/footer.tpl';
