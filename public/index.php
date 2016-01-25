<?php

require_once '../vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\PHPModelTest\Config::get());

echo '<div>MODELS <a href="/?a=add_model">+</a></div>';

// ACTIONS

if (isset($_GET['a'])){
    if ($_GET['a'] == 'add_model'){
        $new_model = new \PHPModelTest\TestModel();
        $new_model->setTitle(rand(1, 1000));
        $new_model->save();
    }
}

// DISPLAY

$models_ids_arr = \OLOG\DB\DBWrapper::readColumn(
    \PHPModelTest\Config::DB_NAME_PHPMODELTEST,
    'select id from ' . \PHPModelTest\TestModel::DB_TABLE_NAME . ' order by id desc'
);

echo '<ul>';

foreach ($models_ids_arr as $model_id){
    $model_obj = \PHPModelTest\TestModel::factory($model_id);
    echo '<div>' . $model_obj->getTitle() . '</div>';
}

echo '</ul>';