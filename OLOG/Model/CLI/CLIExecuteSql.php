<?php

namespace OLOG\Model\CLI;

class CLIExecuteSql
{
    static public function executeSql()
    {
        $db_arr = \OLOG\ConfWrapper::value('db'); // TODO: check not empty

        foreach ($db_arr as $db_name => $db_config) {
            self::process_db($db_name);
        }
    }

    function process_db($db_name)
    {
        echo "Processing database " . $db_name . "\n";

        $executed_queries_ids_arr = [];
        try {
            $executed_queries_ids_arr = \OLOG\DB\DBWrapper::readColumn(
                $db_name, 'select id from _executed_queries'
            );
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo "Похоже что таблица _executed_queries не создана, без этой таблицы работа невозможна. Введите 1 чтобы создать ее, ENTER для выхода.\n";
            $command_str = trim(fgets(STDIN));

            if ($command_str == '1') {
                \OLOG\DB\DBWrapper::query($db_name, 'create table _executed_queries (id int not null, created_at_ts int not null, sql_query text, unique key (id)) engine InnoDB default charset utf8');
            } else {
                exit;
            }
        }

        $sql_arr = self::loadSqlArrForDB($db_name);

        foreach ($sql_arr as $id => $sql) {
            if (!in_array($id, $executed_queries_ids_arr)) {
                echo $sql . "\n";
                echo "type 1 to execute, ENTER to skip\n";

                $command_str = trim(fgets(STDIN));

                if ($command_str == '1') {

                    \OLOG\DB\DBWrapper::query($db_name, $sql);

                    \OLOG\DB\DBWrapper::query(
                        $db_name,
                        'insert into _executed_queries (id, created_at_ts, sql_query) values (?, ?, ?)',
                        array($id, time(), $sql)
                    );
                    echo "query executed\n";
                } else {
                    echo "query skipped\n";
                }

            }
        }
    }

    static public function getSqlFileNameForDB($db_name){
        // TODO: open file in current project root
        $filename = $db_name . '.sql';

        return $filename;
    }

    static public function loadSqlArrForDB($db_name){
        $filename = self::getSqlFileNameForDB($db_name);

        // TODO: must open file from current project root
        $sql_file_str = file_get_contents($filename); // TODO: better errors check?
        \OLOG\Assert::assert($sql_file_str, 'missing or empty sql file');

        $sql_arr = array();
        eval('$sql_arr = ' . $sql_file_str . ';');
        ksort($sql_arr);

        return $sql_arr;
    }

    static public function addSqlToRegistry($db_name, $sql_str){
        $sql_arr = self::loadSqlArrForDB($db_name);

        $max_sql_id = max(array_keys($sql_arr));
        $new_sql_id = $max_sql_id + 1;

        $sql_arr[$new_sql_id] = $sql_str;

        ksort($sql_arr);
        $exported_arr = var_export($sql_arr, true);

        $filename = self::getSqlFileNameForDB($db_name);

        // TODO: check errors
        file_put_contents($filename, $exported_arr);
    }
}