<?php
require PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['answer'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'answer.']);
}

$id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;
$question = isset($_POST['question']) && !empty($_POST['question']) ? $_POST['question'] : null;

$ret = DbiAdmin::getDbi()->addSolution($id, $question, $answer);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
