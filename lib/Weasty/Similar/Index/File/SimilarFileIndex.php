<?php
namespace Weasty\Similar\Index\File;

use Weasty\Similar\Index\AbstractSimilarIndex;

/**
 * Class SimilarFileIndex
 * @package Weasty\Similar\Index\File
 */
class SimilarFileIndex extends AbstractSimilarIndex {

    /**
     * @var array
     */
    protected $haystack;

    /**
     * @var string
     */
    protected $haystackFilePath;

    /**
     * @var array
     */
    protected $index;

    /**
     * @var array
     */
    protected $querySimilarValueKeys;

    function __construct($haystackFilePath)
    {
        $this->haystackFilePath = $haystackFilePath;
        $this->querySimilarValueKeys = [];
    }

    /**
     * @return $this
     */
    public function build(){

        $this->index = [];

        foreach($this->getHaystack() as $key => $value){
            $this->index[$key] = $this->prepareValue($value);
        }

        return $this;

    }

    /**
     * @param $query
     * @return array
     */
    public function searchKeys($query){

        $query = $this->prepareValue($query);
        if(!$query){
            return [];
        }

        if(!$this->index){
            $this->build();
        }

        $similarityMinPercent = 70.0;

        $similarValueKeys = [];

        foreach($this->index as $key => $indexValue){

            if($indexValue == $query){
                continue;
            }

            $similarityPercent = 0;
            similar_text($query, $indexValue, $similarityPercent);
            $isSimilar = ($similarityPercent >= $similarityMinPercent);

            if($isSimilar){
                $similarValueKeys[] = $key;
            }

        }

        return $similarValueKeys;

    }

    /**
     * @param $keys
     * @return array
     */
    protected function getSimilarByKeys($keys){
        return array_intersect_key($this->getHaystack(), array_flip($keys));
    }

    /**
     * @param $query
     * @return array
     */
    public function search($query)
    {

        if(!isset($this->querySimilarValueKeys[$query])){
            $this->querySimilarValueKeys[$query] = $this->searchKeys($query);
        }

        $results = $this->getSimilarByKeys($this->querySimilarValueKeys[$query]);
        array_unique($results);

        return $results;

    }

    /**
     * @return array
     */
    protected function getHaystack(){
        if(!$this->haystack){
            $haystackFileContent = @file_get_contents($this->haystackFilePath);
            $haystack = preg_split('/\r\n|\r|\n/', $haystackFileContent);
            $this->haystack = array_unique(array_filter($haystack));
        }
        return $this->haystack;
    }

}