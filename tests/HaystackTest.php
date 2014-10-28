<?php

use Weasty\Similar\Finder\SimilarFinder;

/**
 * Class HaystackTest
 */
class HaystackTest extends PHPUnit_Framework_TestCase
{

    /**
     * @return string
     */
    protected function getFilePath()
    {
        return realpath(__DIR__ . '/../data/fruits.txt');
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
     * @param $haystackFileContent
     * @depends testHaystackFile
     */
    public function testHaystack($haystackFileContent)
    {

        $haystack = preg_split('/\r\n|\r|\n/', $haystackFileContent);
        $this->assertNotEmpty($haystack, 'Empty haystack');

        $similarFinder = new SimilarFinder($haystack);
        $similarValues = $similarFinder->find('fruit');

        $this->assertTrue(is_array($similarValues));

    }

} 