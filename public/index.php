<?php

require_once '../vendor/autoload.php';

$models_ids_arr = \OLOG\DB\DBWrapper::readColumn(
    \PHPModelTest\Conf::DB_NAME_PHPMODELTEST,
    'select id from ' . \PHPModelTest\TestModel::DB_TABLE_NAME . ' order by id desc'
);

