<?php

const FIELD_OPERATION = 'a';
const OPERATION_CACHE_SET = 'cache_set';
const OPERATION_ADD_MODEL = 'add_model';
const OPERATION_CACHE_INC = 'cache_inc';
const OPERATION_CACHE_DELETE = 'cache_del';

require_once '../vendor/autoload.php';

\PHPModelDemo\ModelDemoConfig::init();

\OLOG\Model\WebExecuteSQL::render(__DIR__ . '/../');

echo '<div><a href="/">reload</a></div>';

//
// CACHE TEST
//

$test_cache_key = 'test_key';

if (\OLOG\GETAccess::getOptionalGetValue('a') == OPERATION_CACHE_SET) {
    \OLOG\Cache\CacheWrapper::set($test_cache_key, 100);
}

if (\OLOG\GETAccess::getOptionalGetValue('a') == OPERATION_CACHE_INC) {
    \OLOG\Cache\CacheWrapper::increment($test_cache_key);
}

if (\OLOG\GETAccess::getOptionalGetValue('a') == OPERATION_CACHE_DELETE) {
    \OLOG\Cache\CacheWrapper::delete($test_cache_key);
}

echo '<h2>Cache test</h2>';
echo '<div>Cache engine class: <b>' . \OLOG\Cache\CacheConfig::getEngineClassname() . '</b></div>';

$test_value_from_cache = OLOG\Cache\CacheWrapper::get($test_cache_key);

echo '<div>Value from cache: <b>' . json_encode($test_value_from_cache) . '</b></div>';

echo '<div><a href="?' . FIELD_OPERATION . '=' . OPERATION_CACHE_SET . '">set value</a></div>';
echo '<div><a href="?' . FIELD_OPERATION . '=' . OPERATION_CACHE_INC . '">increment value</a></div>';
echo '<div><a href="?' . FIELD_OPERATION . '=' . OPERATION_CACHE_DELETE . '">delete value</a></div>';

//
// CONSTANT MODELS TEST
//

echo '<h2>Const models <a href="/?a=add_constmodel">+</a></h2>';
echo '<div>Class name: <b>' . \PHPModelDemo\ConstTest::class . '</b></div>';

if (\OLOG\GETAccess::getOptionalGetValue('a') == 'add_constmodel') {
    $new_model = new \PHPModelDemo\ConstTest();
    $new_model->setTitle(rand(1, 1000));
    $new_model->save();
}

$models_ids_arr = \PHPModelDemo\ConstTest::getAllIdsArrByCreatedAtDesc();

echo '<ol>';

foreach ($models_ids_arr as $model_id) {
    $model_obj = \PHPModelDemo\ConstTest::factory($model_id);
    echo '<li><b>' . $model_obj->getTitle() . '</b></li>';
}

echo '</ol>';

//
// MODELS TEST
//

if (\OLOG\GETAccess::getOptionalGetValue('a') == OPERATION_ADD_MODEL) {
    $new_model = new \PHPModelDemo\DemoModel();
    $new_model->setTitle(rand(1, 1000));
    $new_model->save();
}

echo '<h2>Models <a href="/?' . FIELD_OPERATION . '=' . OPERATION_ADD_MODEL . '">+</a></h2>';
echo '<div>Class name: <b>' . \PHPModelDemo\DemoModel::class . '</b></div>';

$models_ids_arr = \OLOG\DB\DBWrapper::readColumn(
    \PHPModelDemo\ModelDemoConfig::DB_NAME_PHPMODELDEMO,
    'select id from ' . \PHPModelDemo\DemoModel::DB_TABLE_NAME . ' order by id desc'
);

echo '<ul>';

foreach ($models_ids_arr as $model_id) {
    $model_obj = \PHPModelDemo\DemoModel::factory($model_id);
    echo '<div>' . $model_obj->getTitle() . '</div>';
}

echo '</ul>';
