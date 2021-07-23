<?php

namespace interfaces;

/**
 * Interface IConvertableToArrayInterface
 * @package core\backend\interfaces osztályból tömböt készítő classok interface
 */
interface IConvertableToArrayInterface
{
    /**
     * @return array returns the values of an object in array form
     */
    public function getAllValueAsArray(): array;
}
