<?php

namespace OLOG\Model\CLI;

class CLICreateModel
{
    static public function run()
    {
        echo "Еnter news model class name:\n";
        echo "Example: \"TestModel\"\n";

        // TODO: sanitize
        $model_class_name = trim(fgets(STDIN));

        echo "Enter new model namespace:\n";
        echo "Example: \"Test\", \"Deep\\Test\"\n";

        // TODO: sanitize
        // TODO: support empty namespaces
        // TODO: check for leading '\' and correct format
        $model_namespace = trim(fgets(STDIN));

        $model_namespace_for_path = str_replace('\\', '/', $model_namespace);

        echo "Enter model DB ID:\n";
        //echo "Example: \"testdb\"\n";
        $db_arr = \OLOG\ConfWrapper::value('db'); // TODO: check not empty

        // TODO: select db by index
        foreach ($db_arr as $db_name => $db_conf){
            echo "- " . $db_name . "\n";
        }

        // TODO: check db presence in config?
        $model_db_id = trim(fgets(STDIN));

        //
        //

        $cwd = getcwd();

        // TODO: model_namespace_for_path may have leading '/' - remove it?
        $model_filename = $cwd . DIRECTORY_SEPARATOR . $model_namespace_for_path . DIRECTORY_SEPARATOR . $model_class_name . '.php';

        $model_tablename = mb_strtolower($model_class_name);

        //
        // creating model class file
        //

        $class_file = self::getClassTemplate();

        // TODO: use common variable replacemnt method
        $class_file = str_replace('TEMPLATECLASS_CLASSNAME', $model_class_name, $class_file);
        $class_file = str_replace('TEMPLATECLASS_NAMESPACE', $model_namespace, $class_file);
        $class_file = str_replace('TEMPLATECLASS_TABLENAME', $model_tablename, $class_file);
        $class_file = str_replace('TEMPLATECLASS_DBID', $model_db_id, $class_file);

        self::file_force_contents($model_filename, $class_file);

        //
        // altering database sql file
        //

        $class_sql = self::getClassSQL();

        // TODO: use common variable replacemnt method
        $class_sql = str_replace('TEMPLATECLASS_TABLENAME', $model_tablename, $class_sql);

        CLIExecuteSql::addSqlToRegistry($model_db_id, $class_sql);

        echo "DONE\n";
    }

    static public function file_force_contents($filename, $data, $flags = 0)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename) . '/', 0777, TRUE);
        }

        return file_put_contents($filename, $data, $flags);
    }

    static public function getClassSQL(){
        return 'create table TEMPLATECLASS_TABLENAME (id int not null auto_increment primary key, created_at_ts int not null default 0) engine InnoDB default charset utf8;';
    }

    static public function getClassTemplate()
    {
        return <<<'EOT'
<?php

namespace TEMPLATECLASS_NAMESPACE;

class TEMPLATECLASS_CLASSNAME implements
    \OLOG\Model\InterfaceFactory,
    \OLOG\Model\InterfaceLoad,
    \OLOG\Model\InterfaceSave
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = 'TEMPLATECLASS_DBID';
    const DB_TABLE_NAME = 'TEMPLATECLASS_TABLENAME';

    protected $id;
    protected $created_at_ts = '';

    public function __construct(){
        $this->created_at_ts = time();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCreatedAtTs()
    {
        return $this->created_at_ts;
    }

    /**
     * @param string $title
     */
    public function setCreatedAtTs($title)
    {
        $this->created_at_ts = $title;
    }
}
EOT;
    }

}