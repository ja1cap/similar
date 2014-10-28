<?php
namespace Weasty\Similar\Index;

/**
 * Class AbstractSimilarIndex
 * @package Weasty\Similar\Index
 */
abstract class AbstractSimilarIndex implements SimilarIndexInterface {

    /**
     * @var array
     */
    protected $options = [
        self::OPT_SKIP_NUMBERS => true,
        self::OPT_SKIP_WORDS => [
            '$',
            'rx',
            'mg',
            'buy',
            'online',
            'for sale',
            'dose',
            'age',
            'of',
            'vs',
            'in',
            'were',
            'to',
            '-',
            'for',
            'by',
            'mail',
            'can',
            'you',
            'take',
            'get',
            'at',
            'per',
            'with',
            'without',
        ],
    ];

    /**
     * @param $value
     * @return string
     */
    protected function prepareValue($value){

        $indexValue = $value;

        $indexValue = str_replace(',', '', $indexValue);

        if($this->getOption(self::OPT_SKIP_NUMBERS)){
            $indexValue = trim(preg_replace('/\d+/', '', $indexValue));
        }

        if($this->getOption(self::OPT_SKIP_WORDS)){
            $indexValue = trim($this->cleanWords($indexValue));
        }

        $indexValue = trim($indexValue);
        $indexValue = preg_replace('!\s+!', ' ',$indexValue);

        return $indexValue;

    }

    /**
     * @param $value
     * @return mixed
     */
    protected function cleanWords($value) {

        $wordList = '';

        foreach($this->getOption(self::OPT_SKIP_WORDS) as $word){
            $wordList .= str_replace(chr(13), '', $word).'|';
        }
        $wordList = substr($wordList,0,-1);

        $value = preg_replace("/\b($wordList)\b/ie", 'preg_replace("/./","","\\1")', $value);
        return $value;

    }

    /**
     * @param $opt
     * @return null
     */
    public function getOption($opt){
        return isset($this->options[$opt]) ? $this->options[$opt] : null;
    }

}