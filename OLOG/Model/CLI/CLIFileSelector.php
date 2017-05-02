<?php

namespace OLOG\Model\CLI;

use OLOG\CLIUtil;

class CLIFileSelector
{
    // TODO: docs
    static public function selectFileName($folder, $only_files = true){
        while (true) {
            $dirty_arr = scandir($folder);

            // убираем все элементы, которые начинаются с .
            $arr = [];
            $index = 1;
            foreach ($dirty_arr as $dir_item){
                if (!preg_match('@^\.@', $dir_item)){
                    $arr[$index] = $dir_item;
                    $index++;
                }
            }

            echo "\n" . $folder . ":\n";

            foreach ($arr as $index => $item) {
                echo "\t" . str_pad($index, 8, '.') . $item . "\n";
            }

            echo "\nEnter file or directory index:\n";
            $index = CLIUtil::readStdinAnswer();

            if (!array_key_exists($index, $arr)) {
                echo "Index not found\n";
                continue;
            }

            $selected_path = $folder . DIRECTORY_SEPARATOR . $arr[$index];

            if (is_dir($selected_path)){
                if ($only_files) {
                    $folder = $selected_path;
                    continue;
                }

                echo CLIUtil::delimiter();
                echo "Selected directory: " . $selected_path . "\n";
                echo "Use it or enter directory?\n\t1 use directory\n\tENTER enter directory\n"; // TODO: constants
                $answer = CLIUtil::readStdinAnswer();

                switch ($answer){
                    case 1: // TODO: use constant
                        return $selected_path;
                    case '':
                        $folder = $selected_path;
                        break;
                    default:
                        throw new \Exception('Unknown answer');
                }

                continue;
            }

            return $selected_path;
        }
    }
}
