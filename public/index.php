<?php

const FIELD_OPERATION = 'a';
const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';
const OPERATION_DELETE_MODEL = 'OPERATION_DELETE_MODEL';

require_once '../vendor/autoload.php';

\Config\Config::init();

echo '<div><a href="/">reload</a></div>';

//
// MODELS TEST
//

if (\OLOG\GET::optional(FIELD_OPERATION) == OPERATION_ADD_MODEL) {
    $new_model = new \PHPModelDemo\DemoModel();
    $new_model->title = rand(1, 1000);
    $new_model->bool_val = rand(0, 2) ? false : true;
    $new_model->save();
}

if (\OLOG\GET::optional(FIELD_OPERATION) == OPERATION_DELETE_MODEL) {
    $model = \PHPModelDemo\DemoModel::factory(\OLOG\GET::required('id'));
    $model->delete();
}

echo '<h2>Models <a href="/?' . FIELD_OPERATION . '=' . OPERATION_ADD_MODEL . '">+</a></h2>';
echo '<div>Class name: <b>' . \PHPModelDemo\DemoModel::class . '</b></div>';

$models_ids_arr = \PHPModelDemo\DemoModel::idsByCreatedAtDesc(0, 10);

echo '<ul>';

foreach ($models_ids_arr as $model_id) {
    $model = \PHPModelDemo\DemoModel::factory($model_id);
    echo '<li>' . print_r($model, true) . ' <a href="/?' . FIELD_OPERATION . '=' . OPERATION_DELETE_MODEL . '&id=' . $model_id . '">delete</a></li>';
}

echo '</ul>';

$objs = \PHPModelDemo\DemoModel::idsToObjs($models_ids_arr);

echo '<ul>';

foreach ($objs as $model) {
    echo '<li>' . print_r($model, true) . '</li>';
}

echo '</ul>';

echo '<b>First: </b>';
print_r(\PHPModelDemo\DemoModel::first($objs, false));
