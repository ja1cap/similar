<?php
namespace Weasty\Similar\Index;

/**
 * Class SimilarIndex
 * @package Weasty\Similar\Index
 */
class SimilarIndex {

    const OPT_SKIP_NUMBERS = 1;
    const OPT_SKIP_WORDS = 2;

    /**
     * @var array
     */
    protected $haystack;

    /**
     * @var array
     */
    protected $index;

    protected $options = [
        self::OPT_SKIP_NUMBERS => true,
        self::OPT_SKIP_WORDS => [
            '$',
            'rx',
            'mg',
            'buy',
            'online',
        ],
    ];

    function __construct($haystack)
    {
        $this->haystack = $haystack;
    }

    /**
     * @return $this
     */
    public function build(){

        $this->index = [];

        foreach($this->haystack as $key => $value){
            $this->index[$key] = $this->prepareValue($value);
        }

        return $this;

    }

    /**
     * @param $value
     * @return string
     */
    protected function prepareValue($value){

        $indexValue = $value;

        if($this->getOption(self::OPT_SKIP_NUMBERS)){
            $indexValue = trim(preg_replace('/\d+/', '', $indexValue));
        }

        if($this->getOption(self::OPT_SKIP_WORDS)){
            $indexValue = trim(str_replace($this->getOption(self::OPT_SKIP_WORDS), '', $indexValue));
        }

        $indexValue = trim($indexValue);
        $indexValue = preg_replace('!\s+!', ' ',$indexValue);

        return $indexValue;

    }

    /**
     * @param $query
     * @param $similarityMinPercent
     * @return array
     */
    public function find($query, $similarityMinPercent){

        $query = $this->prepareValue($query);

        $similarValueKeys = [];

        foreach($this->index as $key => $indexValue){

            if($indexValue == $query){
                continue;
            }

            $similarityPercent = 0;
            similar_text($query, $indexValue, $similarityPercent);
            $isSimilar = ($similarityPercent >= $similarityMinPercent);

/*
            $lev = levenshtein($query,$indexValue);
            if($lev == 0)
            {
                continue;
            }
            else if($lev >= 17)
            {
                $isSimilar = true;
            }
*/
            if($isSimilar){
                $similarValueKeys[] = $key;
            }

        }

        return $similarValueKeys;

    }

    /**
     * @param $opt
     * @return null
     */
    public function getOption($opt){
        return isset($this->options[$opt]) ? $this->options[$opt] : null;
    }

} 