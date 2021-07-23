<?php

namespace rest;

use classModel\RequestParameters;
use database\PDOProcessorBuilder;
use databaseSource\WhereConditionsBackboneClass;
use exception\HttpResponseTriggerException;
use helper\VariableHelper;
use PDO;

/**
 * Class BookListGetter http request processor, return a list of book isbn-s, which matches search parameters
 * @package rest
 */
class BookListGetter
{
    /**
     * searches for books which match search parameters
     * @param RequestParameters $requestData http request parameters
     * @throws HttpResponseTriggerException on success, throws the isbn list
     * @throws HttpResponseTriggerException bad processor type
     * @throws HttpResponseTriggerException bad search parameter type
     */
    public function getBookList(RequestParameters $requestData): void
    {
        //DO parameter validation
        $parameters = $requestData->getRequestData();
        if (gettype($parameters) === 'object')
            $parameters['criteria'] = $this->checkAndConvertSearchCriteria($parameters['criteria']);
        [$PDOLink, $dataSource] = PDOProcessorBuilder::getProcessorAndDataSource('select');
        switch ($parameters['order']) {
            case 'Title':
                $dataSource->addTable('book', 'b');
                $dataSource->addAttributes('book', ['isbn']);
                break;
            case 'Year':
                $dataSource->addTable('book_description', 'bd');
                $dataSource->addAttributes('book_description', ['isbn']);
                break;
            case 'Price':
                $dataSource->addTable('book_price', 'bp');
                $dataSource->addAttributes('book_price', ['isbn']);
                break;
            case 'Author':
                $dataSource->addTable('book', 'b');
                $dataSource->addAttributes('book', ['isbn' => 'MISBN']);
                [$subPDOLink, $subDataSource] = PDOProcessorBuilder::getProcessorAndDataSource('select');
                $subDataSource->addTable('book', 'sb');
                $subDataSource->addTable('author', 'sa');
                $subDataSource->addTable('book_author', 'sba');
                $subDataSource->addAttributes('author', ['name']);
                $subDataSource->addWhereCondition('=', ['book_author.author_id', 'author.ID'], 'AND');
                $subDataSource->addWhereCondition('=', ['book.isbn', 'book_author.isbn'], 'AND');
                $subDataSource->addWhereCondition('=', ['book.isbn', 'MISBN'], 'AND');
                $subDataSource->setOrder('author.name');
                $subDataSource->enableLimit();
                $dataSource->bindValue(1, PDO::PARAM_INT);
                $dataSource->addSubQueryAsAttribute($subPDOLink, $subDataSource, 'FirstAuthor');
                break;
            default:
                throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOPTTA', 'type' => $parameters['order']], 500);
        }
        switch ($parameters['order']) {
            case 'Title':
            case 'Author':
                foreach ($parameters['criteria'] as $key => $value) {
                    switch ($key) {
                        case 'Author':
                            $dataSource->addTable('book_author', 'ba');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_author.isbn'], 'AND');
                            break;
                        case 'Series':
                            $dataSource->addTable('book_series', 'bs');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_series.isbn'], 'AND');
                            break;
                        case 'Tags':
                            $dataSource->addTable('book_tag', 'bt');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_tag.isbn'], 'AND');
                            break;
                        case "TargetAudience":
                        case "Publisher":
                        case "Language":
                        case "Year":
                        case "Pages":
                        case "Format":
                            $dataSource->addTable('book_description', 'bd');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_description.isbn'], 'AND');
                            break;
                        case "Price":
                            $dataSource->addTable('book_price', 'bp');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_price.isbn'], 'AND');
                            break;
                        case 'Discount':
                        case 'DiscountCategory':
                            $dataSource->addTable('book_discount', 'bdc');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_discount.isbn'], 'AND');
                            break;
                        case 'ReleaseDate':
                            $dataSource->addTable('book_upload_date', 'bu');
                            $dataSource->addWhereCondition('=', ['book.isbn', 'book_upload_date.isbn'], 'AND');
                            break;
                    }
                }
                break;
            case 'Year':
                foreach ($parameters['criteria'] as $key => $value) {
                    switch ($key) {
                        case "Quick":
                        case"ISBN":
                        case "Title":
                        case"Type":
                        case "Category":
                        case "MainCategory":
                            $dataSource->addTable('book', 'b');
                            $dataSource->addWhereCondition("=", ['book.isbn', 'book_description.isbn'], 'AND');
                            break;
                        case 'Author':
                            $dataSource->addTable('book_author', 'ba');
                            $dataSource->addWhereCondition("=", ['book_description.isbn', 'book_author.isbn'], 'AND');
                            break;
                        case 'Series':
                            $dataSource->addTable('book_series', 'bs');
                            $dataSource->addWhereCondition("=", ['book_description.isbn', 'book_series.isbn'], 'AND');
                            break;
                        case 'Tags':
                            $dataSource->addTable('book_tag', 'bt');
                            $dataSource->addWhereCondition("=", ['book_description.isbn', 'book_tag.isbn'], 'AND');
                            break;
                        case "Price":
                            $dataSource->addTable('book_price', 'bp');
                            $dataSource->addWhereCondition("=", ['book_description.isbn', 'book_price.isbn'], 'AND');
                            break;
                        case 'Discount':
                        case 'DiscountCategory':
                            $dataSource->addTable('book_discount', 'bpc');
                            $dataSource->addWhereCondition("=", ['book_description.isbn', 'book_discount.isbn'], 'AND');
                            break;
                    }
                }
                break;
            case 'Price':
                foreach ($parameters['criteria'] as $key => $value) {
                    switch ($key) {
                        case "Quick":
                        case"ISBN":
                        case "Title":
                        case"Type":
                        case "Category":
                        case "MainCategory":
                            $dataSource->addTable('book', 'b');
                            $dataSource->addWhereCondition("=", ['book.isbn', 'book_price.isbn'], 'AND');
                            break;
                        case 'Author':
                            $dataSource->addTable('book_author', 'ba');
                            $dataSource->addWhereCondition("=", ['book_price.isbn', 'book_author.isbn'], 'AND');
                            break;
                        case 'Series':
                            $dataSource->addTable('book_series', 'bs');
                            $dataSource->addWhereCondition("=", ['book_price.isbn', 'book_series.isbn'], 'AND');
                            break;
                        case "TargetAudience":
                        case "Publisher":
                        case "Language":
                        case "Year":
                        case "Pages":
                        case "Format":
                            $dataSource->addTable('book_description', 'bd');
                            $dataSource->addWhereCondition("=", ['book_price.isbn', 'book_description.isbn'], 'AND');
                            break;
                    }
                }
                break;
        }
        $newOrder = match ($parameters['order']) {
            'Title' => 'book.title',
            'Year' => 'book_description.year',
            'Price' => 'book_price.price',
            'Author' => 'FirstAuthor',
            default => throw new HttpResponseTriggerException(false, ['errorCode' => 'PDOPTTA', 'type' => $parameters['order']], 500)
        };
        $parameters['order'] = $newOrder;
        if ($parameters['criteria'] !== []) {
            foreach ($parameters['criteria'] as $key => $value) {
                switch ($key) {
                    case 'Quick':
                        $dataSource->setDistinct();
                        $dataSource->addTable('book', 'b');
                        $dataSource->addTable('book_author', 'ba');
                        $dataSource->addTable('author', 'a');
                        $dataSource->addWhereCondition('=', ['book_author.author_id', 'author.id'], 'AND');
                        $dataSource->addWhereCondition('=', ['book.isbn', 'book_author.isbn'], 'AND');
                        $subWhereObj = new WhereConditionsBackboneClass();
                        $subWhereObj->addWhereCondition('LIKE', [$dataSource->checkTableExists('author.name'), '?']);
                        $subWhereObj->addWhereCondition('LIKE', [$dataSource->checkTableExists('book.title'), '?'], 'OR');
                        $dataSource->addConditionObject($subWhereObj, 'AND', true);
                        $dataSource->bindValue('%' . $value . '%');
                        $dataSource->bindValue('%' . $value . '%');
                        break;
                    case 'ISBN':
                        $dataSource->addTable('book', 'b');
                        $dataSource->addWhereCondition('LIKE', ['book.isbn', '?'], 'AND');
                        $dataSource->bindValue('%' . $value . '%');
                        break;
                    case 'Title':
                        $dataSource->addTable('book', 'b');
                        $dataSource->addWhereCondition('LIKE', ['book.title', '?'], 'AND');
                        $dataSource->bindValue('%' . $value . '%');
                        break;
                    case 'Author':
                        $dataSource->addTable('book', 'b');
                        $dataSource->addTable('book_author', 'ba');
                        $dataSource->addTable('author', 'a');
                        $dataSource->addWhereCondition('=', ['book_author.author_id', 'author.id'], 'AND');
                        $dataSource->addWhereCondition('LIKE', ['author.name', '?'], 'AND');
                        $dataSource->bindValue('%' . $value . '%');
                        break;
                    case 'Type':
                        $dataSource->addTable('book', 'b');
                        $dataSource->addWhereCondition('=', ['book.type_id', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'Category':
                        $dataSource->addTable('book', 'b');
                        $dataSource->addWhereCondition('=', ['book.category_id', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'MainCategory':
                        $dataSource->addTable('book', 'b');
                        $dataSource->addTable('meta_subcategory', 'msc');
                        $dataSource->addTable('meta_main_category', 'mmc');
                        $dataSource->addWhereCondition('=', ['book.category_id', 'meta_subcategory.id'], 'AND');
                        $dataSource->addWhereCondition('=', ['meta_subcategory.main_category_id', 'meta_main_category.id'], 'AND');
                        $dataSource->addWhereCondition('=', ['meta_main_category.id', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'TargetAudience':
                        $dataSource->addTable('book_description', 'bd');
                        $dataSource->addTable('meta_target_audience', 'mta');
                        $dataSource->addWhereCondition('=', ['book_description.target_audience_id', '?'], 'AND');
                        $dataSource->addWhereCondition('=', ['book_description.target_audience_id', 'meta_target_audience.id'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'Publisher':
                        $dataSource->addTable('book_description', 'bd');
                        $dataSource->addTable('publisher', 'p');
                        $dataSource->addWhereCondition('=', ['book_description.publisher_id', 'publisher.id'], 'AND');
                        $dataSource->addWhereCondition('LIKE', ['publisher.name', '?'], 'AND');
                        $dataSource->bindValue('%' . $value . '%');
                        break;
                    case 'Series':
                        $dataSource->addTable('book_series', 'bs');
                        $dataSource->addTable('series', 's');
                        $dataSource->addWhereCondition('=', ['book_series.series_id', 'series.id'], 'AND');
                        $dataSource->addWhereCondition('LIKE', ['series.name', '?'], 'AND');
                        $dataSource->bindValue('%' . $value . '%');
                        break;
                    case 'Language':
                        $dataSource->addTable('book_description', 'bd');
                        $dataSource->addWhereCondition('=', ['book_description.language_id', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'Year':
                        $dataSource->addTable('book_description', 'bd');
                        $dataSource->addWhereCondition('=', ['book_description.year', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'Pages':
                        $pages = [[0, 100], [101, 250], [251, 500], [501, 1000], [1001, 10000]];
                        $dataSource->addTable('book_description', 'bd');
                        $dataSource->addWhereCondition('>=', ['book_description.page_number', '?'], 'AND');
                        $dataSource->addWhereCondition('<=', ['book_description.page_number', '?'], 'AND');
                        $dataSource->bindValue($pages[$value][0]);
                        $dataSource->bindValue($pages[$value][1]);
                        break;
                    case 'Format':
                        $dataSource->addTable('book_description', 'bd');
                        $dataSource->addWhereCondition('=', ['book_description.format_id', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'Tags':
                        $dataSource->setDistinct();
                        $dataSource->addTable('book_tag', 'bt');
                        $subWhereObj = new WhereConditionsBackboneClass();
                        foreach ($value as $key2) {
                            $subWhereObj->addWhereCondition('=', [$dataSource->checkTableExists('book_tag.tag_id'), '?'], 'OR');
                            $dataSource->bindValue($key2);
                        }
                        $dataSource->addConditionObject($subWhereObj, 'AND', true);
                        break;
                    case 'Price':
                        $pages = [[0, 1000], [1001, 3000], [3001, 6000], [6001, 10000], [10001, 100000]];
                        $dataSource->addTable('book_price', 'bp');
                        $dataSource->addWhereCondition('BETWEEN', ['book_price.price', '?', '?'], 'AND');
                        $dataSource->bindValue($pages[$value][0], PDO::PARAM_INT);
                        $dataSource->bindValue($pages[$value][1], PDO::PARAM_INT);
                        break;
                    case 'Discount':
                        $pages = [[0, 0], [1, 5], [6, 15], [16, 30], [31, 50], [51, 100]];
                        $dataSource->addTable('book_discount', 'bdc');
                        $dataSource->addWhereCondition('BETWEEN', ['book_discount.discount_value', '?', '?'], 'AND');
                        $dataSource->bindValue($pages[$value][0], PDO::PARAM_INT);
                        $dataSource->bindValue($pages[$value][1], PDO::PARAM_INT);
                        break;
                    case 'DiscountCategory':
                        $dataSource->addTable('book_discount', 'bdc');
                        $dataSource->addWhereCondition('=', ['book_discount.discount_id', '?'], 'AND');
                        $dataSource->bindValue($value);
                        break;
                    case 'ReleaseDate':
                        $pages = [6400, 259200, 604800, 2678400];
                        $dataSource->addTable('book_upload_date', 'bu');
                        $subWhereObj = new WhereConditionsBackboneClass();
                        $subWhereObj->addWhereCondition('-', ['?', $dataSource->checkTableExists('book_upload_date.upload_date')]);
                        $dataSource->addWhereCondition('BETWEEN', [$subWhereObj, '?', '?'], 'AND');
                        $dataSource->bindValue(time(), PDO::PARAM_INT);
                        $dataSource->bindValue(0, PDO::PARAM_INT);
                        $dataSource->bindValue($pages[$value], PDO::PARAM_INT);
                        break;
                }
            }
        }
        if ($parameters['offset'] !== '0') {
            $dataSource->enableOffset();
            $dataSource->bindValue((int)$parameters['offset'], PDO::PARAM_INT);
        }
        if ($parameters['limit'] !== '0') {
            $dataSource->enableLimit();
            $dataSource->bindValue((int)$parameters['limit'], PDO::PARAM_INT);
        }
        $dataSource->setOrder($parameters['order']);
        $dataSource->setOrderDirection($parameters['orderDir']);
        $tempList = $PDOLink->query($dataSource, 'fetchAll', PDO::FETCH_ASSOC);
        $isbnCount = $PDOLink->countQuery();
        $getParam = ($parameters['order'] === 'FirstAuthor') ? 'MISBN' : 'isbn';
        $isbnList = [];
        foreach ($tempList as $value) {
            $isbnList[] = $value[$getParam];
        }
        throw new HttpResponseTriggerException(true, ['list' => $isbnList, 'count' => $isbnCount]);
    }

    /**
     * converts JSON search criteria to object, then an associative array
     * @param string $criteria a search criteria in JSON string form
     * @return array criteria as array
     * @throws HttpResponseTriggerException if the data converted from JSON is not an object
     */
    private function checkAndConvertSearchCriteria(string $criteria): array
    {
        if ($criteria === '{}') {
            return [];
        }
        $criteria = json_decode($criteria);
        if (is_object($criteria)) {
            return VariableHelper::convertStdClassToArray($criteria);
        }
        throw new HttpResponseTriggerException(false, ['errorMessage' => 'DBLHCT', 'type' => gettype($criteria)], 500);
    }

}
