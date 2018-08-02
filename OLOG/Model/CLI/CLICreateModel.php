<?php

namespace OLOG\Model\CLI;

use OLOG\CLIUtil;
use OLOG\DB\DBConfig;

class CLICreateModel
{
    static $model_class_name = '';
    static $model_namespace_for_path = '';
    static $model_namespace_for_class = '';
    static $model_db_id = '';

//    static public function enterClassNameScreen()
//    {
//        echo CLIUtil::delimiter();
//        echo "Еnter new model class name. Example:\n\tTestModel\n";
//
//        // TODO: sanitize
//        self::$model_class_name = CLIUtil::readStdinAnswer();
//
//        // TODO: check class name format
//
//        self::chooseNamespaceScreen();
//    }

//    static public function chooseNamespaceScreen()
//    {
//        echo CLIUtil::delimiter();
//        echo "Select new model namespace:\n";
//
//        // TODO: sanitize
//        // TODO: support empty namespaces
//        // TODO: check for leading '\' and correct format
//        $cwd = getcwd();
//        self::$model_namespace_for_path = CLIFileSelector::selectFileName($cwd, false);
//
//        // убираем из начала текущую папку
//        if (strpos(self::$model_namespace_for_path, $cwd) === 0) {
//            self::$model_namespace_for_path = substr(self::$model_namespace_for_path, strlen($cwd));
//        } else {
//            throw new \Exception('fail');
//        }
//
//        // отрезаем слэш в начале если есть
//        if (substr(self::$model_namespace_for_path, 0, 1) == DIRECTORY_SEPARATOR) {
//            self::$model_namespace_for_path = substr(self::$model_namespace_for_path, strlen(DIRECTORY_SEPARATOR));
//        }
//
//        self::$model_namespace_for_class = str_replace(DIRECTORY_SEPARATOR, '\\', self::$model_namespace_for_path);
//
//        self::chooseModelDBIndex();
//    }

    static public function chooseModelDBIndex()
    {
        echo CLIUtil::delimiter();
        echo "Choose model DB index:\n";
        //$db_arr = \OLOG\ConfWrapper::value(\OLOG\Model\ModelConstants::MODULE_CONFIG_ROOT_KEY . '.db'); // TODO: check not empty
        $spaces = DBConfig::spaces();

        if (count($spaces) == 0) throw new \Exception('No spaces in config');

        // TODO: select db by index
        $db_id_by_index = [];
        $index = 1;
        foreach ($spaces as $db_id => $space) {
            echo "\t" . str_pad($index, 8, '.') . $db_id . "\n";
            $db_id_by_index[$index] = $db_id;
            $index++;
        }

        $model_db_index = CLIUtil::readStdinAnswer();

        if (!array_key_exists($model_db_index, $db_id_by_index)) {
            throw new \Exception('Wrong index');
        }

        self::$model_db_id = $db_id_by_index[$model_db_index];

        // TODO: check db presence in config?

        self::generateClass();
    }

    static public function generateClass(){
        $cwd = getcwd();

        // TODO: model_namespace_for_path may have leading '/' - remove it?
        $model_filename = $cwd . DIRECTORY_SEPARATOR . self::$model_namespace_for_path . DIRECTORY_SEPARATOR . self::$model_class_name . '.php';

        $model_tablename = mb_strtolower(self::$model_namespace_for_class . "\\" . self::$model_class_name);
        $model_tablename = preg_replace('@\W@', '_', $model_tablename);

        //
        // creating model class file
        //

        $class_file = self::getClassTemplate();

        // TODO: use common variable replacemnt method
        $class_file = str_replace('TEMPLATECLASS_CLASSNAME', self::$model_class_name, $class_file);
        $class_file = str_replace('TEMPLATECLASS_NAMESPACE', self::$model_namespace_for_class, $class_file);
        $class_file = str_replace('TEMPLATECLASS_TABLENAME', $model_tablename, $class_file);
        $class_file = str_replace('TEMPLATECLASS_DBID', self::$model_db_id, $class_file);

        self::file_force_contents($model_filename, $class_file);

        echo "\nModel file created: " . $model_filename . "\n";

        //
        // altering database sql file
        //

        $class_sql = self::getClassSQL();

        // TODO: use common variable replacemnt method
        $class_sql = str_replace('TEMPLATECLASS_TABLENAME', $model_tablename, $class_sql);

        \OLOG\DB\Migrate::addMigration(self::$model_db_id, $class_sql);

        echo "\nSQL registry updated\n";

        echo "\nType ENTER to execute SQL queries, Ctrl+C to exit.\n";
        
        $command_str = CLIUtil::readStdinAnswer();
        if ($command_str == ''){
            \OLOG\DB\MigrateCLI::run();
        }
        
        return;
    }

    static public function file_force_contents($filename, $data, $flags = 0)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename) . '/', 0777, TRUE);
        }

        return file_put_contents($filename, $data, $flags);
    }

    static public function getClassSQL(){
        return 'create table TEMPLATECLASS_TABLENAME (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8 /* rand' . rand(0, 9999) . ' */;';
    }

    static public function getClassTemplate()
    {
        
        // здесь поле id стоит ниже остальных, потому что добавлялка полей будет вставлять новые поля под него. т.е. поле id как бы разделяет поля и методы
        return <<<'EOT'
<?php
declare(strict_types=1);

namespace TEMPLATECLASS_NAMESPACE;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class TEMPLATECLASS_CLASSNAME implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'TEMPLATECLASS_DBID';
    const DB_TABLE_NAME = 'TEMPLATECLASS_TABLENAME';

    const _CREATED_AT_TS = 'created_at_ts';
    public $created_at_ts;
    const _ID = 'id';
    public $id;
    
    public function __construct(){
        $this->created_at_ts = time();
    }
    
    public function getId()
    {
        return $this->id;
    }

    static public function all($limit = 30, $offset = 0){
        return self::idsToObjs(self::ids($limit, $offset));
    }

    static public function ids($limit = 30, $offset = 0){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$limit, $offset]
        );
        return $ids_arr;
    }
}
EOT;
    }

}