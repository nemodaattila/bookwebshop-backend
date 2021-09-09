<?php

namespace classModel;

use classDbHandler\AuthorDBHandler;
use classDbHandler\PublisherDBHandler;
use classDbHandler\SeriesDBHandler;
use exception\HttpResponseTriggerException;

class Book
{
    private ?string $isbn;

    private ?string $title;

    private string|array|null $author;

    private ?array $authorId = [];

    private ?int $type;

    private ?int $category;

    private ?string $publisher;

    private ?int $publisherId;

    private ?string $series;

    private ?int $seriesId;

    private ?int $targetAudience;

    private ?int $language;

    private ?int $year;

    private ?int $page;

    private ?int $format;

    private ?int $weight;

    private ?string $size;

    private ?string $description;

    private string|array|null $tags;

    private ?array $tagId = [];

    private ?int $price;

    private ?int $discount;

    private ?int $discountType;

    private ?string $coverUrl;

    private ?string $coverFile;

    private mixed $coverFileSource;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
//            var_dump($key);
            if (!property_exists($this, $key)) {
                throw new HttpResponseTriggerException(false, ['errorCode' => 'BMPNE', 'value' => $key]);
            }
            $this->$key = $value;

        }
    }

    /**
     * @return array|null
     */
    public function getAuthorId(): ?array
    {
        return $this->authorId;
    }

    /**
     * @return string|null
     */
    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    /**
     * @return array|null
     */
    public function getTagId(): ?array
    {
        return $this->tagId;
    }

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price ?? null;
    }

    /**
     * @return int|null
     */
    public function getDiscountType(): ?int
    {
        return $this->discountType ?? null;
    }

    /**
     * @return int|null
     */
    public function getDiscount(): ?int
    {
        return $this->discount ?? null;
    }

    /**
     * @return int|null
     */
    public function getSeriesId(): ?int
    {
        return $this->seriesId ?? null;
    }

    /**
     * @return string|null
     */
    public function getSeries(): ?string
    {
        return $this->series ?? null;
    }

    /**
     * @return string|null
     */
    public function getCoverFile(): ?string
    {
        return $this->coverFile;
    }

    /**
     * @return string|null
     */
    public function getCoverUrl(): ?string
    {
        return $this->coverUrl ?? null;
    }

    /**
     * @return mixed
     */
    public function getCoverFileSource(): mixed
    {
        return $this->coverFileSource ?? null;
    }

    public function checkNulls()
    {
        if (is_null($this->author)) $this->author = 'UNKNOWN';
        if (is_null($this->publisher)) $this->publisher = 'UNKNOWN';
        if (is_null($this->year)) $this->year = 0;
        if (is_null($this->page)) $this->page = 0;
    }

    public function formatBeforeSave()
    {
        //original: is_null
        if (!empty($this->author)) {
            if (is_string($this->author)) {
                $this->authorId = explode(",", $this->author);
            }
        }
        if (!empty($this->tags)) {
//            print_r($this->tags);
            if (is_string($this->tags))
                $this->tagId = explode(",", $this->tags);
            if (is_array($this->tags)) {
                $this->tagId = $this->tags;
                if (isset($this->tagId[0][0]) && $this->tagId[0][0] === '') $this->tagId[0] = [];
                if (isset($this->tagId[1][0]) && $this->tagId[1][0] === '') $this->tagId[1] = [];
            }
        }

        if (!empty($this->author)) {
            if (is_string($this->author)) {
                $this->authorId = array_map(function ($value) {
                    if ($value === 'UNKNOWN') return 1;
                    return (new AuthorDBHandler())->getIdByName($value);
                }, $this->authorId);
            }
            if (is_array($this->author)) {
                $this->authorId[0] = array_map(function ($value) {
                    if ($value === 'UNKNOWN') return 1;
                    return (new AuthorDBHandler())->getIdByName($value);
                }, $this->author[0]);
                $this->authorId[1] = array_map(function ($value) {
                    if ($value === 'UNKNOWN') return 1;
                    return (new AuthorDBHandler())->getIdByName($value);
                }, $this->author[1]);
            }

        }

        if (!empty($this->publisher)) {
            $this->publisherId = ($this->publisher === 'UNKNOWN') ? 1 : (new PublisherDBHandler())->getIdByName(htmlspecialchars($this->publisher, ENT_QUOTES));
        }

        if (!empty($this->series)) {
            $this->seriesId = (new SeriesDBHandler())->getIdByName(htmlspecialchars($this->series, ENT_QUOTES));
        }

        if (!empty($this->coverUrl)) {
            $this->coverFileSource = file_get_contents($this->coverUrl);
            if (!$this->coverFileSource) {
                throw new HttpResponseTriggerException(false, ['errorcode' => 'BUFCURLNE']);
            }

        } elseif (!empty($this->coverFile)) {

            $this->coverFileSource = file_get_contents($this->coverFile);
            if (!$this->coverFileSource) {
                throw new HttpResponseTriggerException(false, ['errorcode' => 'BUFCFILENE']);
            }
        }
        if (empty($this->isbn) || str_starts_with($this->isbn, 'AN-')) {
            $this->isbn = "AN-" . md5(serialize($this));
        }

    }

    public function getPropertiesForBookTableInsert(): array
    {
        return [$this->isbn, $this->title, $this->type, $this->category];
    }

    public function getPropertiesForBookTableUpdateWithoutIsbn(): array
    {
        $res = [];
        if (!empty($this->title)) $res['title'] = $this->title;
        if (!empty($this->type)) $res['type_id'] = $this->type;
        if (!empty($this->category)) $res['category_id'] = $this->category;
        return $res;
    }

    public function getPropertiesForBookDescriptionTableInsert(): array
    {
        return [$this->isbn, $this->targetAudience, $this->publisherId, $this->language, $this->year, $this->page,
            $this->format, $this->weight, $this->size, $this->description];
    }

    public function getPropertiesForBookDescriptionTableUpdate(): array
    {
        $res = [];
        if (!empty($this->publisherId)) $res['publisher_id'] = $this->publisherId;
        if (!empty($this->targetAudience)) $res['target_audience_id'] = $this->targetAudience;
        if (!empty($this->language) || (isset ($this->language) && $this->language === 0)) $res['language_id'] = $this->language;
        if (!empty($this->year) || (isset ($this->year) && $this->year === 0)) $res['year'] = $this->year;
        if (!empty($this->page) || (isset ($this->page) && $this->page === 0)) $res['page_number'] = $this->page;
        if (!empty($this->format)) $res['format_id'] = $this->format;
        if (!empty($this->weight)) $res['weight'] = $this->weight;
        if (isset ($this->weight) && $this->weight === 0) $res['weight'] = null;
        if (!empty($this->size)) $res['physical_size'] = $this->size;
        if (isset ($this->size) && $this->size === '') $res['physical_size'] = null;
        if (!empty($this->description)) $res['short_description'] = $this->description;
        if (isset ($this->description) && $this->description === '') $res['short_description'] = null;
        return $res;

    }

    public function getPropertiesForDiscountTableInsert(): array
    {
        return [$this->isbn, $this->discountType, $this->discount ?? 0];
    }
}
