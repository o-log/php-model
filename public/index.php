<?php

require_once '../vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\PHPModelDemo\Config::get());

echo '<div>MODELS <a href="/?a=add_model">+</a></div>';

// ACTIONS

if (isset($_GET['a'])){
    if ($_GET['a'] == 'add_model'){
        $new_model = new \PHPModelDemo\DemoModel();
        $new_model->setTitle(rand(1, 1000));
        $new_model->save();
    }
}

// DISPLAY

$models_ids_arr = \OLOG\DB\DBWrapper::readColumn(
    \PHPModelDemo\Config::DB_NAME_PHPMODELDEMO,
    'select id from ' . \PHPModelDemo\DemoModel::DB_TABLE_NAME . ' order by id desc'
);

echo '<ul>';

foreach ($models_ids_arr as $model_id){
    $model_obj = \PHPModelDemo\DemoModel::factory($model_id);
    echo '<div>' . $model_obj->getTitle() . '</div>';
}

echo '</ul>';