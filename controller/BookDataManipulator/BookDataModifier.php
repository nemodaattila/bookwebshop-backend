<?php

namespace bookDataManipulator;

use classDbHandler\bookData\BookDBHAndler;
use classModel\Book;
use database\PDOConnection;
use exception\HttpResponseTriggerException;

class BookDataModifier
{
    public function modifyBookData(array $newData): array
    {
        $originalISBN = $newData['originalIsbn'];
        $originalCoverDelete = $newData['originalCoverDelete'];
        if (!isset($newData['isbn']))
            $newData['isbn'] = $originalISBN;
        unset($newData['originalIsbn']);
        unset ($newData['originalCoverDelete']);

        $book = new Book($newData);
//        $book->checkNulls();
        $book->formatBeforeSave();
        if (!(new BookDBHAndler)->getByIsbn($originalISBN))
            throw new HttpResponseTriggerException(false, ['errorCode' => 'BMOISBNNE']);
        if (isset($newData['isbn']) && ($newData['isbn'] !== $originalISBN)) {
            if ((new BookDBHAndler)->getByIsbn($newData['isbn']))
                throw new HttpResponseTriggerException(false, ['errorCode' => 'BMNISBNAE']);
        }

        $pdoConn = PDOConnection::getInstance();
        $pdoConn->beginTransaction();
        if (isset($newData['isbn']) && ($newData['isbn'] !== $originalISBN))
            (new BookDBHAndler)->updateIsbn($originalISBN, $newData['isbn']);

        (new BookDBHAndler)->update($book->getPropertiesForBookTableUpdateWithoutIsbn(), $book->getIsbn());

        //                if (array_key_exists("publisher", $data))        //kiadó id keresése
//                {
//                    $temp = htmlspecialchars($data["publisher"], ENT_QUOTES);
//                    $publisher = DataBaseConnector::selectFetchWithPrepare("select * from publisher where Name=?", [$temp]);
//                    $data["publisher"] = $publisher["ID"];
//                }
//
//                //book tábla adatainak kezelése - isbn,cim : string, tipus,kategória: szám
//                if ($task == "addnewbook") {
//                    DataBaseConnector::simpleInsert("book", [$data["isbn"], $data["title"], $data["type"], $data["category"]]);
//                }
//                if ($task == "modifybook") {
//                    foreach (["isbn", "title", "type", "category"] as $value) {
//                        if ((array_key_exists($value, $data)) && ($data[$value] != null)) {
//                            $isbnres = DataBaseConnector::simpleUpdate("book", [$value], [$data[$value]], ["isbn"], [$originalisbn]);
//                            /*$sql = "UPDATE book SET ".$value."=? WHERE isbn=?";
//                            $stmt= DataBaseConnector::$pdo->prepare($sql);
//                            $isbnres=$stmt->execute([$data[$value], $originalisbn]);*/
//
//                            if (($isbnres) && ($value == "isbn")) {
//                                $originalisbn = $data[$value];
//                            }
//                            if (!$isbnres) {
//                                throw new Exception("BOOK_MODIFY_" . $value . "_ERROR");
//                            }
//                        }
//                    }
//                }
//
//                //írók kezelése
//                //DO iró alias
//                if ((array_key_exists("author", $data)) && ($data["author"] != null)) {
//                    if ($task == "addnewbook") {
//                        foreach ($data["author"] as $value) {
//
//                            $aut = DataBaseConnector::selectFetchWithPrepare("select * from author where Name=?", [$value]);
//                            if ($aut == null) throw new PDOException($value . " AUTHOR_NOT_EXITS");
//                            $aut = $aut["ID"];
//                            DataBaseConnector::simpleInsert("book_author", [$data["isbn"], $aut]);
//                        }
//                    }
//                    if ($task == "modifybook") {
//
//                        foreach ($data["author"] as $value)          //új iró hozzáadása
//                        {
//
//                            if (($originaldata["Author"] == null) || (!in_array($value, $originaldata["Author"]))) {
//                                $aut = DataBaseConnector::selectFetchAllWithPrepare("select * from author where Name=?", [$value]);
//                                if ($aut == null) throw new PDOException($value . " AUTHOR_NOT_EXITS");
//                                $aut = $aut[0]["ID"];
//                                DataBaseConnector::simpleInsert("book_author", [$originalisbn, $aut]);
//                            }
//                        }
//
//                        if ($originaldata["Author"] != null) {
//                            foreach ($originaldata["Author"] as $value)          //régi iró törtlése
//                            {
//                                if (!in_array($value, $data["author"])) {
//                                    $aut = DataBaseConnector::selectFetchWithPrepare("select * from author where Name=?", [$value]);
//                                    if ($aut == null) throw new PDOException($value . " AUTHOR_NOT_EXITS");
//                                    DataBaseConnector::simpleDelete("book_author", ["ISBN", "authorid"], [$originalisbn, $aut["ID"]]);
//
//                                }
//                            }
//                        }
//                    }
//                }
//
//                //bookdesc tábla szerkesztése
//                //"isbn",célközönség,"kiadó","nyelv","kiadás éve","lapok száma","formátum","súly","méret","rvid ismertető"
//                if ($task == "addnewbook") {
//                    DataBaseConnector::simpleInsert("bookdesc", [$data["isbn"], $data["targetaud"], $data["publisher"], $data["language"], $data["year"],
//                        $data["page"], $data["format"], $data["weight"], $data["size"], $data["desc"]]);
//                }
//
//                if ($task == "modifybook") {
//                    foreach (["targetaud", "publisher", "language", "year", "page", "format", "weight", "size", "desc"] as $value) {
//                        if ((array_key_exists($value, $data)) && ($data[$value] != null)) {
//                            if ($value == "targetaud") {
//                                $res = DataBaseConnector::simpleUpdate("bookdesc", ["Targetaudience"], [$data[$value]], ["isbn"], [$originalisbn]);
//                                //$sql = "UPDATE bookdesc SET Targetaudience=? WHERE isbn=?";
//                            } elseif ($value == "desc") {
//                                $res = DataBaseConnector::simpleUpdate("book_description", ["ShortDesc"], [$data[$value]], ["isbn"], [$originalisbn]);
//                                //$sql = "UPDATE bookdesc SET ShortDesc=? WHERE isbn=?";
//                            } else $res = DataBaseConnector::simpleUpdate("book_description", [$value], [$data[$value]], ["isbn"], [$originalisbn]);
//                            //$sql = "UPDATE bookdesc SET ".$value."=? WHERE isbn=?";
//                            if (!$res) {
//                                throw new Exception("BOOK_MODIFY_" . $value . "_ERROR");
//                            }
//                        }
//                    }
//                }
//
//                //tagek kezelése
//                //tags tábla
//                /*vars::sumDumpWithLine($data);
//                vars::sumDumpWithLine($originaldata);*/
//                if ((isset($data["tags"])) || (isset($originaldata["Tags"]))) {
//                    if ($task == "addnewbook") {
//                        foreach ($data["tags"] as $value) {
//                            DataBaseConnector::simpleInsert("book_tag", [null, $data["isbn"], $value]);
//                        }
//                    }
//
//                    if ($task == "modifybook") {
//                        if (isset($data["tags"])) {
//                            foreach ($data["tags"] as $value)            //új tag hozzáadása
//                            {
//                                if (($originaldata["Tags"] == "") || (!in_array($value, $originaldata["Tags"]))) {
//                                    DataBaseConnector::simpleInsert("book_tag", [null, $originalisbn, $value]);
//                                }
//                            }
//                        }
//
//                        if ($originaldata["Tags"] !== "") {
//
//                            foreach ($originaldata["Tags"] as $value)        //régi tag törlése
//                            {
//                                if (($data["tags"] == "") || (!in_array($value, $data["tags"]))) {
//                                    DataBaseConnector::simpleDelete("book_tag", ["ISBN", "Tag"], [$originalisbn, $value]);
//                                }
//                            }
//                        }
//                    }
//                }
//
//                // ár kezelése - price tábla
//                //ár, kedvezmény
//                if ($task == "addnewbook") {
//                    DataBaseConnector::simpleInsert("book_price", [$data["isbn"], $data["price"], (int)$data["discount"], (int)$data["discounttype"]]);
//                }
//                if ($task == "modifybook") {
//                    foreach (["price", "discount", "discounttype"] as $value) {
//                        if (array_key_exists($value, $data)) {
//
//                            $res = DataBaseConnector::simpleUpdate("book_price", [$value], [$data[$value]], ["isbn"], [$originalisbn]);
//                            if (!$res) {
//                                throw new Exception("BOOK_MODIFY_" . $value . "_ERROR");
//                            }
//                        }
//                    }
//                }
//
//                //könyvsorozat kezelése
//                //series tábla
//                if (array_key_exists("series", $data)) {
//                    if (($task == "addnewbook") && ($data["series"] != null)) {
//                        $series = DataBaseConnector::selectFetchWithPrepare("select * from series where Name=?", [$data["series"]]);
//                        $series = $series["ID"];
//
//                        DataBaseConnector::simpleInsert("book_series", [$data["isbn"], $series]);
//                    }
//
//                    if ($task == "modifybook") {
//                        if (($data["series"] != null) && ($originaldata["Series"] != null)) //módosítás
//                        {
//                            $series = DataBaseConnector::selectFetchWithPrepare("select * from series where Name=?", [$data["series"]]);
//                            $series = $series["ID"];
//
//                            DataBaseConnector::simpleUpdate("book_series", ["series"], [$series], ["isbn"], [$originalisbn]);
//                        }
//
//                        if (($data["series"] == null) && ($originaldata["Series"] != null))     //törlés
//                        {
//                            DataBaseConnector::simpleDelete("book_series", ["ISBN"], [$originalisbn]);
//                        }
//                    }
//                }
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
        $pdoConn->commit();
        return ['isbn' => $newData['isbn'] | $originalISBN];
    }

}
