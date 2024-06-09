<?php

namespace App\Domain\Enums;

use ReflectionClass;

class Enum {

    /**
     * Retrieves all the values of this enum
     * @throws \ReflectionException
     */
    public static function values(){
        $class = new ReflectionClass(new static());
        $constants = $class->getConstants();
        return $constants;
    }
}