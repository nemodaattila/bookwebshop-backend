<?php

namespace classDbHandler\metaData;

use database\PDOProcessorBuilder;

class MetaFormatDBHandler
{

    /**
     * formátumtipusok visszadása
     * @return array multidimenzionális tömb pl: ['könyv'=>[0=>'puhafedeles', ...], ['hangoskönyv'=>[10=>'audiocd']]]
     * @throws RequestResultException PDOProcesszot hiba
     */
    public function getGroupedByType(): array
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

}
