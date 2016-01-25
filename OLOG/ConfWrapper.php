<?php
namespace OLOG;

class ConfWrapper 
{
    static $config_arr = null;

    static public function assignConfig($config_arr){
        self::$config_arr = $config_arr;
    }

    /**
     * Just proxy function to \Cebera\Conf::get()
     * @see \Cebera\Conf::get()
     */
 	static public function get(){
        \OLOG\Helpers::assert(self::$config_arr);
 		return self::$config_arr;
 	}
 
    /**
     * Get value an array by using "root.branch.leaf" notation
     *
     * @param string $path   Path to a specific option to extract
     * @param mixed $default Value to use if the path was not found
     * @return mixed
     */
    static public function value($path, $default = ''){
    	
    	if (empty($path)) {
    		return '';
    	}
        
    	$value = self::get();
 
        $parts = explode(".", $path);
 
        foreach ($parts as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                // key doesn't exist, fail
                return $default;
            }
        }
 
        return $value;
    }
}
