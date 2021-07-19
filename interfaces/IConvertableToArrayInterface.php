<?php

namespace interfaces;

/**
 * Interface IConvertableToArrayInterface
 * @package core\backend\interfaces osztályból tömböt készítő classok interface
 */
interface IConvertableToArrayInterface
{
    /**
     * @return array visszaadja egy osztály adatait tömb formában
     */
    public function getAllValueAsArray(): array;
}
