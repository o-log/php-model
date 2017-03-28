<?php

require_once '../vendor/autoload.php';

\PHPModelDemo\ModelDemoConfig::init();

echo '<div><a href="/">Main</a></div>';

/*
$new_model = new \PHPModelDemo\SomeModel();
$new_model->save();
*/

$test_key = 'test_key';

if (\OLOG\GETAccess::getOptionalGetValue('a') == 'cache_set'){
    \OLOG\Cache\CacheWrapper::set($test_key, 100);
}

if (\OLOG\GETAccess::getOptionalGetValue('a') == 'cache_inc'){
    \OLOG\Cache\CacheWrapper::increment($test_key);
}

if (\OLOG\GETAccess::getOptionalGetValue('a') == 'cache_del'){
    \OLOG\Cache\CacheWrapper::delete($test_key);
}

echo '<h2>Cache test</h2>';

$test_value_from_cache = OLOG\Cache\CacheWrapper::get($test_key);

echo '<div><code>' . json_encode($test_value_from_cache) . '</code></div>';

echo '<div><a href="?a=cache_set">set value</a></div>';
echo '<div><a href="?a=cache_inc">increment value</a></div>';
echo '<div><a href="?a=cache_del">delete value</a></div>';

echo '<h2>Models test</h2>';

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
    \PHPModelDemo\ModelDemoConfig::DB_NAME_PHPMODELDEMO,
    'select id from ' . \PHPModelDemo\DemoModel::DB_TABLE_NAME . ' order by id desc'
);

echo '<ul>';

foreach ($models_ids_arr as $model_id){
    $model_obj = \PHPModelDemo\DemoModel::factory($model_id);
    echo '<div>' . $model_obj->getTitle() . '</div>';
}

echo '</ul>';

echo '<div>CONST MODELS <a href="/?a=add_constmodel">+</a></div>';

// ACTIONS

if (isset($_GET['a'])){
    if ($_GET['a'] == 'add_constmodel'){
        $new_model = new \PHPModelDemo\ConstTest();
        $new_model->setTitle(rand(1, 1000));
        $new_model->save();
    }
}

// DISPLAY

/*
$models_ids_arr = \OLOG\DB\DBWrapper::readColumn(
    \PHPModelDemo\ModelDemoConfig::DB_NAME_PHPMODELDEMO,
    'select id from ' . \PHPModelDemo\DemoModel::DB_TABLE_NAME . ' order by id desc'
);
*/

$models_ids_arr = \PHPModelDemo\ConstTest::getAllIdsArrByCreatedAtDesc();

echo '<ul>';

foreach ($models_ids_arr as $model_id){
    $model_obj = \PHPModelDemo\ConstTest::factory($model_id);
    echo '<div>' . $model_obj->getTitle() . '</div>';
}

echo '</ul>';
