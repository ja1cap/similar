<?php
namespace Weasty\Similar;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use Weasty\Similar\Cache\MultiCache;
use Weasty\Similar\Index\File\SimilarFileIndex;
use Weasty\Similar\Index\Sphinx\SimilarSphinxIndex;

/**
 * Class Similar
 * @package Weasty\Similar
 */
class Similar {

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @var \Weasty\Similar\Index\SimilarIndexInterface[]
     */
    protected $indexes = [];

    function __construct($cacheDriver = null)
    {
        $rootDir = realpath(__DIR__ . '/../../../');

        if(!$cacheDriver instanceof Cache){

            $cacheDriver = new MultiCache();

            //$sqlLiteCacheDriver = new SQLiteCache($rootDir . '/cache.sqlite');
            //$cacheDriver->addCacheProvider($sqlLiteCacheDriver);

            $cacheDir = $rootDir . '/cache';
            $fileCacheDriver = new FilesystemCache($cacheDir, '.similar_cache.data');
            $cacheDriver->addCacheProvider($fileCacheDriver);

        }

        $this->cache = $cacheDriver;

        $dataDir = $rootDir . '/data';
        $haystackFilePath = $dataDir . '/main.txt';
        $this->indexes[] = new SimilarFileIndex($haystackFilePath);
        $this->indexes[] = new SimilarSphinxIndex();

    }

    /**
     * @param string $haystackFilePath
     * @return $this
     */
    public function buildSimilar($haystackFilePath)
    {

        $haystackFileContent = @file_get_contents($haystackFilePath);
        $haystack = preg_split('/\r\n|\r|\n/', $haystackFileContent);
        $haystack = array_unique(array_filter($haystack));

        $index = new SimilarFileIndex($haystack);

        $count = 0;
        $total = count($haystack);

        foreach($haystack as $value){

            $count++;

            $similarities = $index->search($value);
            $this->getCache()->save($value, implode(',', $similarities));

            echo sprintf('%s/%s %s - %s', $count, $total, $value, count($similarities)) . PHP_EOL;

        }

        return $this;

    }

    /**
     * @param string $query
     * @return array
     */
    public function search($query)
    {

        $results = $this->getCache()->fetch($query);

        if($results){

            $results = explode(',', $results);

        } else {

            $results = [];
            foreach($this->indexes as $index){
                foreach($index->search($query) as $result){
                    $results[] = $result;
                }
            }

            $results = array_map(function($result){
                return str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $result);
            }, $results);
            $results = array_filter(array_unique($results));

            $this->getCache()->save($query, implode(',', $results));

        }

        return $results;

    }

    /**
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: new ArrayCache();
    }

    /**
     * @param \Doctrine\Common\Cache\Cache $cache
     * @return $this
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
        return $this;
    }

} 