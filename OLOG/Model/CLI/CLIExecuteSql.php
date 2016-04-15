<?php

namespace OLOG\Model\CLI;

use OLOG\CliUtil;
use OLOG\DB\DBFactory;

/**
 * См. README
 * Class CLIExecuteSql
 * @package OLOG\Model\CLI
 */
class CLIExecuteSql
{
    const EXECUTED_QUERIES_TABLE_NAME = '_executed_queries';

    const COMMAND_SKIP_QUERY = 's';
    const COMMAND_IGNORE_QUERY = 'i';

    static public function executeSqlScreen()
    {
        $db_arr = \OLOG\ConfWrapper::value('db'); // TODO: check not empty

        foreach ($db_arr as $db_id => $db_config) {
            echo "\nDatabase ID in application config: " . $db_id . "\n";

            self::process_db($db_id);
        }
    }

    function process_db($db_id)
    {
        // checking DB connectivity
        $db_obj = null;
        try {
            $db_obj = \OLOG\DB\DBFactory::getDB($db_id);
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n\n";
        }

        if (!$db_obj) {
            echo CliUtil::delimiter();
            echo "Can't connect to database " . $db_id . "\n";
            echo "Probable problems:\n";
            echo "- misconfiguration. App config for database:\n";
            echo var_export(DBFactory::getConfigArr($db_id)) . "\n";

            echo "- database server not accessible\n";
            echo "- database not created. It must be created manually.\n";
            exit;
        }

        $executed_queries_sql_arr = [];
        try {
            $executed_queries_sql_arr = \OLOG\DB\DBWrapper::readColumn(
                $db_id,
                'select sql_query from _executed_queries'
            );
        } catch (\Exception $e) {
            echo CliUtil::delimiter();
            echo "Can not load the executed queries list from " . self::EXECUTED_QUERIES_TABLE_NAME . " table:\n";
            echo $e->getMessage() . "\n\n";

            echo "Probably the " . self::EXECUTED_QUERIES_TABLE_NAME . " table was not created. Choose:\n";
            echo "\t1 to create table and proceed\n"; // TODO: constants
            echo "\tENTER to exit\n";

            //$command_str = trim(fgets(STDIN));
            $command_str = CliUtil::readStdinAnswer();

            // TODO: switch
            if ($command_str == '1') { // TODO: constants
                \OLOG\DB\DBWrapper::query(
                    $db_id,
                    'create table ' . self::EXECUTED_QUERIES_TABLE_NAME . ' (id int not null auto_increment primary key, created_at_ts int not null, sql_query text) engine InnoDB default charset utf8'
                );
            } else {
                exit;
            }
        }

        $sql_arr = self::loadSqlArrForDB($db_id);

        foreach ($sql_arr as $sql) {
            if (!in_array($sql, $executed_queries_sql_arr)) {
                echo CliUtil::delimiter();
                echo "\n" . $sql . "\n";

                // TODO: constants
                echo "\n";
                echo "\t" . self::COMMAND_SKIP_QUERY . ": skip query now, do not mark as executed\n";
                echo "\t" . self::COMMAND_IGNORE_QUERY . ": ignore query - mark as executed, but do not execute (you can execute one manually)\n";
                echo "\tENTER execute query\n";

                $command_str = CliUtil::readStdinAnswer();

                switch ($command_str) {
                    case '':
                        \OLOG\DB\DBWrapper::query($db_id, $sql);

                        \OLOG\DB\DBWrapper::query(
                            $db_id,
                            'insert into _executed_queries (created_at_ts, sql_query) values (?, ?)',
                            array(time(), $sql)
                        );
                        echo "Query executed.\n";
                        break;

                    case self::COMMAND_IGNORE_QUERY:
                        \OLOG\DB\DBWrapper::query(
                            $db_id,
                            'insert into _executed_queries (created_at_ts, sql_query) values (?, ?)',
                            array(time(), $sql)
                        );
                        echo "Query marked as executed without execution.\n";
                        break;

                    case self::COMMAND_SKIP_QUERY:
                        echo "Query skipped.\n";
                        break;

                    default:
                        echo "Unknown command.\n";
                        break; // TODO: repeat entry?
                }
            }
        }
    }

    static public function getSqlFileNameForDB($db_name)
    {
        // TODO: open file in current project root
        $cwd = getcwd();

        $filename = $cwd . DIRECTORY_SEPARATOR . $db_name . '.sql';

        return $filename;
    }

    static public function loadSqlArrForDB($db_name)
    {
        $filename = self::getSqlFileNameForDB($db_name);

        if (!file_exists($filename)) {
            echo "Не найден файл SQL запросов для БД " . $db_name . ": " . $filename . "\n";
            echo "Введите 1 чтобы создать файл SQL запросов, ENTER для выхода:\n";

            $command_str = trim(fgets(STDIN));

            if ($command_str == '1') {
                // TODO: check errors
                file_put_contents($filename, var_export([], true));
            } else {
                exit;
            }
        }

        // TODO: must open file from current project root
        $sql_file_str = file_get_contents($filename); // TODO: better errors check?
        \OLOG\Assert::assert($sql_file_str, 'SQL queries file doesnt exist or empty.');

        $sql_arr = array();
        eval('$sql_arr = ' . $sql_file_str . ';');
        ksort($sql_arr);

        return $sql_arr;
    }

    static public function addSqlToRegistry($db_name, $sql_str)
    {
        $sql_arr = self::loadSqlArrForDB($db_name);

        
        $sql_arr[] = $sql_str;

        //$exported_arr = var_export($sql_arr, true);
        // не используется var_export, потому что он сохраняет массив с индексами, а индексы могут конфликтовать при мерже если несколько разработчиков одновременно добавляют запросы

        $exported_arr = "array(\n";
        foreach ($sql_arr as $sql_str){
            $sql_str = str_replace('\'', '\\\'', $sql_str);
            $exported_arr .= '\'' . $sql_str . '\',' . "\n";
        }
        $exported_arr .= ")\n";


        $filename = self::getSqlFileNameForDB($db_name);

        // TODO: check errors
        file_put_contents($filename, $exported_arr);
    }
}