<?php
require 'common.php';
require_once PATH_LIB . 'db/DbiBatch.php';

$logFile = 'batch_move_data.log';
Logger::writeBatch($logFile, 'start to move data.');

$tableFrom = 'ecg';
$tableTo = 'ecg_history';
$field = 'guardian_id';

$fieldFrom = DbiBatch::getDbi()->getDiffEcgIdFrom();
if (VALUE_DB_ERROR === $fieldFrom) {
    Logger::writeBatch($logFile, 'can not get filedFrom.');
    exit;
}

$fieldTo = DbiBatch::getDbi()->getDiffEcgIdTo();
if (VALUE_DB_ERROR === $fieldTo) {
    Logger::writeBatch($logFile, 'can not get filedTo.');
    exit;
}

if ($fieldFrom == $fieldTo) {
    Logger::writeBatch($logFile, "no data need to be moved.");
    exit;
}

$ret = DbiBatch::getDbi()->moveData($tableFrom, $tableTo, $field, $fieldFrom, $fieldTo);
if (VALUE_DB_ERROR === $ret) {
    Logger::writeBatch($logFile, "failed to move data from $tableFrom to $tableTo($fieldFrom - $fieldTo).");
    exit;
}

Logger::writeBatch($logFile, "batch succeed to move data from $tableFrom to $tableTo($fieldFrom - $fieldTo).");
exit;
