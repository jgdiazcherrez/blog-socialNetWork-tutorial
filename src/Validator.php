<?php
/**
 * Created by PhpStorm.
 * User: Jonathan
 * Date: 29/01/2018
 * Time: 23:52
 */

namespace Social;

/**
 * Class Validator
 * @package Social
 */
class Validator {


    public static function isUrl($url){
        $assert = false;
        if(filter_var($url, FILTER_VALIDATE_URL))
            $assert = true;
        return $assert;
    }
}