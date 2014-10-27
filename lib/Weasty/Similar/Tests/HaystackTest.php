<?php

namespace Weasty\Similar\Tests;

use PHPUnit_Framework_TestCase;
use Weasty\Similar\Finder\SimilarFinder;

/**
 * Class HaystackTest
 * @package Weasty\Similar\Tests
 */
class HaystackTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return string
     */
    protected function getFilePath()
    {
        return realpath(__DIR__ . '/../../../../data/fruits.txt');
    }

    /**
     * @return string
     */
    public function testHaystackFile()
    {

        $filePath = $this->getFilePath();

        $this->assertFileExists($filePath, 'Haystack file not found');

        return @file_get_contents($filePath);

    }

    /**
     * @param $haystack
     * @depends testHaystackFile
     */
    public function testHaystack($haystack)
    {

        $this->assertNotEmpty($haystack, 'Empty haystack');

        $similarFinder = new SimilarFinder($haystack);
        //$similarValues = $similarFinder->find('fruit');

        //var_dump($similarValues);

    }

} 