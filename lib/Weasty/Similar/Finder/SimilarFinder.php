<?php
namespace Weasty\Similar\Finder;

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
     * @var array
     */
    protected $needleSimilarValueKeys;

    /**
     * @param $haystack
     */
    function __construct($haystack)
    {

        $this->haystack = $haystack;
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
     * @param float|int $similarMinPercent
     * @return array
     */
    protected function _findSimilarValueKeys($needle, $similarMinPercent = 75.0)
    {

        $similarValueKeys = [];

        foreach($this->haystack as $key => $comparisonValue){

            if($comparisonValue == $needle){
                continue;
            }

            $comparisonValuePercent = 0;
            similar_text($needle, $comparisonValue, $comparisonValuePercent);

            if($comparisonValuePercent >= $similarMinPercent){
                $similarValueKeys[] = $key;
            }

        }

        return $similarValueKeys;

    }

    /**
     * @param $keys
     * @return array
     */
    protected function _findSimilarValuesByKeys($keys){
        return array_intersect_key($this->haystack, $keys);
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
        return $this->_findSimilarValuesByKeys($this->needleSimilarValueKeys[$needle]);
    }

}