<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;
use OLOG\CliUtil;
use OLOG\DB\DBConfig;

class CLICreateComponent
{
    static $component_name = '';
    static $component_class_name = '';
    static $component_namespace_for_path = '';
    static $component_namespace_for_class = '';
    static $model_db_id = '';

    static public function enterComponentNameScreen()
    {
        echo CliUtil::delimiter();
        echo "Еnter new component name. Example:\n\tLayout\n";

        // TODO: sanitize
        self::$component_name = CliUtil::readStdinAnswer();

        $cwd = getcwd();
        // убираем из начала текущую папку
        if (strpos(self::$component_namespace_for_path, $cwd) === 0) {
            self::$component_namespace_for_path = substr(self::$component_namespace_for_path, strlen($cwd));
        } else {
            throw new \Exception('fail');
        }

        // отрезаем слэш в начале если есть
        if (substr(self::$component_namespace_for_path, 0, 1) == DIRECTORY_SEPARATOR) {
            self::$component_namespace_for_path = substr(self::$component_namespace_for_path, strlen(DIRECTORY_SEPARATOR));
        }

        self::$component_class_name = self::$component_name . 'Component';
        self::$component_namespace_for_path .= DIRECTORY_SEPARATOR . self::$component_name;
        self::$component_namespace_for_class = str_replace(DIRECTORY_SEPARATOR, '\\', self::$component_namespace_for_path);

        self::generateClass();
    }

    static public function chooseNamespaceScreen()
    {
        echo CliUtil::delimiter();
        echo "Select new component namespace:\n";
        //echo "Example: \"Test\", \"Deep\\Test\"\n";

        // TODO: sanitize
        // TODO: support empty namespaces
        // TODO: check for leading '\' and correct format
        $cwd = getcwd();
        self::$component_namespace_for_path = CLIFileSelector::selectFileName($cwd, false);

        self::enterComponentNameScreen();
    }

    static public function generateClass()
    {
        //

        $cwd = getcwd();

        // TODO: model_namespace_for_path may have leading '/' - remove it?
        $component_filename = $cwd . DIRECTORY_SEPARATOR . self::$component_namespace_for_path . DIRECTORY_SEPARATOR . self::$component_class_name . '.php';
        $script_filename = $cwd . DIRECTORY_SEPARATOR . self::$component_namespace_for_path . DIRECTORY_SEPARATOR . 'scripts.js';
        $style_filename = $cwd . DIRECTORY_SEPARATOR . self::$component_namespace_for_path . DIRECTORY_SEPARATOR . 'styles.less';

        //
        // creating component class, script, style file
        //
        $class_file = self::getClassTemplate();
        $class_file = str_replace('TEMPLATECLASS_CLASSNAME', self::$component_class_name, $class_file);
        $class_file = str_replace('TEMPLATECLASS_NAMESPACE', self::$component_namespace_for_class, $class_file);
        self::file_force_contents($component_filename, $class_file);
        self::file_force_contents($script_filename, '');
        self::file_force_contents($style_filename, self::getStyleTemplate());

        echo "\nComponent created: " . $component_filename . "\n";

        return;
    }

    static public function file_force_contents($filename, $data, $flags = 0)
    {
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename) . '/', 0777, TRUE);
        }

        return file_put_contents($filename, $data, $flags);
    }

    static public function getClassTemplate()
    {
        return <<<'EOT'
<?php
namespace TEMPLATECLASS_NAMESPACE;

use OLOG\Component\ComponentTrait;
use OLOG\Component\GenerateCSS;
use OLOG\Component\InterfaceComponent;

class TEMPLATECLASS_CLASSNAME implements InterfaceComponent
{
    use ComponentTrait;

    static public function render()
    {
        $_component_class = GenerateCSS::getCssClassName(__CLASS__);

    }
}
EOT;
    }

    static public function getStyleTemplate()
    {
        return <<<'EOT'
._COMPONENT_CLASS {

}
EOT;
    }
}