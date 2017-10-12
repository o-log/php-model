<?php

const FIELD_OPERATION = 'a';
const OPERATION_ADD_MODEL = 'OPERATION_ADD_MODEL';

require_once '../vendor/autoload.php';

\PHPModelDemo\ModelDemoConfig::init();

echo '<div><a href="/">reload</a></div>';

//
// MODELS TEST
//

if (\OLOG\GET::optional(FIELD_OPERATION) == OPERATION_ADD_MODEL) {
    $new_model = new \PHPModelDemo\DemoModel();
    $new_model->title = rand(1, 1000);
    $new_model->save();
}

echo '<h2>Models <a href="/?' . FIELD_OPERATION . '=' . OPERATION_ADD_MODEL . '">+</a></h2>';
echo '<div>Class name: <b>' . \PHPModelDemo\DemoModel::class . '</b></div>';

$models_ids_arr = \PHPModelDemo\DemoModel::idsByCreatedAtDesc();

echo '<ul>';

foreach ($models_ids_arr as $model_id) {
    $model_obj = \PHPModelDemo\DemoModel::factory($model_id);
    echo '<li>' . $model_id . '</li>';
}

echo '</ul>';
