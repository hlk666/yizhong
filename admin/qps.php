<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require_once PATH_LIB . 'DataFile.php';

function hourToAllTime($hour)
{
    if (strlen($hour) == 10) {
        return substr($hour, 0, 4) . '-' . substr($hour, 4, 2) . '-' . substr($hour, 6, 2) 
            . ' ' . substr($hour, 8, 2) . ':00:00';
    } else {
        return '';
    }
}

if (isset($_GET['time'])) {
    $title = '<font color="red">' . hourToAllTime($_GET['time']) . '</font>';
    require 'header.php';
    
    $txtFile = URL_ROOT . 'data/qps/' . $_GET['time'] . '.txt';
    echo <<<EOF
<div id="lineChart" style="width:100%;"></div>
<div style="margin-top:10px;text-align:center;">
  <button type="button" class="btn btn-lg btn-primary" onclick="javascript:history.back();">返回前一页</button>
</div>
<script src="js/dygraph-combined.js"></script>
<script type="text/javascript">
    g = new Dygraph(
      document.getElementById("lineChart"),
      "$txtFile",
      {
        color: 'lightblue',
        legend: 'always'
      }
    );
  </script>
EOF;
} else {
    $title = '近24小时并发信息';
    require 'header.php';
    
    $dataFile = DataFile::getDataFile('qps', date('Ym', strtotime('-1 hour')));
    if (false === $dataFile) {
        echo '缓存文件不存在，请联系系统管理员。';
        require 'tpl/footer.tpl';
        exit;
    }
    include $dataFile;
    
    $html = '';
    $keys = array_keys($requestMonth);
    $last = count($requestMonth) - 1;
    $first = count($requestMonth) >= 24 ? count($requestMonth) - 24 : 0;
    for ($i = $last; $i >= $first; $i--) {
        if ($requestMonth[$keys[$i]]['maxRequest'] >= 20) {
            $maxRequest = '<font style="color:red;font-weight:bold;">' 
                    . $requestMonth[$keys[$i]]['maxRequest'] . '</font>';
        } elseif ($requestMonth[$keys[$i]]['maxRequest'] >= 10) {
            $maxRequest = '<font style="color:red;">' . $requestMonth[$keys[$i]]['maxRequest'] . '</font>';
        } else {
            $maxRequest = $requestMonth[$keys[$i]]['maxRequest'];
        }
        $html .= '<tr><td>' . hourToAllTime($keys[$i]) . '</td><td>' 
            . $maxRequest . '</td><td>' 
            . $requestMonth[$keys[$i]]['maxRequestCount'] . '</td><td>' 
            . '<button type="button" class="btn btn-xs btn-info" ' 
                . ' onclick="javascript:location.href=\'qps.php?time=' . $keys[$i] . '\';">点击查看</button>';
    }
    
    echo <<<EOF
    <table class="table table-striped">
    <thead>
      <tr>
        <th>时间</th>
        <th>最大并发值</th>
        <th>出现次数</th>
        <th>详细信息</th>
      </tr>
    </thead>
    <tbody>$html</tbody>
  </table>
EOF;
}

require 'tpl/footer.tpl';
