<?php

require 'vendor/autoload.php';

$cacheDriver = new \Weasty\Similar\Cache\MultiCache();

$sqlLiteCacheDriver = new \Weasty\Similar\Cache\SQLiteCache(__DIR__ . '/cache.sqlite');
$cacheDriver->addCacheProvider($sqlLiteCacheDriver);

$fileCacheDriver = new \Weasty\Similar\Cache\FilesystemCache(__DIR__ . '/cache', '.similar_cache.data');
$cacheDriver->addCacheProvider($fileCacheDriver);

$cacheDriver->setNamespace('__SIMILAR__');

$similar = new \Weasty\Similar\Similar();
$similar->setCache($cacheDriver);

$query = $argv[1];
echo json_encode($similar->search($query));