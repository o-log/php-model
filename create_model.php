<?php

echo "Еnter model class name:\n";
echo "Example: \"TestModel\"\n";

// TODO: sanitize
$model_class_name = trim(fgets(STDIN));

echo "Enter model namespace:\n";
echo "Example: \"Test\", \"Deep\\Test\"\n";

// TODO: sanitize
// TODO: support empty namespaces
$model_namespace = trim(fgets(STDIN));

$model_namespace_for_path = str_replace('\\', '/', $model_namespace);

// TODO: select frrom config
echo "Enter model DB ID:\n";
echo "Example: \"testdb\"\n";

// TODO: sanitize
// TODO: support empty or multilevel namespaces
$model_db_id = trim(fgets(STDIN));

//
//

$cwd = getcwd();

$model_filename = $cwd . DIRECTORY_SEPARATOR . $model_namespace_for_path . DIRECTORY_SEPARATOR . $model_class_name . '.php';

$model_tablename = mb_strtolower($model_class_name);

//$class_file = file_get_contents('model_class_template.php');
$class_file = class_template();

$class_file = str_replace('TEMPLATECLASS_CLASSNAME', $model_class_name, $class_file);
$class_file = str_replace('TEMPLATECLASS_NAMESPACE', $model_namespace, $class_file);
$class_file = str_replace('TEMPLATECLASS_TABLENAME', $model_tablename, $class_file);
$class_file = str_replace('TEMPLATECLASS_DBID', $model_db_id, $class_file);

// TODO: create folder
file_force_contents($model_filename, $class_file);

echo "DONE\n";

function file_force_contents($filename, $data, $flags = 0){
    if(!is_dir(dirname($filename)))
        mkdir(dirname($filename).'/', 0777, TRUE);
    return file_put_contents($filename, $data,$flags);
}

function class_template(){
    return <<<'EOT'
<?php

/*
 * create table TEMPLATECLASS_TABLENAME (id int not null auto_increment primary key, title varchar(250) not null default '') engine InnoDB default charset utf8;
 */

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
    protected $title = '';

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
EOT;
}