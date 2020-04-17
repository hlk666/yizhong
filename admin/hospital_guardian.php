<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院开单统计';
require 'header.php';

$agencyId = isset($_GET['agency']) ? $_GET['agency'] : '';
if (empty($agencyId)) {
    user_back_after_delay(MESSAGE_PARAM);
}

$ret = DbiAdmin::getDbi()->getHospitalGuardianAgency($agencyId);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

$htmlData = '';
foreach ($ret as $value) {
    $htmlData .= '<tr><td>' 
        . $value['hospital_name'] . '</td><td>'
        . $value['qty'] . '</td></tr>';
}
echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>医院ID</th>
        <th>数量</th>
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
