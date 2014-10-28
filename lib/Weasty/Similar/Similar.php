<?php
namespace Weasty\Similar;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Weasty\Similar\Cache\MultiCache;
use Weasty\Similar\Cache\SQLiteCache;
use Weasty\Similar\Finder\SimilarFinder;

/**
 * Class Similar
 * @package Weasty\Similar
 */
class Similar {

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    function __construct()
    {
        $rootDir = realpath(__DIR__ . '/../../../');

        $cacheDir = $rootDir . '/cache';
        $cacheDriver = new MultiCache();

        $sqlLiteCacheDriver = new SQLiteCache($rootDir . '/cache.sqlite');
        $cacheDriver->addCacheProvider($sqlLiteCacheDriver);

        $fileCacheDriver = new FilesystemCache($cacheDir, '.similar_cache.data');
        $cacheDriver->addCacheProvider($fileCacheDriver);

        $cacheDriver->setNamespace('__SIMILAR__');

        $this->cache = $cacheDriver;

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

        $finder = new SimilarFinder($haystack);

        $count = 0;
        $total = count($haystack);

        foreach($haystack as $value){

            $count++;

            $similarities = $finder->find($value);
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
        return array_filter(explode(',', $this->getCache()->fetch($query)));
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