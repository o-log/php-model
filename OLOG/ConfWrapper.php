<?php
namespace Cebera;

class ConfWrapper 
{
 
    /**
     * Just proxy function to \Cebera\Conf::get()
     * @see \Cebera\Conf::get()
     */
 	static public function get(){
 		return \Cebera\Conf::get();
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

?>