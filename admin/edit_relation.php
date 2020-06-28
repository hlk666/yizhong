<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '配置上级医院';
require 'header.php';

if (isset($_POST['save']) || isset($_POST['add'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要重复刷新页面。', 2000, 'edit_hospital.php');
    }
    $hospitalId = isset($_POST['hospital_id']) ? $_POST['hospital_id'] : null;
    if (empty($hospitalId)) {
        user_back_after_delay('非法访问。');
    }
    
    if (isset($_POST['save'])) {
        $newParentId = $_POST;
        unset($newParentId['save']);
        unset($newParentId['hospital_id']);
        $newParentId = array_keys($newParentId);
        
        $ret = DbiAdmin::getDbi()->delHospitalRelation($hospitalId, $newParentId);
    }
    if (isset($_POST['add'])) {
        $parentHospital = isset($_POST['hospital_parent']) ? $_POST['hospital_parent'] : null;
        if (empty($parentHospital)) {
            user_back_after_delay('请选择上级医院。');
        }
        
        $ret = DbiAdmin::getDbi()->addHospitalParent($hospitalId, $parentHospital);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://101.200.174.235/batch/refresh_relation.php');
    $data = curl_exec($ch);
    curl_close($ch);
    //@todo send a message to device which is belong to the hospital
    
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS
    . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" '
            . ' onclick="javascript:location.href=\'edit_relation.php?id=' . $hospitalId . '\';">刷新查看</button>';
} else {
    $hospitalId = isset($_GET['id']) ? $_GET['id'] : null;
    $_SESSION['post'] = false;
    
    $hospitalParent = DbiAdmin::getDbi()->getHospitalParent($hospitalId);
    $parentId = array();
    if (VALUE_DB_ERROR === $hospitalParent) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $htmlHospitalParent = '<h4><font color="#f0ad4e">';
    foreach ($hospitalParent as $value) {
        $parentId[] = $value['hospital_id'];
        $htmlHospitalParent .= '<div class="col-xs-12 col-sm-4"><input type="checkbox" name="' 
                . $value['hospital_id'] . '" checked>' . $value['hospital_name'] . '</div>';
    }
    $htmlHospitalParent .= '</font></h4>';
    
    $ret = DbiAdmin::getDbi()->getHospitalParentList();
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $htmlHospitalParentList = '';
    foreach ($ret as $value) {
        if (in_array($value['hospital_id'], $parentId)) {
            continue;
        }
        $htmlHospitalParentList .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
    
    if (in_array($_SESSION['user'], $auth_level1)) {
        $authEditRelationSubmitEdit = '<button type="submit" class="btn btn-lg btn-info" name="save">保存修改</button>';
        $authEditRelationSubmitAdd = '<button type="submit" class="btn btn-lg btn-info" name="add">添加新的上级医院</button>';
    } else {
        $authEditRelationSubmitEdit = '';
        $authEditRelationSubmitAdd = '';
    }
    
    if (!empty($hospitalParent)) {
        echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <input type="hidden" name="hospital_id" value="$hospitalId">
  <div class="form-group"><h3>上级医院信息：</h3>$htmlHospitalParent</div>
  <div class="form-group">
    <div class="col-sm-offset-4 col-sm-2">
      $authEditRelationSubmitEdit
    </div>
  </div>
</form>
<hr style="border-top:1px ridge red;" />
EOF;
    }
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <input type="hidden" name="hospital_id" value="$hospitalId">
  <h3>添加新的上级医院：</h3>
  <div class="row"><h4>
    <div class="col-sm-6">
      <select class="form-control" name="hospital_parent" style="margin:6px 0 6px 0;">
        <option value="0">请选择上级医院</option>$htmlHospitalParentList
      </select>
    </div>
    <div class="col-sm-6">
      $authEditRelationSubmitAdd
    </div>
  </h4></div>
</form>
<hr style="border-top:1px ridge red;" />
<div class="form-group">
  <div class="col-sm-offset-4 col-sm-2">
    <button type="button" class="btn btn-lg btn-primary" onclick="javascript:history.back();">返回</button>
  </div>
</div>
EOF;
}

require 'tpl/footer.tpl';
