<?php
require '../config/config.php';
require_once PATH_LIB . 'function.php';;
require_once PATH_LIB . 'DataFile.php';
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Logger.php';

$title = '设置APP版本更新';
require 'header.php';

$updateLog = 'app_update.log';

if (isset($_POST['submit'])) {
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。');
    }
    $version = isset($_POST['version']) ? $_POST['version'] : null;
    $city = isset($_POST['city']) ? $_POST['city'] : null;
    $hospitalId = isset($_POST['hospital']) ? $_POST['hospital'] : null;
    if (empty($version)) {
        user_back_after_delay('请输入版本号。');
    }
    if (empty($city)) {
        user_back_after_delay('请选择省份。');
    }
    
    $ret = DbiAdmin::getDbi()->getDeviceIdList($city, $hospitalId);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $dataFile = DataFile::getDataFile('app_update', $city);
    if (false === $dataFile) {
        $device = array();
    } else {
        include $dataFile;
    }
    foreach ($ret as $value) {
        $device[$value['device_id']] = $version;
        Logger::write($updateLog, 'set update with ID : ' . $value['device_id'] . ', version : ' . $version);
    }
    //$device = array_unique($device);
    
    $retIO = DataFile::setDataFile('app_update', $city, ['device' => $device]);
    if (false === $retIO) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $_SESSION['post'] = true;
    echo '<font color="red">' . MESSAGE_SUCCESS . '</font><br>' 
        . '<button type="button" class="btn btn-lg btn-primary" type="margin-top:10px;" 
                onclick="javascript:location.href=\'app_set_update.php\';">返回前一页</button>';
} else {
    $_SESSION['post'] = false;
    $ret = DbiAdmin::getDbi()->getDeviceBloc();
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $hospitalInfo = DbiAdmin::getDbi()->getHospitalList();
    if (VALUE_DB_ERROR === $hospitalInfo) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $hospitalNames = array();
    foreach ($hospitalInfo as $row) {
        $hospitalNames[$row['hospital_id']] = $row['hospital_name'];
    }
    
    $city = array();
    $hospital = array();
    foreach ($ret as $deviceInfo) {
        $city[] = $deviceInfo['city'];
        $hospital[$deviceInfo['city']][] = $deviceInfo['hospital_id'];
    }
    
    $jsHospital = 'var arrayHospitalId = [';
    foreach ($hospital as $value) {
        $jsHospital .= '[';
        foreach ($value as $subValue) {
            $jsHospital .= '"' . $subValue . '",';
        }
        $jsHospital .= '],';
    }
    $jsHospital .= '];';
    
    $jsHospital .= 'var arrayHospital = [';
    foreach ($hospital as $value) {
        $jsHospital .= '[';
        foreach ($value as $subValue) {
            if (array_key_exists($subValue, $hospitalNames)) {
                $jsHospital .= '"' . $hospitalNames[$subValue] . '",';
            } else {
                $jsHospital .= '"' . $subValue . '",';
            }
        }
        $jsHospital .= '],';
    }
    $jsHospital .= '];';
    $jsHospital = str_replace(',]', ']', $jsHospital);
    
    $city = array_unique($city);
    $htmlCity = '';
    foreach ($city as $value) {
        switch ($value) {
            case 99:
                $province = '测试专用';
                break;
            case 37:
                $province = '山东省';
                break;
            case 34:
                $province = '安徽省';
                break;
            default:
                $province = '错误数据';
        }
        $htmlCity .= "<option value='$value'>$province</option>";
    }
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post" name="formCityHospital">
  <div class="form-group"><h3 style="color:red">请放置新版本的apk文件后再设置更新信息。</h3></div>
  <div class="form-group">
    <label for="version" class="col-sm-2 control-label">版本号<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="version" name="version" placeholder="请输入版本号(必须)" required>
    </div>
  </div>
  <div class="form-group">
    <label for="city" class="col-sm-2 control-label">选择省份<font color="red">*</font></label>
    <div class="col-sm-10">
      <select class="form-control" name="city" onChange="getHospital()">
        <option value="0">请选择省份(必须)</option>$htmlCity
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="hospital" class="col-sm-2 control-label">选择医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital">
        <option value="0">请选择医院(如果不选择，将更新该省所有设备)</option>
      </select>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px"
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
<script language="JavaScript" type="text/javascript">
    $jsHospital
    function getHospital(){
        var objCity = document.formCityHospital.city;
        var objHospital = document.formCityHospital.hospital;
        var cityHospitalId = arrayHospitalId[objCity.selectedIndex - 1];
        var cityHospital = arrayHospital[objCity.selectedIndex - 1];
        objHospital.length=1;
        for (var i = 0; i < cityHospital.length; i++){
            objHospital[i + 1] = new Option(cityHospital[i], cityHospitalId[i]);
         }
    }
</script>
EOF;
}

require 'tpl/footer.tpl';
