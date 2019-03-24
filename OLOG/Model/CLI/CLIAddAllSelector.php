<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model\CLI;

use OLOG\CLIUtil;
use OLOG\Model\CLI\Templates\AllSelectorTemplate;
use OLOG\Model\UniqifySQL;
use Stringy\Stringy;

class CLIAddAllSelector
{
    public $model_file_path = ''; // полный путь к файлу модели

    public function __construct($model_file_path)
    {
        $this->model_file_path = $model_file_path;
    }

    static public function replacePageSizePlaceholders($str, $page_size){
        $str = str_replace('#SELECTOR_PAGE_SIZE#', $page_size, $str);

        return $str;
    }

    static public function replaceClassNamePlaceholders($str, $class_name){
        $str = str_replace('#CLASS_NAME#', $class_name, $str);

        return $str;
    }

    public function addSelector()
    {
        if (!$this->model_file_path) throw new \Exception();

        echo "Enter page size for selector, press ENTER to leave default (30):\n";
        $page_size = trim(fgets(STDIN));

        if ($page_size == ''){
            $page_size = '30';
        }

        // TODO: check page size validity

        $class_file_obj = new PHPClassFile($this->model_file_path);

        $selector_template = AllSelectorTemplate::selectorTemplate();
        $selector_template = self::replacePageSizePlaceholders($selector_template, $page_size);
        $selector_template = self::replaceClassNamePlaceholders($selector_template, $class_file_obj->class_name);
        $class_file_obj->insertBelowIdField($selector_template);

        $class_file_obj->save();

        echo "\nClass file updated\n";

        $model_db_id = $class_file_obj->model_db_id;
        $model_table_name = $class_file_obj->model_table_name;

        if (!$model_table_name) throw new \Exception();
        if (!$model_db_id) throw new \Exception();

        $sql = 'alter table ' . $model_table_name . ' add index INDEX_all_' . rand(0, 99999999) . ' (created_at_ts);';

        \OLOG\DB\Migrate::addMigration(
            $model_db_id,
            UniqifySQL::addDatetimeComment($sql)
        );

        echo "\nSQL registry updated\n";
    }
}
