<?php
require PATH_ROOT . 'lib/analysis/AnalysisUpload.php';

$data = file_get_contents('php://input');

$upload = new AnalysisUpload();
echo $upload->run($_GET, $data, 'hbi');
