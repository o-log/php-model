<?php

namespace OLOG\Model;

use OLOG\DB\DBConfig;
use OLOG\Model\CLI\CLIExecuteSql;
use OLOG\Operations;
use OLOG\POSTAccess;
use OLOG\Sanitize;

/**
 * Веб выполнялка sql-запросов.
 * Позволяет выполнять миграцию структуры БД на сервере без ssh-доступа.
 * Как использовать:
 * - нужно создать на сайте новый экшен для выполнения sql-запросов
 * - в этом экшене вызвать метод WebExecuteSQL::render(), который выводит инструкции по выполнению запросов, выполняет собственно запросы и выводит результат их выполнения
 * - весь роутинг, авторизация и проверка полномочий пользователя остаются на совести автора экшена!! нужно не забыть защитить этот экшен от несанкционированного доступа
 * Class WebExecuteSQL
 * @package OLOG\Model
 */
class WebExecuteSQL
{
    const OPERATION_EXECUTE_QUERY = 'OPERATION_EXECUTE_QUERY';

    const INPUT_NAME_SQL = 'INPUT_NAME_SQL';
    const INPUT_NAME_DB_ID = 'INPUT_NAME_DB_ID';

    static public function render($project_root_path_in_filesystem){
        Operations::matchOperation(self::OPERATION_EXECUTE_QUERY, function() {
            $sql = POSTAccess::getRequiredPostValue(self::INPUT_NAME_SQL);
            $db_id = POSTAccess::getRequiredPostValue(self::INPUT_NAME_DB_ID);

            self::renderLine("Executing: " . $sql);

            \OLOG\DB\DBWrapper::query($db_id, $sql);

            \OLOG\DB\DBWrapper::query(
                $db_id,
                'insert into ' . CLIExecuteSql::EXECUTED_QUERIES_TABLE_NAME . ' (created_at_ts, sql_query) values (?, ?)',
                array(time(), $sql)
            );
            self::renderLine("Query executed.");
        });

        $db_arr = DBConfig::getDBSettingsObjArr();

        if (empty($db_arr)){
            self::renderLine('No database entries in config');
            return;
        }

        foreach ($db_arr as $db_id => $db_config) {
            self::renderLine("Database ID in application config: " . $db_id);

            self::process_db($db_id, $project_root_path_in_filesystem);
        }
    }

    protected static function process_db($db_id, $project_root_path_in_filesystem)
    {
        // checking DB connectivity
        $db_obj = null;
        try {
            $db_obj = \OLOG\DB\DBFactory::getDB($db_id);
        } catch (\Exception $e) {
            self::renderLine($e->getMessage());
            return;
        }

        if (!$db_obj) {
            self::renderLine("Can't connect to database " . $db_id);
            self::renderLine("Probable problems:");
            self::renderLine("- misconfiguration. App config for database:");
            //echo var_export(DBFactory::getConfigArr($db_id)) . "\n"; // TODO: fix
            self::renderLine("- database server not accessible");
            self::renderLine("- database not created. It must be created manually.");
            return;
        }

        $executed_queries_sql_arr = [];
        try {
            $executed_queries_sql_arr = \OLOG\DB\DBWrapper::readColumn(
                $db_id,
                'select sql_query from ' . CLIExecuteSql::EXECUTED_QUERIES_TABLE_NAME
            );
        } catch (\Exception $e) {
            self::renderLine("Can not load the executed queries list from " . CLIExecuteSql::EXECUTED_QUERIES_TABLE_NAME . " table:");
            self::renderLine($e->getMessage());

            self::renderLine("Probably the " . CLIExecuteSql::EXECUTED_QUERIES_TABLE_NAME . " table was not created. Choose:");
            self::renderLine("- ENTER to create table and proceed"); // TODO: constants
            self::renderLine("- any other key to exit");

            // TODO: restore
            /*
            $command_str = CliUtil::readStdinAnswer();

            // TODO: switch
            if ($command_str == '') { // TODO: constants
                \OLOG\DB\DBWrapper::query(
                    $db_id,
                    'create table ' . self::EXECUTED_QUERIES_TABLE_NAME . ' (id int not null auto_increment primary key, created_at_ts int not null, sql_query text) engine InnoDB default charset utf8'
                );
            } else {
                exit;
            }
            */
            return;
        }

        $sql_arr = CLIExecuteSql::loadSqlArrForDB($db_id, $project_root_path_in_filesystem);

        foreach ($sql_arr as $sql) {
            if (!in_array($sql, $executed_queries_sql_arr)) {
                echo '<form method="post">';
                echo Operations::operationCodeHiddenField(self::OPERATION_EXECUTE_QUERY);
                echo '<input type="hidden" name="' . self::INPUT_NAME_DB_ID . '" value="' . Sanitize::sanitizeAttrValue($db_id) . '">';
                echo '<textarea cols=60 rows=5 name="' . self::INPUT_NAME_SQL . '">' . $sql . '</textarea>';
                echo '<div><input type="submit" value="Выполнить запрос"></div>';
                echo '</form>';

                /*
                $command_str = CliUtil::readStdinAnswer();

                switch ($command_str) {
                    case '':
                        \OLOG\DB\DBWrapper::query($db_id, $sql);

                        \OLOG\DB\DBWrapper::query(
                            $db_id,
                            'insert into ' . self::EXECUTED_QUERIES_TABLE_NAME . ' (created_at_ts, sql_query) values (?, ?)',
                            array(time(), $sql)
                        );
                        echo "Query executed.\n";
                        break;

                    case self::COMMAND_IGNORE_QUERY:
                        \OLOG\DB\DBWrapper::query(
                            $db_id,
                            'insert into ' . self::EXECUTED_QUERIES_TABLE_NAME . ' (created_at_ts, sql_query) values (?, ?)',
                            array(time(), $sql)
                        );
                        echo "Query marked as executed without execution.\n";
                        break;

                    case self::COMMAND_SKIP_QUERY:
                        echo "Query skipped.\n";
                        break;

                    default:
                        //echo "Unknown command.\n";
                        throw new \Exception('unknown command');
                        break; // TODO: repeat entry?
                }
                */

                return;
            }
        }
    }

    static protected function renderLine($line){
        echo '<div>' . $line . '</div>';
    }
}