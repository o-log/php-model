<?php

namespace OLOG\Model\CLI;

use OLOG\DB\DBFactory;

/**
 * См. README
 * Class CLIExecuteSql
 * @package OLOG\Model\CLI
 */
class CLIExecuteSql
{
    const EXECUTED_QUERIES_TABLE_NAME = '_executed_queries';

    static public function executeSql()
    {
        $db_arr = \OLOG\ConfWrapper::value('db'); // TODO: check not empty

        foreach ($db_arr as $db_name => $db_config) {
            echo "\nВыполнение запросов для БД " . $db_name . "\n";

            self::process_db($db_name);
        }
    }

    function process_db($db_id)
    {
        // check DB connectivity

        $db_obj = null;
        try {
            $db_obj = \OLOG\DB\DBFactory::getDB($db_id);
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n\n";
        }

        if (!$db_obj) {
            echo "Не удалось подключиться к БД " . $db_id . "\n";
            echo "Возможные проблемы:\n";
            echo "- неправильная конфигурация БД в коде приложения. Вот текущие параметры:\n";
            echo var_export(DBFactory::getConfigArr($db_id)) . "\n";

            echo "- недоступен указанный в конфигурации сервер БД.\n";
            echo "- БД не создана. В этом случае надо создать ее руками.\n";
            exit;
        }

        $executed_queries_sql_arr = [];
        try {
            $executed_queries_sql_arr = \OLOG\DB\DBWrapper::readColumn(
                $db_id,
                'select sql_query from _executed_queries'
            );
        } catch (\Exception $e) {
            echo "Ошибка при загрузке списка уже выполненных запросов из таблицы " . self::EXECUTED_QUERIES_TABLE_NAME . ":\n";
            echo $e->getMessage() . "\n\n";

            echo "Похоже что таблица " . self::EXECUTED_QUERIES_TABLE_NAME . " не создана. Введите:\n";
            echo "1 чтобы создать таблицу _executed_queries и продолжить работу\n"; // TODO: constants
            echo "ENTER для выхода:\n";

            $command_str = trim(fgets(STDIN));

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
                echo "\nНовый запрос:\n";
                echo $sql . "\n";

                // TODO: constants
                echo "Введите:\n1: чтобы выполнить запрос\n2: чтобы пометить запрос как выполненный, но не выполнять (если он был например выполнен руками)\nENTER чтобы пропустить этот запрос\n";

                $command_str = trim(fgets(STDIN));

                switch ($command_str) {
                    case '1': // TODO: constants
                        \OLOG\DB\DBWrapper::query($db_id, $sql);

                        \OLOG\DB\DBWrapper::query(
                            $db_id,
                            'insert into _executed_queries (created_at_ts, sql_query) values (?, ?)',
                            array(time(), $sql)
                        );
                        echo "Запрос выполнен.\n";
                        break;

                    case '2': // TODO: constants
                        \OLOG\DB\DBWrapper::query(
                            $db_id,
                            'insert into _executed_queries (id, created_at_ts, sql_query) values (?, ?)',
                            array(time(), $sql)
                        );
                        echo "Запрос помечен как выполненный без выполнения.\n";
                        break;

                    default:
                        echo "Запрос пропущен.\n";
                        break;
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
        \OLOG\Assert::assert($sql_file_str, 'Файл SQL запросов не найден или пустой.');

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