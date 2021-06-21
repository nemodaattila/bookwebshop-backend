<?php

namespace classDbHandler;


use database\PDOProcessorBuilder;
use helper\ImgHelper;

class BookDataDBHAndler
{

    /**
     * összes elsődleges adatok lekérése visszaadása
     * @param string $isbn a könyv isbn-je
     * @return array adatok tömb formában
     * @throws RequestResultException PDOProcessor hiba, img thumbnail hiba
     */
    public function getPrimaryData(string $isbn): array
    {
        $prim = $this->getBookPrimaryData($isbn);
        $prim['author'] = $this->getBookAuthor($isbn);
        $prim['price'] = $this->getBookPrice($isbn);
        $prim['discount'] = $this->getDiscountQuantity($isbn);
        $prim['cover_thumbnail'] = $this->getCoverThumbnail($isbn);
        return $prim;
    }

    /**
     * visszaadja egy könyv cimét, isbn-jét, tipus ID-ját, és kategóri ID-ját isbn alapján
     * @param string $isbn könyv isbn-je
     * @return array [isbn, title, type_id, category_id]
     * @throws RequestResultException ha a megadott isbn-nel nem létezik könyv
     * @throws RequestResultException PDOProcessor hiba
     */
    private function getBookPrimaryData(string $isbn): array
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT isbn, title, type_id, category_id FROM book WHERE isbn=?");
        $PDOLink->setValues($isbn);
        $PDOLink->setFetchType('fetch');
        $tempResult = $PDOLink->execute();
        if ($tempResult === null) {
            throw new RequestResultException(500, ['errorCode' => 'GBPDISBNNE']);
        }
        return $tempResult;
    }

    /**
     * visszaadja egy könyv iróját/iróit isbn alapján
     * @param string $isbn könyv isbn-je
     * @return array könyv irója/irói tömbben
     * @throws RequestResultException PDOProcessor hiba
     */
    private function getBookAuthor(string $isbn): array
    {
        [$PDOLink, $dataSource] = PDOProcessorBuilder::getProcessorAndDataSource('select');
        $dataSource->addTable('author', 'a');
        $dataSource->addTable('book_author', 'ba');
        $dataSource->addAttributes('author', ['name']);
        $dataSource->addWhereCondition('=', ['book_author.author_id', 'author.ID'], 'AND');
        $dataSource->addWhereCondition('=', ['book_author.isbn', '?'], 'AND');
        $dataSource->bindValue($isbn);
        $author = $PDOLink->query($dataSource);
        foreach ($author as $key => $value) {
            $author[$key] = $value['name'];
        }
        return $author;
    }

    /**
     * visszaadja egy könyv árát isbn alapján
     * @param string $isbn könyv isbn cime
     * @return int|bool az ár
     * @throws RequestResultException PDOProcessor hiba
     */
    private function getBookPrice(string $isbn): int|bool
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT bp.price FROM book_price as bp WHERE isbn=?");
        $PDOLink->setFetchType('fetch');
        $PDOLink->setValues($isbn);
        $tempResult = $PDOLink->execute();
        if ($tempResult === null) {
            throw new RequestResultException(500, ['errorCode' => 'GBPISBNNE']);
        }
        return $tempResult['price'];
    }

    /**
     * visszaad egy könyvre vonatkozó kedvezményt százalékban, isbn alapján
     * @param string $isbn könyv isbn száma
     * @return int kedvezmény mértéke
     * @throws RequestResultException adott isbn-nel könyv nem létezik
     * @throws RequestResultException PDOProcessor hiba
     */
    private function getDiscountQuantity(string $isbn): int
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT bd.discount_value FROM book_discount as bd WHERE isbn=?");
        $PDOLink->setFetchType('fetch');
        $PDOLink->setValues($isbn);
        $tempResult = $PDOLink->execute();
        if ($tempResult === false) {
            return 0;
        }
        return $tempResult['discount_value'];
    }

    /**
     * visszaadja egy könyv boritóthumnail-jét, base64string formában, isbn alapján |
     * ha a thumbnail nem létezik létrehozza
     * ha nincs cover, egy default thumnailt ad vissza
     * @param string $isbn a köynv email cime
     * @return string a thumbail base64 string formában
     * @throws RequestResultException PDOProcesszor hiba
     */
    private function getCoverThumbnail(string $isbn): string
    {
        $PDOLink = PDOProcessorBuilder::getProcessor('select', true);
        $PDOLink->setCommand("SELECT bc.extension, bc.has_cover, bc.has_thumbnail FROM book_cover as bc WHERE book_isbn=?");
        $PDOLink->setFetchType('fetch');
        $PDOLink->setValues($isbn);
        $tempResult = $PDOLink->execute();
        if ($tempResult === false) {
            return $this->loadThumbnailFromDiskAsBase64String( 'image\coverThumbnail\no_cover.jpg');
        } else {
            if ($tempResult['has_cover'] === '1' && $tempResult['has_thumbnail'] === '0') {
                $this->createCoverThumbnail($isbn . '.' . $tempResult['extension']);
                $PDOLink = PDOProcessorBuilder::getProcessor('UPDATE', true);
                $PDOLink->setCommand("UPDATE book_cover SET has_thumbnail = '1' WHERE book_isbn=?");
                $PDOLink->setValues($isbn);
                $PDOLink->execute();
            }
            return $this->loadThumbnailFromDiskAsBase64String('image\coverThumbnail\\' . $isbn . '.' . $tempResult['extension']);
        }
    }

    /**
     * borító thumbnailkészítő függvény meghívása fájlnév alapján
     * @param string $fileName a fájlnév, pl: 963123456.jpg
     * @throws RequestResultException ha a kép nem létezik, vagy nem sikerült a mentés
     */
    private function createCoverThumbnail(string $fileName)
    {
        ImgHelper::createThumbnail($fileName, 'image\cover\\', ROOT . 'image\coverThumbnail\\', 150, 212);
    }

    /**
     * boritó thumbnail kiolvasása háttérTárból
     * @param string $file a fájl neve (teljes elérési út)
     * @return string thumbnail mint base64string
     * @throws RequestResultException ha a file nem létezik
     */
    private function loadThumbnailFromDiskAsBase64String(string $file): string
    {
        return ImgHelper::convertImageToBase64String($file);
    }
}
