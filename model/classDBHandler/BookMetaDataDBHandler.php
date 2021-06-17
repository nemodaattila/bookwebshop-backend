<?php

namespace classDbHandler;

use core\backend\database\PDOProcessorBuilder;
use core\backend\model\RequestResultException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class BookMetaDataDBHandler könyvekkel kapcsolatos meta-adatok visszaadása adatbázisból
 * @package project\backend\model\dbHandler
 */
class BookMetaDataDBHandler
{
    /**
     * összes metaadat lekérése, további függvényeken keresztül
     * @return array a metaadatok asszociativ tömbje
     * @throws RequestResultException PDOProcesszor hiba
     */
    #[ArrayShape(['format' => "array", 'language' => "array", 'mainCategory' => "array", 'subCategory' => "array", 'tag' => "array", 'targetAudience' => "array", 'type' => "array"])]
    public function getAllMetaData(): array
    {
        return [
            'format' => $this->getFormat(),
            'language' => $this->getLanguage(),
            'mainCategory' => $this->getMainCategory(),
            'subCategory' => $this->getSubCategory(),
            'tag' => $this->getTag(),
            'targetAudience' => $this->getTargetAudience(),
            'type' => $this->getType(),
        ];
    }

    /**
     * formátumtipusok visszadása
     * @return array multidimenzionális tömb pl: ['könyv'=>[0=>'puhafedeles', ...], ['hangoskönyv'=>[10=>'audiocd']]]
     * @throws RequestResultException PDOProcesszot hiba
     */
    private function getFormat(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id, type_id, name FROM meta_format");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            if (!isset($result[$value['type_id']])) {
                $result[$value['type_id']] = [];
            }
            $result[$value['type_id']][$value['id']] = $value['name'];
        }
        return $result;
    }

    /**
     * létező nyelvek visszaadása
     * @return array a nyelvek tömbje
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getLanguage(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_language");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }

    /**
     * fő téma kategóriák visszadása (pl: szép és szórakoztató irodalom)
     * @return array kategóriák tömbje
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getMainCategory(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_main_category");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }

    /**
     * al-téma kategóriák visszadása (pl: [szép és szórakoztató irodalom=>[szépirodalom, képregény]])
     * @return array kategóriák multidimenziónális tömbje
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getSubCategory(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id, main_category_id, name FROM meta_subcategory");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            if (!isset($result[$value['main_category_id']])) {
                $result[$value['main_category_id']] = [];
            }
            $result[$value['main_category_id']][$value['id']] = $value['name'];
        }
        return $result;
    }

    /**
     * könyv tegek visszaadása [pl: scifi, háborús, romantika]
     * @return array tagek tömbje
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getTag(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_tag");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }

    /**
     * létező célcsoportok visszadása pl: all age, 18+
     * @return array célcsoportok tömbje
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getTargetAudience(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_target_audience");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }

    /**
     * könyvtipusok visszaadása
     * @return array ['könyv', 'e-book','hangoskönyv']
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getType(): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT id,  name FROM meta_type");
        $tempResult = $PDOLink->execute();
        $result = [];
        foreach ($tempResult as $value) {
            $result[$value['id']] = $value['name'];
        }
        return $result;
    }
}
