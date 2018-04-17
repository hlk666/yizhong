<?php

$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'erp' . DIRECTORY_SEPARATOR . 'changed_hospital.txt';
unlink($file);

api_exit_success();
