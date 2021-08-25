<?php

namespace bookDataManipulator;

use classDbHandler\AuthorDBHandler;
use classDbHandler\bookData\BookAuthorDBHandler;
use classDbHandler\bookData\BookCoverDBHandler;
use classDbHandler\bookData\BookDBHAndler;
use classDbHandler\bookData\BookDescriptionDBHandler;
use classDbHandler\bookData\BookDiscountDBHandler;
use classDbHandler\bookData\BookPriceDBHandler;
use classDbHandler\bookData\BookSeriesDBHandler;
use classDbHandler\bookData\BookTagDBHandler;
use classDbHandler\bookData\BookUploadDateDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use classModel\Book;
use database\PDOProcessorBuilder;
use exception\HttpResponseTriggerException;

class BookUploader
{



    public function addNewBook(array $data)
    {
        $book = new Book($data);
        $book->checkNulls();
        $book->formatBeforeSave();



        $PDOLink = PDOProcessorBuilder::getProcessor('select',true);

        $exists = (new BookDBHAndler)->getByIsbn($book->getIsbn());
        if ($exists)
            throw new HttpResponseTriggerException(false, ['errorCode'=>'BUISBNAE']);
        $PDOLink->beginTransaction();
        (new BookDBHAndler)->insert(...$book->getPropertiesForBookTable());
        array_map(function ($value) use ($book){
            (new BookAuthorDBHandler())->insert($book->getIsbn(), $value);
        }, $book->getAuthorId());
        (new BookDescriptionDBHandler())->insert(...$book->getPropertiesForBookDescriptionTable());

        array_map(function ($value) use ($book){
            (new BookTagDBHandler())->insert($book->getIsbn(), $value);
        }, $book->getTagId());
        (new BookPriceDBHandler())->insert($book->getIsbn(), $book->getPrice());
        if (!is_null($book->getSeriesId()))
        {
            (new BookSeriesDBHandler())->insert($book->getIsbn(),$book->getSeriesId());
        }
        if ($book->getDiscountType()!==0)
        {
            (new BookDiscountDBHandler())->insert(...$book->getPropertiesForDiscountTable());
        }
        (new BookUploadDateDBHandler())->insert($book->getIsbn(), time());

        if (!is_null($book->getCoverUrl()))
        {
            $ext = pathinfo($book->getCoverUrl(), PATHINFO_EXTENSION);
            file_put_contents('image/cover/'.$book->getIsbn().'.'.$ext, $book->getCoverFileSource());
            (new BookCoverDBHandler())->insert($book->getIsbn(), $ext);
        }
        elseif (!is_null($book->getCoverFile()))
        {
            $ext = explode('/', mime_content_type($book->getCoverFile()))[1];
            file_put_contents('image/cover/'.$book->getIsbn().'.'.$ext, $book->getCoverFileSource());
            (new BookCoverDBHandler())->insert($book->getIsbn(), $ext);

        }


//
//                //borító kezelés
//                //cover tábla szerkesztése
//                //valamint a képfálok kezelése szerveren
//
//                if (array_key_exists("cover", $data)) {
//
//                    if (($task == "addnewbook") && ($data["cover"] != null)) //kép mentése szerverre, bejegyzés létrehozása
//                    {
//                        if (strpos($data["cover"], "data:") === 0) {
//                            $type = explode(";", $data["cover"])[0];
//                            $type = explode("/", $type)[1];
//                            $img = $data["cover"];
//                            $img = str_replace('data:image/' . $type . ';base64,', '', $img);
//                            $img = str_replace(' ', '+', $img);
//                            $cover = base64_decode($img);
//                            //$temp=file_get_contents($data["cover"]);
//                            file_put_contents(FILE . "/covers/" . $data['isbn'] . "." . $type, $cover);
//                            DataBaseConnector::simpleInsert("cover", [$data["isbn"], $data['isbn'] . "." . $type]);
//                        }
//                        if (strpos($data["cover"], "http") === 0) {
//
//                            $name = Arrays::arrayLast(explode("/", $data["cover"]));
//
//                            file_put_contents(FILE . "/covers/" . $name, file_get_contents($data["cover"]));
//                            DataBaseConnector::simpleInsert("cover", [$data["isbn"], $name]);
//                        }
//
//                    }
//
//                    if ($task == "modifybook")        //módosítás
//                    {
//
//                        if ($originaldata["Cover"][0] != "no_cover.jpg")          //eredeti kép törlése
//                        {
//                            DataBaseConnector::simpleDelete("cover", ["ISBN"], [$originalisbn]);
//                            if (file_exists(FILE . "/covers/" . $originaldata["Cover"][0])) {
//                                unlink(FILE . "/covers/" . $originaldata["Cover"][0]);
//                            } else {
//                                throw new PDOException("BOOK_MODIFY_ERROR_DELETABLE_COVER_NOT_EXISTS-" . $originaldata["Cover"][0]);
//                            }
//                        }
//                        if ($data["cover"] != null)                               //új kép felvétele, mentése
//                        {
//                            if (strpos($data["cover"], "data:") === 0) {
//                                $type = explode(";", $data["cover"])[0];
//                                $type = explode("/", $type)[1];
//                                $img = $data["cover"];
//                                $img = str_replace('data:image/' . $type . ';base64,', '', $img);
//                                $img = str_replace(' ', '+', $img);
//                                $cover = base64_decode($img);
//                                //$temp=file_get_contents($data["cover"]);
//                                file_put_contents(FILE . "/covers/" . $originalisbn . "." . $type, $cover);
//                                DataBaseConnector::simpleInsert("cover", [$originalisbn, $originalisbn . "." . $type]);
//                            }
//                            if (strpos($data["cover"], "http") === 0) {
//
//                                $name = Arrays::arrayLast(explode("/", $data["cover"]));
//
//                                file_put_contents(FILE . "/covers/" . $name, file_get_contents($data["cover"]));
//                                DataBaseConnector::simpleInsert("cover", [$originalisbn, $name]);
//                            }
//                        }
//                    }
//                }
//
//                //uploaddate tábla szerksztése - mikor töltödött fel a könyv
//                if ($task == "addnewbook") {
//                    $now = time();
//                    DataBaseConnector::simpleInsert("uploaddate", [$data["isbn"], $now]);
//                }
//
//                $comm = DataBaseConnector::commitTransaction();               //tranzakció hibátlan lezárása
//                //vars::sumDump($comm);
//                DataBaseConnector::close();
//                if ($comm) {
//                    if ($task == "addnewbook") {
//                        return ["SUCC", $task, $data["title"], $data["isbn"]];
//                    }
//                    if ($task == "modifybook") {
//                        return ["SUCC", $task, $originalisbn];
//                    }
//                } else {
//                    return ["ERR", $task . " - PDO_COMMIT_ERROR"];
//                }
//            } catch (Exception $e)                                //hiba esetén rollback , és hibaüzenet küldése
//            {
//                DataBaseConnector::rollbackTransaction();
//                return DataBaseConnector::exceptionHandler($e);
//            }
//        }
        $PDOLink->commit();
        return get_object_vars($book);
    }


}
