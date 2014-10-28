<?php
namespace Weasty\Similar\Index;

/**
 * Interface SimilarIndexInterface
 * @package Weasty\Similar\Index
 */
interface SimilarIndexInterface {

    const OPT_SKIP_NUMBERS = 1;
    const OPT_SKIP_WORDS = 2;

    /**
     * @param $query
     * @return array
     */
    public function search($query);

}