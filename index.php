<?php
require 'vendor/autoload.php';

$cacheDriver = new \Weasty\Similar\Cache\MultiCache();

$sqlLiteCacheDriver = new \Weasty\Similar\Cache\SQLiteCache(__DIR__ . '/cache.sqlite');
$cacheDriver->addCacheProvider($sqlLiteCacheDriver);

$fileCacheDriver = new \Weasty\Similar\Cache\FilesystemCache(__DIR__ . '/cache', '.similar_cache.data');
$cacheDriver->addCacheProvider($fileCacheDriver);

$cacheDriver->setNamespace('__SIMILAR__');

$haystackFilePath = __DIR__ . '/data/main.txt';
$similar = new \Weasty\Similar\Similar();

$similar
    ->setCache($cacheDriver)
    ->buildSimilar($haystackFilePath)
;

$haystackFileContent = @file_get_contents($haystackFilePath);
$haystack = preg_split('/\r\n|\r|\n/', $haystackFileContent);

$count = 1;
foreach($haystack as $query){

    $similarities = $similar->search($query);
    if($similarities){
        $count++;
        echo sprintf('%s - %s - %s', $query, count($similarities), $count) . PHP_EOL;
    }

}