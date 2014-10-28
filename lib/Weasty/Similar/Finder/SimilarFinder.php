<?php
namespace Weasty\Similar\Finder;
use Weasty\Similar\Index\SimilarIndex;

/**
 * Class SimilarFinder
 * @package Weasty\Similar\Finder
 */
class SimilarFinder {

    /**
     * @var array
     */
    protected $haystack;

    /**
     * @var
     */
    protected $haystackIndex;

    /**
     * @var array
     */
    protected $needleSimilarValueKeys;

    /**
     * @param $haystack
     */
    function __construct($haystack)
    {

        if(is_array($haystack)){
            array_filter($haystack);
        }

        $this->haystack = $haystack;

        $this->haystackIndex = new SimilarIndex($haystack);
        $this->haystackIndex->build();

        $this->needleSimilarValueKeys = [];

        $this->init();

    }

    /**
     * @return $this
     */
    protected function init()
    {}

    /**
     * @param string $needle
     * @param float|int $similarityMinPercent
     * @return array
     */
    protected function _findSimilarValueKeys($needle, $similarityMinPercent = 70.0)
    {
        return $this->haystackIndex->find($needle, $similarityMinPercent);
    }

    /**
     * @param $keys
     * @return array
     */
    protected function _findSimilarValuesByKeys($keys){
        return array_intersect_key($this->haystack, array_flip($keys));
    }

    /**
     * @param $needle
     * @return array
     */
    public function find($needle)
    {

        if(!isset($this->needleSimilarValueKeys[$needle])){
            $this->needleSimilarValueKeys[$needle] = $this->_findSimilarValueKeys($needle);
        }

        $results = $this->_findSimilarValuesByKeys($this->needleSimilarValueKeys[$needle]);
        array_unique($results);

        return $results;

    }

}