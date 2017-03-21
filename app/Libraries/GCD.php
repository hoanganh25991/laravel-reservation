<?php
namespace App\Libraries;

class GCD{
    public static function find($arg){
        if (func_num_args() > 1) {
            $nums = func_get_args();
        } else {
            $nums = (array) $arg;
        }

        
        return collect($nums)->reduce(function($carry, $item){
            return static::gcd($carry, $item);
        }, 0);
    }

    public static function validate($nums) {
        // Check we have at least two values
        if (count($nums) < 2) {
            throw new \Exception('Not enough values submitted to find the greatest common factor.');
        }
        // Loop through each value to check it is valid
        foreach ($nums as $value) {
            if (is_int($value) && $value > 0) {
                throw new \Exception('The value of ' . $value . ' is invalid for finding the greatest common factor.');
            }
        }
    }

    public static function gcd($a,$b) {
        return ($a % $b) ? static::gcd($b,$a % $b) : $b;
    }
}