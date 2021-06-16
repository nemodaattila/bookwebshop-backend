<?php

namespace helper;

use stdClass;

/**
 * Class VariableHelper helper functions associated with variables
 * @package core\backend\helper
 */
class VariableHelper
{
    /**
     * converts an stdClass to an associative array
     * @param stdClass $class the class to be transformed
     * @return array the resulted associative array
     */
    public static function convertStdClassToArray(stdClass $class): array
    {
        return json_decode(json_encode($class), true);
    }
}
